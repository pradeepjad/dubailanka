<?php

namespace App\Services;

use App\Mail\StoreStatusChangedMail;
use App\Mail\StoreSubmittedForReviewMail;
use App\Models\PlatformAdmin;
use App\Models\Store;
use Illuminate\Support\Facades\Mail;

class StoreWorkflowNotificationService
{
    public function storeSubmitted(Store $store, bool $resubmission = false): void
    {
        $store->loadMissing('adminAssignment.platformAdmin.user');

        $assignedAdmin = $store->adminAssignment?->platformAdmin;
        if ($assignedAdmin?->status === 'active' && $assignedAdmin->user?->isActive()) {
            Mail::to($assignedAdmin->user->email)
                ->send(new StoreSubmittedForReviewMail($store, $resubmission));
            return;
        }

        PlatformAdmin::query()
            ->with('user')
            ->where('status', 'active')
            ->where('is_super_admin', true)
            ->get()
            ->filter(fn (PlatformAdmin $admin) => $admin->user?->isActive())
            ->each(fn (PlatformAdmin $admin) => Mail::to($admin->user->email)
                ->send(new StoreSubmittedForReviewMail($store, $resubmission)));
    }

    public function sellerStatusChanged(Store $store, string $status, ?string $note = null): void
    {
        if (!in_array($status, [
            Store::STATUS_NEEDS_CHANGES,
            Store::STATUS_APPROVED,
            Store::STATUS_REJECTED,
            Store::STATUS_SUSPENDED,
        ], true)) {
            return;
        }

        $owner = $store->memberships()
            ->where('is_primary_owner', true)
            ->with('user')
            ->first()?->user;

        if (!$owner?->isActive()) {
            return;
        }

        Mail::to($owner->email)->send(new StoreStatusChangedMail($store, $status, $note));
    }
}
