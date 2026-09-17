<?php

namespace Tests\Feature\Seller;

use App\Models\BusinessEntity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessEntityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_seller_onboarding(): void
    {
        $this->get('/seller/start')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_personal_business_as_primary_owner(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/seller/businesses', [
            'type' => BusinessEntity::TYPE_PERSONAL,
            'legal_name' => 'Pradeep Trading',
            'country_code' => 'AE',
            'phone' => '+971501234567',
            'whatsapp' => '+971501234567',
            'email' => 'sales@example.com',
        ]);

        $response->assertRedirect(route('seller.start'));

        $business = BusinessEntity::firstOrFail();

        $this->assertSame('Pradeep Trading', $business->legal_name);
        $this->assertDatabaseHas('business_entity_users', [
            'business_entity_id' => $business->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'is_primary_owner' => 1,
        ]);
    }

    public function test_registered_company_requires_registration_number_and_address(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/seller/businesses', [
            'type' => BusinessEntity::TYPE_REGISTERED,
            'legal_name' => 'Example LLC',
            'country_code' => 'AE',
            'phone' => '+971501234567',
            'email' => 'hello@example.com',
        ]);

        $response->assertSessionHasErrors(['registration_number', 'address']);
        $this->assertDatabaseCount('business_entities', 0);
    }

    public function test_one_user_can_own_multiple_business_entities(): void
    {
        $user = User::factory()->create();

        foreach (['First Business', 'Second Business'] as $name) {
            $this->actingAs($user)->post('/seller/businesses', [
                'type' => BusinessEntity::TYPE_PERSONAL,
                'legal_name' => $name,
                'country_code' => 'LK',
                'phone' => '+94771234567',
                'email' => strtolower(str_replace(' ', '', $name)).'@example.com',
            ])->assertRedirect(route('seller.start'));
        }

        $this->assertCount(2, $user->fresh()->businessEntities);
    }
}
