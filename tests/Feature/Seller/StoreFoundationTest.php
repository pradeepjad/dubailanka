<?php

namespace Tests\Feature\Seller;

use App\Models\BusinessEntity;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreFoundationTest extends TestCase
{
    use RefreshDatabase;

    private function businessFor(User $user, string $name = 'Example Business'): BusinessEntity
    {
        $business = BusinessEntity::create(['type'=>BusinessEntity::TYPE_PERSONAL,'legal_name'=>$name,'country_code'=>'AE','phone'=>'+971501234567','email'=>'business@example.com','status'=>'active']);
        $business->memberships()->create(['user_id'=>$user->id,'role'=>'owner','is_primary_owner'=>true]);
        return $business;
    }

    public function test_user_can_save_store_draft_under_owned_business(): void
    {
        $user=User::factory()->create(); $business=$this->businessFor($user);
        $response=$this->actingAs($user)->post('/seller/stores',['business_entity_id'=>$business->id,'name'=>'Ceylon Gifts','slug'=>'ceylon-gifts','country_code'=>'AE','phone'=>'+971501234567','email'=>'store@example.com']);
        $store=Store::firstOrFail();
        $response->assertRedirect(route('seller.stores.edit',$store));
        $this->assertSame(Store::STATUS_DRAFT,$store->status);
        $this->assertDatabaseHas('stores',['business_entity_id'=>$business->id,'slug'=>'ceylon-gifts','status'=>'draft']);
    }

    public function test_store_slug_must_be_unique(): void
    {
        $user=User::factory()->create(); $business=$this->businessFor($user);
        Store::create(['business_entity_id'=>$business->id,'name'=>'First','slug'=>'ceylon-gifts','country_code'=>'AE','phone'=>'1','email'=>'first@example.com','status'=>'draft']);
        $this->actingAs($user)->post('/seller/stores',['business_entity_id'=>$business->id,'name'=>'Second','slug'=>'ceylon-gifts','country_code'=>'AE','phone'=>'2','email'=>'second@example.com'])->assertSessionHasErrors('slug');
        $this->assertDatabaseCount('stores',1);
    }

    public function test_user_cannot_create_store_under_another_users_business(): void
    {
        $owner=User::factory()->create(); $other=User::factory()->create(); $business=$this->businessFor($owner);
        $this->actingAs($other)->post('/seller/stores',['business_entity_id'=>$business->id,'name'=>'Blocked','slug'=>'blocked-store','country_code'=>'AE','phone'=>'1','email'=>'blocked@example.com'])->assertForbidden();
        $this->assertDatabaseCount('stores',0);
    }

    public function test_owner_can_update_existing_store_draft(): void
    {
        $user=User::factory()->create(); $business=$this->businessFor($user);
        $store=Store::create(['business_entity_id'=>$business->id,'name'=>'Old Name','slug'=>'old-name','country_code'=>'AE','phone'=>'1','email'=>'old@example.com','status'=>'draft']);
        $this->actingAs($user)->put("/seller/stores/{$store->id}",['business_entity_id'=>$business->id,'name'=>'New Name','slug'=>'new-name','country_code'=>'LK','phone'=>'+94771234567','email'=>'new@example.com'])->assertRedirect(route('seller.stores.edit',$store));
        $this->assertDatabaseHas('stores',['id'=>$store->id,'name'=>'New Name','slug'=>'new-name','country_code'=>'LK']);
    }
}
