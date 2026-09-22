<?php

namespace App\Actions\Seller;

use App\Models\Store;
use App\Models\StoreChangeRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaveApprovedStoreChanges
{
    public function execute(User $user, Store $store, array $data): Store
    {
        return DB::transaction(function () use ($user, $store, $data) {
            $direct = [
                'description' => $data['description'] ?? null,
                'phone' => $data['phone'],
                'whatsapp' => $data['whatsapp'] ?? null,
                'email' => $data['email'],
                'website' => $data['website'] ?? null,
                'business_hours' => $data['business_hours'] ?? null,
            ];

            if (($data['logo'] ?? null) instanceof UploadedFile) {
                $newPath = $data['logo']->store('stores/logos', 'public');
                if ($store->logo_path) Storage::disk('public')->delete($store->logo_path);
                $direct['logo_path'] = $newPath;
            }

            if (($data['cover'] ?? null) instanceof UploadedFile) {
                $newPath = $data['cover']->store('stores/covers', 'public');
                if ($store->cover_path) Storage::disk('public')->delete($store->cover_path);
                $direct['cover_path'] = $newPath;
            }

            $store->update($direct);

            $hasIdentityChanges =
                $data['name'] !== $store->name
                || $data['slug'] !== $store->slug
                || $data['country_code'] !== $store->country_code
                || ($data['city'] ?? null) !== $store->city
                || ($data['address'] ?? null) !== $store->address;

            // Keep a complete proposed identity snapshot. This intentionally preserves
            // nullable values so a seller can request clearing City or Address.
            $identity = [
                'proposed_name' => $data['name'],
                'proposed_slug' => $data['slug'],
                'proposed_country_code' => $data['country_code'],
                'proposed_city' => $data['city'] ?? null,
                'proposed_address' => $data['address'] ?? null,
            ];

            $request = $store->changeRequests()
                ->whereIn('status', [
                    StoreChangeRequest::STATUS_PENDING,
                    StoreChangeRequest::STATUS_UNDER_REVIEW,
                    StoreChangeRequest::STATUS_NEEDS_CHANGES,
                ])->latest('id')->first();

            if ($request?->status === StoreChangeRequest::STATUS_UNDER_REVIEW) {
                // Direct fields may still be updated while identity changes are under Admin review.
                return $store->fresh();
            }

            if ($hasIdentityChanges) {
                $payload = $identity + [
                    'requested_by_user_id' => $user->id,
                    'status' => StoreChangeRequest::STATUS_PENDING,
                    'admin_note' => null,
                    'reviewed_by_user_id' => null,
                    'review_started_at' => null,
                    'resolved_at' => null,
                ];

                if ($request) $request->update($payload);
                else $store->changeRequests()->create($payload);
            } elseif ($request && $request->status !== StoreChangeRequest::STATUS_UNDER_REVIEW) {
                $request->update([
                    'status' => StoreChangeRequest::STATUS_REJECTED,
                    'admin_note' => 'Seller removed all proposed identity changes.',
                    'resolved_at' => now(),
                ]);
            }

            return $store->fresh();
        });
    }
}
