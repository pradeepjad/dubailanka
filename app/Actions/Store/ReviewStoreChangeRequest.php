<?php

namespace App\Actions\Store;

use App\Models\StoreChangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewStoreChangeRequest
{
    public function start(StoreChangeRequest $change, User $admin): void
    {
        abort_unless($change->status === StoreChangeRequest::STATUS_PENDING, 422);

        $change->update([
            'status' => StoreChangeRequest::STATUS_UNDER_REVIEW,
            'reviewed_by_user_id' => $admin->id,
            'review_started_at' => now(),
            'admin_note' => null,
        ]);
    }

    public function approve(StoreChangeRequest $change, User $admin, ?string $note = null): void
    {
        abort_unless($change->status === StoreChangeRequest::STATUS_UNDER_REVIEW, 422);

        DB::transaction(function () use ($change, $admin, $note) {
            $store = $change->store()->lockForUpdate()->firstOrFail();

            if ($change->proposed_slug && $change->proposed_slug !== $store->slug) {
                $slugTaken = \App\Models\Store::where('slug', $change->proposed_slug)->whereKeyNot($store->id)->exists()
                    || \App\Models\StoreSlugHistory::where('slug', $change->proposed_slug)->exists();

                if ($slugTaken) {
                    throw ValidationException::withMessages([
                        'slug' => 'The proposed store address is no longer available. Return it to the seller to choose another one.',
                    ]);
                }

                \App\Models\StoreSlugHistory::firstOrCreate(
                    ['slug' => $store->slug],
                    ['store_id' => $store->id, 'changed_by_user_id' => $admin->id, 'changed_at' => now()]
                );
            }

            $store->update([
                'name' => $change->proposed_name,
                'slug' => $change->proposed_slug,
                'country_code' => $change->proposed_country_code,
                'city' => $change->proposed_city,
                'address' => $change->proposed_address,
            ]);

            $change->update([
                'status' => StoreChangeRequest::STATUS_APPROVED,
                'reviewed_by_user_id' => $admin->id,
                'admin_note' => $note,
                'resolved_at' => now(),
            ]);
        });
    }

    public function needsChanges(StoreChangeRequest $change, User $admin, string $note): void
    {
        abort_unless($change->status === StoreChangeRequest::STATUS_UNDER_REVIEW, 422);
        $change->update([
            'status' => StoreChangeRequest::STATUS_NEEDS_CHANGES,
            'reviewed_by_user_id' => $admin->id,
            'admin_note' => $note,
            'resolved_at' => null,
        ]);
    }

    public function reject(StoreChangeRequest $change, User $admin, string $note): void
    {
        abort_unless($change->status === StoreChangeRequest::STATUS_UNDER_REVIEW, 422);
        $change->update([
            'status' => StoreChangeRequest::STATUS_REJECTED,
            'reviewed_by_user_id' => $admin->id,
            'admin_note' => $note,
            'resolved_at' => now(),
        ]);
    }
}
