<?php

namespace Tests\Feature\Seller;

use App\Models\BusinessEntity;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerDashboardResubmitTest extends TestCase
{
    use RefreshDatabase;

    public function test_needs_changes_store_only_becomes_resubmittable_after_a_saved_edit(): void
    {
        $user = User::factory()->create();
        $business = BusinessEntity::create([
            'type' => 'personal_business',
            'legal_name' => 'Twenty Steps',
            'country_code' => 'AE',
            'phone' => '0550000000',
            'email' => $user->email,
            'status' => 'active',
        ]);
        $store = Store::create([
            'business_entity_id' => $business->id,
            'name' => 'Twenty Steps',
            'slug' => 'twenty-steps',
            'country_code' => 'AE',
            'phone' => '0550000000',
            'status' => Store::STATUS_NEEDS_CHANGES,
            'review_changes_saved_at' => null,
        ]);
        $store->memberships()->create(['user_id' => $user->id, 'role' => 'owner', 'is_primary_owner' => true]);

        $this->actingAs($user)->get('/seller/dashboard')
            ->assertInertia(fn($page) => $page
                ->where('stores.0.status', Store::STATUS_NEEDS_CHANGES)
                ->where('stores.0.can_resubmit', false));

        $store->review_changes_saved_at = now();
        $store->save();

        $this->actingAs($user)->get('/seller/dashboard')
            ->assertInertia(fn($page) => $page
                ->where('stores.0.can_resubmit', true));
    }
}
