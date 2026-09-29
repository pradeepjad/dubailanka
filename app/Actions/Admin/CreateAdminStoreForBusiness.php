<?php

namespace App\Actions\Admin;

use App\Models\BusinessEntity;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class CreateAdminStoreForBusiness
{
    public function execute(BusinessEntity $business, array $data): Store
    {
        return DB::transaction(function () use ($business, $data) {
            $store = Store::create([
                'business_entity_id' => $business->id,
                'name' => $data['store_name'], 'slug' => $data['store_slug'],
                'description' => $data['store_description'] ?? null,
                'country_code' => $data['store_country_code'], 'city' => $data['store_city'] ?? null,
                'address' => $data['store_address'] ?? null, 'phone' => $data['store_phone'],
                'whatsapp' => $data['store_whatsapp'] ?? null, 'email' => $data['store_email'] ?? null,
                'website' => $data['store_website'] ?? null, 'business_hours' => $data['store_business_hours'] ?? null,
                'status' => Store::STATUS_DRAFT,
            ]);

            $owner = $business->memberships()->where('is_primary_owner', true)->first();
            if ($owner) {
                $store->memberships()->updateOrCreate(
                    ['user_id' => $owner->user_id],
                    ['role' => 'owner', 'is_primary_owner' => true]
                );
            }

            return $store;
        });
    }
}
