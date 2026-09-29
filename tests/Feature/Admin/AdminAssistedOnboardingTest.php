<?php
namespace Tests\Feature\Admin;
use App\Mail\SellerOnboardingInvitationMail;
use App\Models\BusinessEntity;
use App\Models\PlatformAdmin;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
class AdminAssistedOnboardingTest extends TestCase
{
    use RefreshDatabase;
    private function admin(): User { $u=User::factory()->create(); PlatformAdmin::create(['user_id'=>$u->id,'status'=>'active']); return $u; }
    private function payload(array $overrides=[]): array { return array_merge(['ownership_mode'=>'unassigned','owner_name'=>null,'owner_email'=>null,'business_type'=>'personal_business','legal_name'=>'Lanka Crafts','trading_name'=>null,'registration_number'=>null,'business_country_code'=>'LK','business_address'=>null,'business_phone'=>'+94770000000','business_whatsapp'=>null,'business_email'=>null,'store_name'=>'Lanka Crafts Dubai','store_slug'=>'lanka-crafts-dubai','store_country_code'=>'AE','store_city'=>'Dubai','store_address'=>null,'store_phone'=>'+971500000000','store_whatsapp'=>null,'store_email'=>null,'store_website'=>null,'store_description'=>'Admin created store','store_business_hours'=>null],$overrides); }
    public function test_admin_can_create_unassigned_admin_managed_store(): void
    {
        $admin=$this->admin(); $this->actingAs($admin)->post('/admin/onboarding',$this->payload())->assertRedirect('/admin/onboarding');
        $store=Store::firstOrFail(); $this->assertSame(Store::STATUS_DRAFT,$store->status); $this->assertFalse($store->memberships()->exists()); $this->assertFalse($store->businessEntity->memberships()->exists()); $this->assertNull($store->email); $this->assertNull($store->businessEntity->email);
    }
    public function test_admin_can_assign_existing_account_as_first_primary_owner(): void
    {
        $admin=$this->admin(); $owner=User::factory()->create(['email'=>'owner@example.com']);
        $this->actingAs($admin)->post('/admin/onboarding',$this->payload(['ownership_mode'=>'existing','owner_email'=>$owner->email]))->assertRedirect('/admin/onboarding');
        $store=Store::firstOrFail(); $this->assertDatabaseHas('store_users',['store_id'=>$store->id,'user_id'=>$owner->id,'role'=>'owner','is_primary_owner'=>1]); $this->assertDatabaseHas('business_entity_users',['business_entity_id'=>$store->business_entity_id,'user_id'=>$owner->id,'is_primary_owner'=>1]); $this->assertDatabaseHas('ownership_assignments',['store_id'=>$store->id,'user_id'=>$owner->id,'assigned_by_user_id'=>$admin->id]);
    }
    public function test_admin_can_invite_passwordless_owner_and_keep_ownership_for_registration(): void
    {
        Mail::fake(); $admin=$this->admin();
        $this->actingAs($admin)->post('/admin/onboarding',$this->payload(['ownership_mode'=>'invite','owner_name'=>'Nimal Perera','owner_email'=>'nimal@example.com']))->assertRedirect('/admin/onboarding');
        $owner=User::where('email','nimal@example.com')->firstOrFail(); $this->assertFalse($owner->hasPassword()); $this->assertDatabaseHas('store_users',['user_id'=>$owner->id,'is_primary_owner'=>1]); Mail::assertSent(SellerOnboardingInvitationMail::class,fn($mail)=>$mail->hasTo('nimal@example.com'));
    }
    public function test_unassigned_approved_store_can_receive_first_owner_without_changing_status(): void
    {
        Mail::fake(); $admin=$this->admin(); $business=BusinessEntity::create(['type'=>'personal_business','legal_name'=>'No Email Business','country_code'=>'LK','phone'=>'0770000000','email'=>null,'status'=>'active']); $store=Store::create(['business_entity_id'=>$business->id,'name'=>'Admin Managed','slug'=>'admin-managed','country_code'=>'AE','phone'=>'0500000000','email'=>null,'status'=>Store::STATUS_APPROVED]);
        $this->actingAs($admin)->post("/admin/onboarding/stores/{$store->id}/assign-owner",['owner_name'=>'Owner One','owner_email'=>'owner1@example.com'])->assertRedirect();
        $this->assertSame(Store::STATUS_APPROVED,$store->fresh()->status); $this->assertDatabaseHas('store_users',['store_id'=>$store->id,'is_primary_owner'=>1]);
    }
    public function test_admin_can_submit_unassigned_admin_managed_draft_for_review(): void
    {
        $admin=$this->admin();
        $this->actingAs($admin)->post('/admin/onboarding',$this->payload())->assertRedirect('/admin/onboarding');
        $store=Store::firstOrFail();

        $this->actingAs($admin)->post("/admin/onboarding/stores/{$store->id}/submit")->assertRedirect();

        $this->assertSame(Store::STATUS_PENDING,$store->fresh()->status);
        $this->assertFalse($store->memberships()->exists());
        $this->assertDatabaseHas('store_status_histories',['store_id'=>$store->id,'from_status'=>Store::STATUS_DRAFT,'to_status'=>Store::STATUS_PENDING,'actor_user_id'=>$admin->id]);
    }

    public function test_admin_can_edit_and_resubmit_unassigned_store_after_needs_changes(): void
    {
        $admin=$this->admin();
        $this->actingAs($admin)->post('/admin/onboarding',$this->payload())->assertRedirect('/admin/onboarding');
        $store=Store::firstOrFail();
        $store->update(['status'=>Store::STATUS_NEEDS_CHANGES]);
        $store->statusHistories()->create(['actor_user_id'=>$admin->id,'from_status'=>Store::STATUS_UNDER_REVIEW,'to_status'=>Store::STATUS_NEEDS_CHANGES,'note'=>'Please add the complete address.']);

        $this->actingAs($admin)->put("/admin/onboarding/stores/{$store->id}",[
            'name'=>$store->name,'slug'=>$store->slug,'country_code'=>$store->country_code,'city'=>'Dubai','address'=>'Al Quoz, Dubai','phone'=>$store->phone,'whatsapp'=>null,'email'=>null,'website'=>null,'description'=>'Updated description','business_hours'=>null,
        ])->assertRedirect("/admin/onboarding/stores/{$store->id}/edit");

        $this->assertSame('Al Quoz, Dubai',$store->fresh()->address);
        $this->assertNotNull($store->fresh()->review_changes_saved_at);
        $this->actingAs($admin)->post("/admin/onboarding/stores/{$store->id}/submit")->assertRedirect();
        $this->assertSame(Store::STATUS_PENDING,$store->fresh()->status);
        $this->assertDatabaseHas('store_status_histories',['store_id'=>$store->id,'from_status'=>Store::STATUS_NEEDS_CHANGES,'to_status'=>Store::STATUS_PENDING]);
    }

    public function test_needs_changes_store_cannot_be_resubmitted_until_a_real_edit_is_saved(): void
    {
        $admin=$this->admin();
        $this->actingAs($admin)->post('/admin/onboarding',$this->payload())->assertRedirect('/admin/onboarding');
        $store=Store::firstOrFail();
        $store->update(['status'=>Store::STATUS_NEEDS_CHANGES]);
        $store->statusHistories()->create(['actor_user_id'=>$admin->id,'from_status'=>Store::STATUS_UNDER_REVIEW,'to_status'=>Store::STATUS_NEEDS_CHANGES,'note'=>'Please add the complete address.']);

        $this->actingAs($admin)->post("/admin/onboarding/stores/{$store->id}/submit")->assertStatus(422);
        $this->assertSame(Store::STATUS_NEEDS_CHANGES,$store->fresh()->status);
    }

    public function test_assign_owner_existing_account_does_not_require_owner_name(): void
    {
        $admin=$this->admin(); $owner=User::factory()->create(['name'=>'Existing Seller','email'=>'existing-owner@example.com']);
        $business=BusinessEntity::create(['type'=>'personal_business','legal_name'=>'Admin Business','country_code'=>'LK','phone'=>'0770000000','email'=>null,'status'=>'active']);
        $store=Store::create(['business_entity_id'=>$business->id,'name'=>'Admin Store','slug'=>'admin-store','country_code'=>'AE','phone'=>'0500000000','email'=>null,'status'=>Store::STATUS_APPROVED]);

        $this->actingAs($admin)->post("/admin/onboarding/stores/{$store->id}/assign-owner",['owner_email'=>$owner->email])->assertRedirect();
        $this->assertDatabaseHas('store_users',['store_id'=>$store->id,'user_id'=>$owner->id,'is_primary_owner'=>1]);
        $this->assertSame('Existing Seller',$owner->fresh()->name);
    }


    public function test_admin_can_continue_needs_changes_editing_after_first_owner_is_assigned(): void
    {
        $admin=$this->admin();
        $this->actingAs($admin)->post('/admin/onboarding',$this->payload())->assertRedirect('/admin/onboarding');
        $store=Store::firstOrFail();
        $store->update(['status'=>Store::STATUS_NEEDS_CHANGES]);
        $store->statusHistories()->create(['actor_user_id'=>$admin->id,'from_status'=>Store::STATUS_UNDER_REVIEW,'to_status'=>Store::STATUS_NEEDS_CHANGES,'note'=>'Add address.']);

        $this->actingAs($admin)->post("/admin/onboarding/stores/{$store->id}/assign-owner",['owner_name'=>'Sameera','owner_email'=>'sameera@example.com'])->assertRedirect();

        $this->actingAs($admin)->put("/admin/onboarding/stores/{$store->id}",[
            'name'=>$store->name,'slug'=>$store->slug,'country_code'=>$store->country_code,'city'=>'Dubai','address'=>'Al Quoz','phone'=>$store->phone,'whatsapp'=>null,'email'=>null,'website'=>null,'description'=>'Updated','business_hours'=>null,
        ])->assertRedirect("/admin/onboarding/stores/{$store->id}/edit");

        $this->assertNotNull($store->fresh()->review_changes_saved_at);
        $this->actingAs($admin)->post("/admin/onboarding/stores/{$store->id}/submit")->assertRedirect();
        $this->assertSame(Store::STATUS_PENDING,$store->fresh()->status);
    }


    public function test_new_needs_changes_cycle_resets_previous_resubmit_eligibility(): void
    {
        $admin=$this->admin();
        $business=BusinessEntity::create([
            'type'=>'personal_business',
            'legal_name'=>'Repeat Review Business',
            'country_code'=>'LK',
            'phone'=>'0770000000',
            'email'=>null,
            'status'=>'active',
        ]);
        $store=Store::create([
            'business_entity_id'=>$business->id,
            'name'=>'Repeat Review Store',
            'slug'=>'repeat-review-store',
            'country_code'=>'AE',
            'phone'=>'0500000000',
            'status'=>Store::STATUS_UNDER_REVIEW,
        ]);

        $store->review_changes_saved_at=now();
        $store->save();
        $this->assertNotNull($store->fresh()->review_changes_saved_at);

        app(\App\Actions\Store\ChangeStoreStatus::class)->execute(
            $store,
            Store::STATUS_NEEDS_CHANGES,
            $admin,
            'Please add the address.'
        );

        $store->refresh();
        $this->assertSame(Store::STATUS_NEEDS_CHANGES,$store->status);
        $this->assertNull($store->review_changes_saved_at);
        $this->assertDatabaseHas('store_status_histories',[
            'store_id'=>$store->id,
            'from_status'=>Store::STATUS_UNDER_REVIEW,
            'to_status'=>Store::STATUS_NEEDS_CHANGES,
            'actor_user_id'=>$admin->id,
            'note'=>'Please add the address.',
        ]);
    }


    public function test_existing_account_cannot_be_handed_over_before_business_exists(): void
    {
        Mail::fake(); $admin=$this->admin(); $owner=User::factory()->create(['email'=>'existing@example.com']);
        $payload=$this->payload(['ownership_mode'=>'existing','owner_email'=>$owner->email,'submit_intent'=>'handover']);
        $this->actingAs($admin)->post('/admin/onboarding',$payload)->assertSessionHasErrors('ownership_mode');
        $this->assertDatabaseCount('business_entities',0); $this->assertDatabaseCount('stores',0);
        $this->assertDatabaseMissing('seller_onboarding_handovers',['owner_email'=>$owner->email,'status'=>'pending']);
    }

    public function test_admin_can_lookup_existing_account_before_continuing_setup(): void
    {
        $admin=$this->admin(); $owner=User::factory()->create(['name'=>'Lookup Seller','email'=>'lookup@example.com']);
        $this->actingAs($admin)->getJson('/admin/onboarding/owner-lookup?email=lookup@example.com')
            ->assertOk()->assertJson(['exists'=>true,'available'=>true,'email'=>'lookup@example.com','user'=>['name'=>'Lookup Seller','email'=>'lookup@example.com']]);
        $this->actingAs($admin)->getJson('/admin/onboarding/owner-lookup?email=missing@example.com')
            ->assertOk()->assertJson(['exists'=>false,'email'=>'missing@example.com']);
    }

    public function test_admin_can_stop_after_business_and_add_store_later(): void
    {
        $admin=$this->admin(); $owner=User::factory()->create(['email'=>'business-only@example.com']);
        $payload=$this->payload(['ownership_mode'=>'existing','owner_email'=>$owner->email,'submit_intent'=>'business_only']);
        $this->actingAs($admin)->post('/admin/onboarding',$payload)->assertRedirect('/admin/onboarding');
        $business=BusinessEntity::firstOrFail(); $this->assertDatabaseCount('stores',0);
        $this->assertDatabaseHas('business_entity_users',['business_entity_id'=>$business->id,'user_id'=>$owner->id,'is_primary_owner'=>1]);
        $this->actingAs($admin)->post("/admin/onboarding/businesses/{$business->id}/stores",[
            'store_name'=>'Later Store','store_slug'=>'later-store','store_country_code'=>'AE','store_phone'=>'+971500000001',
        ])->assertRedirect('/admin/onboarding');
        $store=Store::firstOrFail();
        $this->assertDatabaseHas('store_users',['store_id'=>$store->id,'user_id'=>$owner->id,'is_primary_owner'=>1]);
    }

    public function test_first_owner_assignment_applies_to_all_existing_business_stores(): void
    {
        $admin=$this->admin(); $owner=User::factory()->create(['email'=>'allstores@example.com']);
        $business=BusinessEntity::create(['type'=>'personal_business','legal_name'=>'Multi Store Business','country_code'=>'AE','phone'=>'0500000000','status'=>'active']);
        $one=Store::create(['business_entity_id'=>$business->id,'name'=>'One','slug'=>'one-store','country_code'=>'AE','phone'=>'0500000001','status'=>Store::STATUS_DRAFT]);
        $two=Store::create(['business_entity_id'=>$business->id,'name'=>'Two','slug'=>'two-store','country_code'=>'AE','phone'=>'0500000002','status'=>Store::STATUS_APPROVED]);
        $this->actingAs($admin)->post("/admin/onboarding/businesses/{$business->id}/assign-owner",['owner_email'=>$owner->email])->assertRedirect();
        $this->assertDatabaseHas('store_users',['store_id'=>$one->id,'user_id'=>$owner->id,'is_primary_owner'=>1]);
        $this->assertDatabaseHas('store_users',['store_id'=>$two->id,'user_id'=>$owner->id,'is_primary_owner'=>1]);
        $this->assertSame(Store::STATUS_APPROVED,$two->fresh()->status);
    }

}
