<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $stores = $request->user()->stores()
            ->with('businessEntity:id,legal_name,trading_name')
            ->orderBy('stores.name')
            ->get(['stores.id', 'stores.business_entity_id', 'stores.name', 'stores.slug', 'stores.status', 'stores.logo_path'])
            ->map(fn (Store $store) => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'status' => $store->status,
                'logo_url' => $store->logo_url,
                'business_name' => $store->businessEntity->trading_name ?: $store->businessEntity->legal_name,
                'role' => $store->pivot->role,
                'is_primary_owner' => (bool) $store->pivot->is_primary_owner,
            ])->values();

        $requested = $request->integer('store');
        $activeStoreId = $stores->contains('id', $requested) ? $requested : $stores->first()['id'] ?? null;

        return Inertia::render('Seller/Dashboard', [
            'stores' => $stores,
            'activeStoreId' => $activeStoreId,
        ]);
    }
}
