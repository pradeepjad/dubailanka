<?php

namespace Tests\Feature\Seller;

use App\Actions\Seller\SaveStoreDraft;
use App\Models\BusinessEntity;
use App\Models\BusinessEntityUser;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creator_becomes_primary_owner(): void
    {
        $user = User::factory()->create();
        $business = BusinessEntity::create(['type'=>'personal_business','legal_name'=>'Kandy Fashion','country_code'=>'LK','phone'=>'+94771234567','email'=>'seller@example.com','status'=>'active']);
        BusinessEntityUser::create(['business_entity_id'=>$business->id,'user_id'=>$user->id,'role'=>'owner','is_primary_owner'=>true]);

        $store = app(SaveStoreDraft::class)->execute($user, ['business_entity_id'=>$business->id,'name'=>'Kandy Fashion','slug'=>'kandy-fashion','country_code'=>'LK','phone'=>'+94771234567','email'=>'store@example.com']);

        $this->assertDatabaseHas('store_users', ['store_id'=>$store->id,'user_id'=>$user->id,'role'=>'owner','is_primary_owner'=>true]);
    }

    public function test_user_cannot_edit_store_without_store_membership(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $business = BusinessEntity::create(['type'=>'personal_business','legal_name'=>'Owner Business','country_code'=>'AE','phone'=>'+971501234567','email'=>'owner@example.com','status'=>'active']);
        BusinessEntityUser::create(['business_entity_id'=>$business->id,'user_id'=>$owner->id,'role'=>'owner','is_primary_owner'=>true]);
        BusinessEntityUser::create(['business_entity_id'=>$business->id,'user_id'=>$other->id,'role'=>'staff','is_primary_owner'=>false]);
        $store = Store::create(['business_entity_id'=>$business->id,'name'=>'Private Store','slug'=>'private-store','country_code'=>'AE','phone'=>'+971501234567','email'=>'store@example.com','status'=>'draft']);
        $store->memberships()->create(['user_id'=>$owner->id,'role'=>'owner','is_primary_owner'=>true]);

        $this->actingAs($other)->get("/seller/stores/{$store->id}/edit")->assertForbidden();
    }

    public function test_seller_dashboard_only_lists_accessible_stores(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $business = BusinessEntity::create(['type'=>'personal_business','legal_name'=>'Business','country_code'=>'AE','phone'=>'+971501234567','email'=>'business@example.com','status'=>'active']);
        $one = Store::create(['business_entity_id'=>$business->id,'name'=>'My Store','slug'=>'my-store','country_code'=>'AE','phone'=>'+971501234567','email'=>'one@example.com','status'=>'draft']);
        $two = Store::create(['business_entity_id'=>$business->id,'name'=>'Other Store','slug'=>'other-store','country_code'=>'AE','phone'=>'+971501234567','email'=>'two@example.com','status'=>'draft']);
        $one->memberships()->create(['user_id'=>$user->id,'role'=>'owner','is_primary_owner'=>true]);
        $two->memberships()->create(['user_id'=>$other->id,'role'=>'owner','is_primary_owner'=>true]);

        $this->actingAs($user)->get('/seller/dashboard')->assertOk()->assertInertia(fn ($page) => $page->component('Seller/Dashboard')->has('stores', 1)->where('stores.0.name', 'My Store'));
    }
}
