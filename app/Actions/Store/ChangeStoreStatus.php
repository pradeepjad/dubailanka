<?php

namespace App\Actions\Store;

use App\Models\Store;
use App\Models\User;
use App\Services\StoreWorkflowNotificationService;
use Illuminate\Support\Facades\DB;

class ChangeStoreStatus
{
    public function __construct(private readonly StoreWorkflowNotificationService $notifications) {}

    public function execute(Store $store, string $to, User $actor, ?string $note = null): Store
    {
        $from = $store->status;

        $updated = DB::transaction(function () use ($store, $to, $actor, $note, $from) {
            $store->forceFill([
                'status' => $to,
                'review_changes_saved_at' => $to === Store::STATUS_NEEDS_CHANGES
                    ? null
                    : $store->review_changes_saved_at,
            ])->save();

            $store->statusHistories()->create([
                'actor_user_id' => $actor->id,
                'from_status' => $from,
                'to_status' => $to,
                'note' => $note,
            ]);

            return $store->fresh();
        });

        if ($to === Store::STATUS_PENDING && in_array($from, [Store::STATUS_DRAFT, Store::STATUS_NEEDS_CHANGES], true)) {
            $this->notifications->storeSubmitted($updated, $from === Store::STATUS_NEEDS_CHANGES);
        }

        $this->notifications->sellerStatusChanged($updated, $to, $note);

        return $updated;
    }
}
