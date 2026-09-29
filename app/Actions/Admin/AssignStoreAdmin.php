<?php

namespace App\Actions\Admin;

use App\Models\PlatformAdmin;
use App\Models\Store;
use App\Models\StoreAdminAssignment;
use App\Models\StoreAdminAssignmentHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignStoreAdmin
{
    public function execute(Store $store, PlatformAdmin $platformAdmin, User $actor): StoreAdminAssignment
    {
        if ($actor->platformAdmin?->status !== 'active' || !$actor->platformAdmin?->is_super_admin) {
            abort(403, 'Only a Super Admin can assign or reassign a Store Admin.');
        }

        if ($platformAdmin->status !== 'active') {
            throw ValidationException::withMessages([
                'platform_admin_id' => 'Only an active Platform Admin can be assigned to a Store.',
            ]);
        }

        return DB::transaction(function () use ($store, $platformAdmin, $actor) {
            $current = StoreAdminAssignment::where('store_id', $store->id)->lockForUpdate()->first();

            if ($current?->platform_admin_id === $platformAdmin->id) {
                return $current;
            }

            $fromId = $current?->platform_admin_id;

            $assignment = StoreAdminAssignment::updateOrCreate(
                ['store_id' => $store->id],
                [
                    'platform_admin_id' => $platformAdmin->id,
                    'assigned_by_user_id' => $actor->id,
                ]
            );

            StoreAdminAssignmentHistory::create([
                'store_id' => $store->id,
                'from_platform_admin_id' => $fromId,
                'to_platform_admin_id' => $platformAdmin->id,
                'assigned_by_user_id' => $actor->id,
                'assigned_at' => now(),
            ]);

            return $assignment;
        });
    }
}
