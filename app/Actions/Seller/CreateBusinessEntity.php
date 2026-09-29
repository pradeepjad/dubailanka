<?php

namespace App\Actions\Seller;

use App\Models\BusinessEntity;
use App\Models\User;
use App\Models\SellerOnboardingHandover;
use Illuminate\Support\Facades\DB;

class CreateBusinessEntity
{
    public function execute(User $user, array $data): BusinessEntity
    {
        return DB::transaction(function () use ($user, $data) {
            $business = BusinessEntity::create([
                ...$data,
                'status' => 'active',
            ]);

            $business->memberships()->create([
                'user_id' => $user->id,
                'role' => 'owner',
                'is_primary_owner' => true,
            ]);

            SellerOnboardingHandover::query()
                ->where('status', SellerOnboardingHandover::STATUS_PENDING)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)->orWhere('owner_email', $user->email);
                })
                ->oldest()
                ->first()?->update([
                    'business_entity_id' => $business->id,
                    'status' => SellerOnboardingHandover::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);

            return $business;
        });
    }
}
