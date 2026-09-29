<?php

namespace Tests\Feature\Admin;

use App\Actions\Admin\AssignStoreAdmin;
use App\Actions\Admin\AssignFirstStoreOwner;
use App\Actions\Store\ChangeStoreStatus;
use App\Mail\SellerOnboardingInvitationMail;
use App\Mail\StoreOwnerAssignedMail;
use App\Mail\StoreStatusChangedMail;
use App\Mail\StoreSubmittedForReviewMail;
use App\Models\BusinessEntity;
use App\Models\PlatformAdmin;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StoreWorkflowNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function platformAdmin(bool $super = false, string $email = 'admin@example.com'): array
    {
        $user = User::factory()->create(['email' => $email, 'status' => 'active']);
        $admin = PlatformAdmin::create([
            'user_id' => $user->id,
            'is_super_admin' => $super,
            'status' => 'active',
        ]);
        return [$user, $admin];
    }

    private function store(string $status = Store::STATUS_DRAFT, string $slug = 'workflow-store'): Store
    {
        $business = BusinessEntity::create([
            'type' => 'personal_business',
            'legal_name' => 'Workflow Business',
            'country_code' => 'LK',
            'phone' => '0770000000',
            'status' => 'active',
        ]);

        return Store::create([
            'business_entity_id' => $business->id,
            'name' => 'Workflow Store',
            'slug' => $slug,
            'country_code' => 'AE',
            'phone' => '0500000000',
            'status' => $status,
        ]);
    }

    public function test_unassigned_first_submission_notifies_active_super_admins_only(): void
    {
        Mail::fake();
        [$superUser] = $this->platformAdmin(true, 'super@example.com');
        [$normalUser] = $this->platformAdmin(false, 'normal@example.com');
        $seller = User::factory()->create();
        $store = $this->store();

        app(ChangeStoreStatus::class)->execute($store, Store::STATUS_PENDING, $seller);

        Mail::assertSent(StoreSubmittedForReviewMail::class, fn ($mail) => $mail->hasTo($superUser->email));
        Mail::assertNotSent(StoreSubmittedForReviewMail::class, fn ($mail) => $mail->hasTo($normalUser->email));
    }

    public function test_assigned_admin_receives_resubmission_instead_of_other_admins_or_super_admins(): void
    {
        Mail::fake();
        [$superUser, $super] = $this->platformAdmin(true, 'super@example.com');
        [$assignedUser, $assigned] = $this->platformAdmin(false, 'assigned@example.com');
        [$otherUser] = $this->platformAdmin(false, 'other@example.com');
        $seller = User::factory()->create();
        $store = $this->store(Store::STATUS_NEEDS_CHANGES);
        $store->forceFill(['review_changes_saved_at' => now()])->save();

        app(AssignStoreAdmin::class)->execute($store, $assigned, $superUser);
        app(ChangeStoreStatus::class)->execute($store->fresh(), Store::STATUS_PENDING, $seller);

        Mail::assertSent(StoreSubmittedForReviewMail::class, fn ($mail) => $mail->hasTo($assignedUser->email) && $mail->resubmission);
        Mail::assertNotSent(StoreSubmittedForReviewMail::class, fn ($mail) => $mail->hasTo($superUser->email));
        Mail::assertNotSent(StoreSubmittedForReviewMail::class, fn ($mail) => $mail->hasTo($otherUser->email));
    }

    public function test_existing_owner_assignment_sends_assignment_email_without_invitation(): void
    {
        Mail::fake();
        [$superUser] = $this->platformAdmin(true, 'super@example.com');
        $owner = User::factory()->create(['email' => 'owner@example.com']);
        $store = $this->store();

        app(AssignFirstStoreOwner::class)->execute(
            $store->businessEntity,
            $store,
            $owner->name,
            $owner->email,
            $superUser
        );

        Mail::assertSent(StoreOwnerAssignedMail::class, fn ($mail) => $mail->hasTo($owner->email));
        Mail::assertNotSent(SellerOnboardingInvitationMail::class);
    }

    public function test_new_passwordless_owner_keeps_claim_invitation_without_duplicate_assignment_email(): void
    {
        Mail::fake();
        [$superUser] = $this->platformAdmin(true, 'super@example.com');
        $store = $this->store();

        app(AssignFirstStoreOwner::class)->execute(
            $store->businessEntity,
            $store,
            'New Seller',
            'new@example.com',
            $superUser
        );

        Mail::assertSent(SellerOnboardingInvitationMail::class, fn ($mail) => $mail->hasTo('new@example.com'));
        Mail::assertNotSent(StoreOwnerAssignedMail::class);
    }

    public function test_needs_changes_approved_rejected_and_suspended_notify_primary_owner(): void
    {
        Mail::fake();
        [$adminUser] = $this->platformAdmin(true, 'super@example.com');
        $owner = User::factory()->create(['email' => 'owner@example.com']);
        $store = $this->store(Store::STATUS_UNDER_REVIEW);
        $store->memberships()->create(['user_id' => $owner->id, 'role' => 'owner', 'is_primary_owner' => true]);

        app(ChangeStoreStatus::class)->execute($store, Store::STATUS_NEEDS_CHANGES, $adminUser, 'Add the address.');

        Mail::assertSent(StoreStatusChangedMail::class, fn ($mail) =>
            $mail->hasTo($owner->email)
            && $mail->status === Store::STATUS_NEEDS_CHANGES
            && $mail->note === 'Add the address.'
        );

        Mail::fake();
        $store->forceFill(['status' => Store::STATUS_UNDER_REVIEW])->save();
        app(ChangeStoreStatus::class)->execute($store->fresh(), Store::STATUS_APPROVED, $adminUser);
        Mail::assertSent(StoreStatusChangedMail::class, fn ($mail) => $mail->hasTo($owner->email) && $mail->status === Store::STATUS_APPROVED);

        Mail::fake();
        $store->forceFill(['status' => Store::STATUS_UNDER_REVIEW])->save();
        app(ChangeStoreStatus::class)->execute($store->fresh(), Store::STATUS_REJECTED, $adminUser, 'Not eligible.');
        Mail::assertSent(StoreStatusChangedMail::class, fn ($mail) => $mail->hasTo($owner->email) && $mail->status === Store::STATUS_REJECTED);

        Mail::fake();
        $store->forceFill(['status' => Store::STATUS_APPROVED])->save();
        app(ChangeStoreStatus::class)->execute($store->fresh(), Store::STATUS_SUSPENDED, $adminUser, 'Policy issue.');
        Mail::assertSent(StoreStatusChangedMail::class, fn ($mail) => $mail->hasTo($owner->email) && $mail->status === Store::STATUS_SUSPENDED);
    }

    public function test_only_super_admin_can_assign_and_reassign_responsible_store_admin(): void
    {
        [$superUser, $super] = $this->platformAdmin(true, 'super@example.com');
        [$adminUser, $admin] = $this->platformAdmin(false, 'admin@example.com');
        [, $secondAdmin] = $this->platformAdmin(false, 'second@example.com');
        $store = $this->store(Store::STATUS_PENDING);

        $this->actingAs($superUser)->post("/admin/stores/{$store->id}/assign-admin", [
            'platform_admin_id' => $admin->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('store_admin_assignments', [
            'store_id' => $store->id,
            'platform_admin_id' => $admin->id,
        ]);

        $this->actingAs($adminUser)->post("/admin/stores/{$store->id}/assign-admin", [
            'platform_admin_id' => $secondAdmin->id,
        ])->assertForbidden();

        $this->actingAs($superUser)->post("/admin/stores/{$store->id}/assign-admin", [
            'platform_admin_id' => $secondAdmin->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('store_admin_assignment_histories', [
            'store_id' => $store->id,
            'from_platform_admin_id' => $admin->id,
            'to_platform_admin_id' => $secondAdmin->id,
        ]);
    }

    public function test_normal_admin_cannot_moderate_a_store_assigned_to_another_admin(): void
    {
        [$superUser] = $this->platformAdmin(true, 'super@example.com');
        [$assignedUser, $assigned] = $this->platformAdmin(false, 'assigned@example.com');
        [$otherUser] = $this->platformAdmin(false, 'other@example.com');
        $store = $this->store(Store::STATUS_PENDING);

        app(AssignStoreAdmin::class)->execute($store, $assigned, $superUser);

        $this->actingAs($otherUser)->post("/admin/stores/{$store->id}/start-review")->assertForbidden();
        $this->actingAs($assignedUser)->post("/admin/stores/{$store->id}/start-review")->assertRedirect();
        $this->assertSame(Store::STATUS_UNDER_REVIEW, $store->fresh()->status);
    }
    public function test_normal_admin_store_queue_only_contains_stores_assigned_to_them(): void
    {
        [$superUser] = $this->platformAdmin(true, 'super@example.com');
        [$adminUser, $admin] = $this->platformAdmin(false, 'admin@example.com');
        [, $otherAdmin] = $this->platformAdmin(false, 'other@example.com');

        $assignedStore = $this->store(Store::STATUS_PENDING, 'assigned-store');
        $otherStore = $this->store(Store::STATUS_PENDING, 'other-store');
        $unassignedStore = $this->store(Store::STATUS_PENDING, 'unassigned-store');

        app(AssignStoreAdmin::class)->execute($assignedStore, $admin, $superUser);
        app(AssignStoreAdmin::class)->execute($otherStore, $otherAdmin, $superUser);

        $this->actingAs($adminUser)->get('/admin/stores')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Stores/Index')
                ->has('stores', 1)
                ->where('stores.0.id', $assignedStore->id)
            );
    }

    public function test_normal_admin_cannot_open_unassigned_or_another_admin_store_review_page(): void
    {
        [$superUser] = $this->platformAdmin(true, 'super@example.com');
        [$adminUser] = $this->platformAdmin(false, 'admin@example.com');
        [, $otherAdmin] = $this->platformAdmin(false, 'other@example.com');

        $unassignedStore = $this->store(Store::STATUS_PENDING, 'unassigned-review-store');
        $otherStore = $this->store(Store::STATUS_PENDING, 'other-review-store');
        app(AssignStoreAdmin::class)->execute($otherStore, $otherAdmin, $superUser);

        $this->actingAs($adminUser)->get("/admin/stores/{$unassignedStore->id}")->assertForbidden();
        $this->actingAs($adminUser)->get("/admin/stores/{$otherStore->id}")->assertForbidden();
    }

    public function test_super_admin_can_open_unassigned_store_review_page(): void
    {
        [$superUser] = $this->platformAdmin(true, 'super@example.com');
        $store = $this->store(Store::STATUS_PENDING, 'super-review-store');

        $this->actingAs($superUser)->get("/admin/stores/{$store->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Stores/Review')
                ->where('store.id', $store->id)
                ->where('isSuperAdmin', true)
            );
    }

}
