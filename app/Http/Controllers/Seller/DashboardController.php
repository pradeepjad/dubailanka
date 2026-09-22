<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreChangeRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $stores = $request->user()->stores()
            ->with([
                'businessEntity:id,legal_name,trading_name',
                'statusHistories' => fn ($q) => $q->limit(1),
                'changeRequests' => fn ($q) => $q->whereIn('status', [
                    StoreChangeRequest::STATUS_PENDING,
                    StoreChangeRequest::STATUS_UNDER_REVIEW,
                    StoreChangeRequest::STATUS_NEEDS_CHANGES,
                ])->limit(1),
            ])
            ->orderBy('stores.name')
            ->get(['stores.id', 'stores.business_entity_id', 'stores.name', 'stores.slug', 'stores.status', 'stores.logo_path'])
            ->map(function (Store $store) {
                $change = $store->changeRequests->first();
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'status' => $store->status,
                    'logo_url' => $store->logo_url,
                    'business_name' => $store->businessEntity->trading_name ?: $store->businessEntity->legal_name,
                    'role' => $store->pivot->role,
                    'is_primary_owner' => (bool) $store->pivot->is_primary_owner,
                    'status_note' => $store->statusHistories->first()?->note,
                    'change_status' => $change?->status,
                    'change_note' => $change?->admin_note,
                ];
            })->values();

        $requested = $request->integer('store');
        $activeStoreId = $stores->contains('id', $requested) ? $requested : $stores->first()['id'] ?? null;

        return Inertia::render('Seller/Dashboard', [
            'stores' => $stores,
            'activeStoreId' => $activeStoreId,
        ]);
    }
}
