<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\AssignStoreAdmin;
use App\Actions\Store\ChangeStoreStatus;
use App\Http\Controllers\Controller;
use App\Models\PlatformAdmin;
use App\Models\Store;
use App\Models\StoreChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreModerationController extends Controller
{
    public function index(Request $request): Response
    {
        $platformAdmin = $request->user()->platformAdmin;

        $stores = Store::with([
            'businessEntity:id,legal_name,trading_name',
            'adminAssignment.platformAdmin.user:id,name,email',
            'changeRequests' => fn($q) => $q->whereIn('status', [
                StoreChangeRequest::STATUS_PENDING,
                StoreChangeRequest::STATUS_UNDER_REVIEW,
                StoreChangeRequest::STATUS_NEEDS_CHANGES,
            ]),
        ])->when(
            ! $platformAdmin?->is_super_admin,
            fn ($query) => $query->whereHas(
                'adminAssignment',
                fn ($assignment) => $assignment->where('platform_admin_id', $platformAdmin?->id)
            )
        )->whereIn('status', [
            Store::STATUS_PENDING,
            Store::STATUS_UNDER_REVIEW,
            Store::STATUS_NEEDS_CHANGES,
            Store::STATUS_APPROVED,
            Store::STATUS_REJECTED,
            Store::STATUS_SUSPENDED,
        ])->latest('updated_at')->get()->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'slug' => $s->slug,
            'status' => $s->status,
            'business_name' => $s->businessEntity->trading_name ?: $s->businessEntity->legal_name,
            'country_code' => $s->country_code,
            'updated_at' => $s->updated_at,
            'change_status' => $s->changeRequests->first()?->status,
            'assigned_admin' => $s->adminAssignment?->platformAdmin?->user?->only(['id', 'name', 'email']),
        ]);

        return Inertia::render('Admin/Stores/Index', ['stores' => $stores]);
    }

    public function show(Request $request, Store $store): Response
    {
        $this->authorizeModeration($request, $store);

        $store->load([
            'businessEntity:id,legal_name,trading_name',
            'statusHistories.actor:id,name,email',
            'adminAssignment.platformAdmin.user:id,name,email',
            'adminAssignmentHistories',
            'changeRequests' => fn($q) => $q->with(['requestedBy:id,name,email', 'reviewedBy:id,name,email'])->latest(),
        ]);

        $isSuperAdmin = (bool) $request->user()->platformAdmin?->is_super_admin;
        $admins = $isSuperAdmin
            ? PlatformAdmin::with('user:id,name,email,status')->where('status', 'active')->get()
            ->filter(fn($admin) => $admin->user?->isActive())
            ->values()
            ->map(fn($admin) => [
                'id' => $admin->id,
                'name' => $admin->user->name,
                'email' => $admin->user->email,
                'is_super_admin' => $admin->is_super_admin,
            ])
            : collect();

        return Inertia::render('Admin/Stores/Review', [
            'store' => $store,
            'isSuperAdmin' => $isSuperAdmin,
            'platformAdmins' => $admins,
        ]);
    }

    public function assignAdmin(Request $request, Store $store, AssignStoreAdmin $action): RedirectResponse
    {
        abort_unless($request->user()->platformAdmin?->is_super_admin, 403);

        $data = $request->validate([
            'platform_admin_id' => ['required', 'integer', 'exists:platform_admins,id'],
        ]);

        $platformAdmin = PlatformAdmin::findOrFail($data['platform_admin_id']);
        $action->execute($store, $platformAdmin, $request->user());

        return back()->with('success', 'Responsible Store Admin updated successfully.');
    }

    public function start(Request $request, Store $store, ChangeStoreStatus $change): RedirectResponse
    {
        $this->authorizeModeration($request, $store);
        abort_unless($store->status === Store::STATUS_PENDING, 422);
        $change->execute($store, Store::STATUS_UNDER_REVIEW, $request->user());
        return back()->with('success', 'Review started. Seller editing is now locked.');
    }

    public function approve(Request $request, Store $store, ChangeStoreStatus $change): RedirectResponse
    {
        $this->authorizeModeration($request, $store);
        abort_unless($store->status === Store::STATUS_UNDER_REVIEW, 422);
        $change->execute($store, Store::STATUS_APPROVED, $request->user(), $request->string('note')->toString() ?: null);
        return back()->with('success', 'Store approved.');
    }

    public function needsChanges(Request $request, Store $store, ChangeStoreStatus $change): RedirectResponse
    {
        $this->authorizeModeration($request, $store);
        $data = $request->validate(['note' => 'required|string|max:2000']);
        abort_unless($store->status === Store::STATUS_UNDER_REVIEW, 422);
        $change->execute($store, Store::STATUS_NEEDS_CHANGES, $request->user(), $data['note']);
        return back()->with('success', 'Store returned to seller with requested changes.');
    }

    public function reject(Request $request, Store $store, ChangeStoreStatus $change): RedirectResponse
    {
        $this->authorizeModeration($request, $store);
        $data = $request->validate(['note' => 'required|string|max:2000']);
        abort_unless($store->status === Store::STATUS_UNDER_REVIEW, 422);
        $change->execute($store, Store::STATUS_REJECTED, $request->user(), $data['note']);
        return back()->with('success', 'Store rejected.');
    }

    public function suspend(Request $request, Store $store, ChangeStoreStatus $change): RedirectResponse
    {
        $this->authorizeModeration($request, $store);
        $data = $request->validate(['note' => 'required|string|max:2000']);
        abort_unless($store->status === Store::STATUS_APPROVED, 422);
        $change->execute($store, Store::STATUS_SUSPENDED, $request->user(), $data['note']);
        return back()->with('success', 'Store suspended.');
    }

    public function reactivate(Request $request, Store $store, ChangeStoreStatus $change): RedirectResponse
    {
        $this->authorizeModeration($request, $store);
        abort_unless($store->status === Store::STATUS_SUSPENDED, 422);
        $change->execute($store, Store::STATUS_APPROVED, $request->user(), $request->string('note')->toString() ?: 'Store reactivated.');
        return back()->with('success', 'Store reactivated.');
    }

    private function authorizeModeration(Request $request, Store $store): void
    {
        $platformAdmin = $request->user()->platformAdmin;

        if ($platformAdmin?->is_super_admin) {
            return;
        }

        $assignedPlatformAdminId = $store->adminAssignment()->value('platform_admin_id');

        abort_unless(
            $assignedPlatformAdminId !== null && $assignedPlatformAdminId === $platformAdmin?->id,
            403,
            'You are not assigned as the Responsible Admin for this Store.'
        );
    }
}
