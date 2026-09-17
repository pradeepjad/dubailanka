<?php

namespace App\Actions\Seller;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaveStoreDraft
{
    public function execute(User $user, array $data, ?Store $store = null): Store
    {
        return DB::transaction(function () use ($user, $data, $store) {
            $isNew = $store === null;
            $store ??= new Store();

            $attributes = Arr::except($data, ['logo', 'cover']);
            $attributes['status'] = Store::STATUS_DRAFT;

            if (($data['logo'] ?? null) instanceof UploadedFile) {
                if ($store->logo_path) Storage::disk('public')->delete($store->logo_path);
                $attributes['logo_path'] = $data['logo']->store('stores/logos', 'public');
            }

            if (($data['cover'] ?? null) instanceof UploadedFile) {
                if ($store->cover_path) Storage::disk('public')->delete($store->cover_path);
                $attributes['cover_path'] = $data['cover']->store('stores/covers', 'public');
            }

            $store->fill($attributes)->save();

            if ($isNew) {
                $store->memberships()->create([
                    'user_id' => $user->id,
                    'role' => 'owner',
                    'is_primary_owner' => true,
                ]);
            }

            return $store->fresh();
        });
    }
}
