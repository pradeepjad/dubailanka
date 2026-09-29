<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;color:#1f2937;line-height:1.6">
    <h2>Store {{ $resubmission ? 'resubmitted' : 'submitted' }} for review</h2>
    <p><strong>{{ $store->name }}</strong> has been {{ $resubmission ? 'resubmitted after requested changes' : 'submitted' }} for Dubai Lanka review.</p>
    <p><a href="{{ route('admin.stores.show', $store) }}">Open Store Review</a></p>
    <p>Dubai Lanka</p>
</body>
</html>
