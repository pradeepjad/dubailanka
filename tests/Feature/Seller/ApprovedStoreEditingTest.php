<?php

namespace Tests\Feature\Seller;

use App\Models\BusinessEntity;
use App\Models\PlatformAdmin;
use App\Models\Store;
use App\Models\StoreChangeRequest;
use App\Models\StoreUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovedStoreEditingTest extends TestCase
{
    use RefreshDatabase;

    private function approvedStore(User $user): Store
    {
        $business = BusinessEntity::create([
            'type' => 'personal_business', 'legal_name' => 'Test Business', 'country_code' => 'AE',
            'phone' => '1', 'email' => 'business@example.com', 'status' => 'active',
        ]);
        $business->users()->attach($user->id, ['role' => 'owner', 'is_primary_owner' => true]);

        $store = Store::create([
            'business_entity_id' => $business->id, 'name' => 'Kandy Fashion', 'slug' => 'kandy-fashion',
            'country_code' => 'AE', 'city' => 'Dubai', 'address' => 'Old Address',
            'phone' => '+971501234567', 'email' => 'old@example.com', 'description' => 'Old description',
            'status' => Store::STATUS_APPROVED,
        ]);
        StoreUser::create(['store_id' => $store->id, 'user_id' => $user->id, 'role' => 'owner', 'is_primary_owner' => true]);

        return $store;
    }

    public function test_direct_fields_update_but_identity_changes_remain_pending(): void
    {
        $user = User::factory()->create();
        $store = $this->approvedStore($user);

        $this->actingAs($user)->put("/seller/stores/{$store->id}", [
            'business_entity_id' => $store->business_entity_id,
            'name' => 'Kandy Fashion Sri Lanka',
            'slug' => 'kandy-fashion-sri-lanka',
            'country_code' => 'LK',
            'city' => 'Kandy',
            'address' => null,
            'phone' => '+94771234567',
            'email' => 'new@example.com',
            'description' => 'New description',
        ])->assertRedirect();

        $this->assertDatabaseHas('stores', [
            'id' => $store->id, 'name' => 'Kandy Fashion', 'slug' => 'kandy-fashion',
            'country_code' => 'AE', 'phone' => '+94771234567', 'email' => 'new@example.com',
            'description' => 'New description',
        ]);
        $this->assertDatabaseHas('store_change_requests', [
            'store_id' => $store->id, 'status' => 'pending',
            'proposed_name' => 'Kandy Fashion Sri Lanka', 'proposed_slug' => 'kandy-fashion-sri-lanka',
            'proposed_country_code' => 'LK', 'proposed_city' => 'Kandy', 'proposed_address' => null,
        ]);
    }

    public function test_admin_approval_applies_identity_changes_and_preserves_old_slug(): void
    {
        $seller = User::factory()->create();
        $admin = User::factory()->create();
        PlatformAdmin::create(['user_id' => $admin->id, 'is_super_admin' => true, 'status' => 'active']);
        $store = $this->approvedStore($seller);

        StoreChangeRequest::create([
            'store_id' => $store->id, 'requested_by_user_id' => $seller->id, 'status' => 'pending',
            'proposed_name' => 'New Name', 'proposed_slug' => 'new-name',
            'proposed_country_code' => $store->country_code,
            'proposed_city' => $store->city,
            'proposed_address' => $store->address,
        ]);

        $this->actingAs($admin)->post("/admin/stores/{$store->id}/changes/start-review")->assertRedirect();
        $this->actingAs($admin)->post("/admin/stores/{$store->id}/changes/approve")->assertRedirect();

        $this->assertDatabaseHas('stores', ['id' => $store->id, 'name' => 'New Name', 'slug' => 'new-name', 'status' => 'approved']);
        $this->assertDatabaseHas('store_slug_histories', ['store_id' => $store->id, 'slug' => 'kandy-fashion']);
        $this->assertDatabaseHas('store_change_requests', ['store_id' => $store->id, 'status' => 'approved']);
    }

    public function test_rejected_identity_change_does_not_change_approved_store(): void
    {
        $seller = User::factory()->create();
        $admin = User::factory()->create();
        PlatformAdmin::create(['user_id' => $admin->id, 'is_super_admin' => true, 'status' => 'active']);
        $store = $this->approvedStore($seller);

        StoreChangeRequest::create([
            'store_id' => $store->id, 'requested_by_user_id' => $seller->id, 'status' => 'pending',
            'proposed_name' => 'Rejected Name',
        ]);

        $this->actingAs($admin)->post("/admin/stores/{$store->id}/changes/start-review")->assertRedirect();
        $this->actingAs($admin)->post("/admin/stores/{$store->id}/changes/reject", ['note' => 'Keep the approved identity.'])->assertRedirect();

        $this->assertDatabaseHas('stores', ['id' => $store->id, 'name' => 'Kandy Fashion', 'status' => 'approved']);
        $this->assertDatabaseHas('store_change_requests', ['store_id' => $store->id, 'status' => 'rejected', 'admin_note' => 'Keep the approved identity.']);
    }
}
