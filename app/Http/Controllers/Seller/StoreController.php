<?php

namespace App\Http\Controllers\Seller;

use App\Actions\Seller\SaveStoreDraft;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreStoreRequest;
use App\Models\BusinessEntity;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function create(Request $request): Response
    {
        $businesses = $this->businessOptions($request);
        abort_if($businesses->isEmpty(), 403, 'Create a business before creating a store.');

        $requestedBusiness = $request->integer('business');
        $selectedBusinessId = $businesses->contains('id', $requestedBusiness)
            ? $requestedBusiness
            : $businesses->first()['id'];

        return Inertia::render('Seller/Store/Wizard', [
            'businesses' => $businesses,
            'selectedBusinessId' => $selectedBusinessId,
            'store' => null,
        ]);
    }

    public function store(StoreStoreRequest $request, SaveStoreDraft $saveStoreDraft): RedirectResponse
    {
        $this->ensureBusinessAccess($request, (int) $request->validated('business_entity_id'));
        $store = $saveStoreDraft->execute($request->user(), $request->validated());

        return to_route('seller.stores.edit', $store)->with('success', 'Store draft saved successfully. You can continue editing it anytime.');
    }

    public function edit(Request $request, Store $store): Response
    {
        $this->ensureStoreAccess($request, $store);
        abort_unless(in_array($store->status, [Store::STATUS_DRAFT, Store::STATUS_NEEDS_CHANGES], true), 403, 'This store cannot be edited while it is in review.');

        return Inertia::render('Seller/Store/Wizard', [
            'businesses' => $this->businessOptions($request),
            'selectedBusinessId' => $store->business_entity_id,
            'store' => $store,
        ]);
    }

    public function update(StoreStoreRequest $request, Store $store, SaveStoreDraft $saveStoreDraft): RedirectResponse
    {
        $this->ensureStoreAccess($request, $store);
        abort_unless(in_array($store->status, [Store::STATUS_DRAFT, Store::STATUS_NEEDS_CHANGES], true), 403, 'This store cannot be edited while it is in review.');
        $this->ensureBusinessAccess($request, (int) $request->validated('business_entity_id'));
        $saveStoreDraft->execute($request->user(), $request->validated(), $store);

        return to_route('seller.stores.edit', $store)->with('success', 'Store draft updated successfully.');
    }

    public function slugAvailability(Request $request): JsonResponse
    {
        $slug = strtolower(trim((string) $request->query('slug')));
        $ignore = $request->integer('ignore');
        $valid = (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) && strlen($slug) >= 3 && strlen($slug) <= 120;
        $available = false;

        if ($valid) {
            $query = Store::query()->where('slug', $slug);
            if ($ignore) $query->whereKeyNot($ignore);
            $available = ! $query->exists();
        }

        return response()->json(['slug' => $slug, 'valid' => $valid, 'available' => $available]);
    }

    private function businessOptions(Request $request)
    {
        return $request->user()->businessEntities()
            ->where('business_entities.status', 'active')
            ->orderBy('business_entities.legal_name')
            ->get(['business_entities.id', 'business_entities.legal_name', 'business_entities.trading_name', 'business_entities.country_code'])
            ->map(fn (BusinessEntity $business) => [
                'id' => $business->id,
                'name' => $business->trading_name ?: $business->legal_name,
                'legal_name' => $business->legal_name,
                'country_code' => $business->country_code,
            ])->values();
    }

    private function ensureBusinessAccess(Request $request, int $businessId): void
    {
        abort_unless($request->user()->businessEntities()->whereKey($businessId)->exists(), 403);
    }

    private function ensureStoreAccess(Request $request, Store $store): void
    {
        abort_unless($request->user()->stores()->whereKey($store->id)->exists(), 403);
    }
}

