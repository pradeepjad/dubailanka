<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Store\ReviewStoreChangeRequest;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreChangeRequestController extends Controller
{
    private function active(Store $store): StoreChangeRequest
    {
        return $store->changeRequests()
            ->whereIn('status', [
                StoreChangeRequest::STATUS_PENDING,
                StoreChangeRequest::STATUS_UNDER_REVIEW,
                StoreChangeRequest::STATUS_NEEDS_CHANGES,
            ])->firstOrFail();
    }

    public function start(Request $request, Store $store, ReviewStoreChangeRequest $review): RedirectResponse
    {
        $review->start($this->active($store), $request->user());
        return back()->with('success', 'Store identity change review started. Seller identity fields are now locked.');
    }

    public function approve(Request $request, Store $store, ReviewStoreChangeRequest $review): RedirectResponse
    {
        $review->approve($this->active($store), $request->user(), $request->string('note')->toString() ?: null);
        return back()->with('success', 'Store identity changes approved and applied.');
    }

    public function needsChanges(Request $request, Store $store, ReviewStoreChangeRequest $review): RedirectResponse
    {
        $data = $request->validate(['note' => ['required', 'string', 'max:2000']]);
        $review->needsChanges($this->active($store), $request->user(), $data['note']);
        return back()->with('success', 'Identity changes returned to the seller with instructions.');
    }

    public function reject(Request $request, Store $store, ReviewStoreChangeRequest $review): RedirectResponse
    {
        $data = $request->validate(['note' => ['required', 'string', 'max:2000']]);
        $review->reject($this->active($store), $request->user(), $data['note']);
        return back()->with('success', 'Proposed identity changes rejected. The approved store remains unchanged.');
    }
}
