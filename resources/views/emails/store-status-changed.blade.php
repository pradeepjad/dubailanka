<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;color:#1f2937;line-height:1.6">
    @php
        $title = match ($status) {
            \App\Models\Store::STATUS_NEEDS_CHANGES => 'Changes are required',
            \App\Models\Store::STATUS_APPROVED => 'Your Store has been approved',
            \App\Models\Store::STATUS_REJECTED => 'Your Store was not approved',
            \App\Models\Store::STATUS_SUSPENDED => 'Your Store has been suspended',
            default => 'Your Store status was updated',
        };
    @endphp
    <h2>{{ $title }}</h2>
    <p>Hello,</p>
    <p>This update is for <strong>{{ $store->name }}</strong>.</p>
    @if($note)
        <p><strong>Admin message:</strong><br>{{ $note }}</p>
    @endif
    @if($status === \App\Models\Store::STATUS_NEEDS_CHANGES)
        <p>Please sign in to your Seller Dashboard, make the requested corrections, save them, and resubmit the Store for review.</p>
    @elseif($status === \App\Models\Store::STATUS_APPROVED)
        <p>Your Store has completed the current review successfully.</p>
    @elseif($status === \App\Models\Store::STATUS_REJECTED)
        <p>Please review the Admin message above. Contact Dubai Lanka support if you need clarification.</p>
    @elseif($status === \App\Models\Store::STATUS_SUSPENDED)
        <p>Please review the reason above and contact Dubai Lanka support if you need assistance.</p>
    @endif
    <p><a href="{{ route('seller.dashboard') }}">Open Seller Dashboard</a></p>
    <p>Dubai Lanka</p>
</body>
</html>
