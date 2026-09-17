<?php

namespace App\Actions\Seller;

use App\Models\BusinessEntity;
use App\Models\User;
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

            return $business;
        });
    }
}
