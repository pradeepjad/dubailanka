<!doctype html>
<html>
<body style="font-family:Arial,sans-serif;color:#1f2937;line-height:1.6">
    <h2>You've been assigned a Dubai Lanka Store</h2>
    <p>Hello {{ $owner->name }},</p>
    <p>Your existing Dubai Lanka account has been assigned as the <strong>Primary Owner</strong> of <strong>{{ $store->name }}</strong>.</p>
    <p>Your existing email, password and account details have not been changed. Sign in normally to access this Store from your Seller Dashboard.</p>
    <p><a href="{{ route('seller.dashboard') }}">Open Seller Dashboard</a></p>
    <p>Dubai Lanka</p>
</body>
</html>
