<?php

namespace App\Http\Middleware;

use App\Services\Auth\EmailOtpService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $otp = app(EmailOtpService::class);
        $registrationEmail = $request->session()->get('pending_registration_email');
        $loginEmail = $request->session()->get('pending_login_email');
        $resetEmail = $request->session()->get('pending_password_reset_email');

        return [
            ...parent::share($request),
            'auth' => ['user' => fn () => $request->user()?->only('id', 'name', 'email', 'status', 'suspension_reason')],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            'registration' => [
                'pending' => fn () => (bool) $registrationEmail,
                'name' => fn () => $request->session()->get('pending_registration_name'),
                'email' => fn () => $registrationEmail,
                'verified' => fn () => (bool) $request->session()->get('registration_verified', false),
                'resend_seconds' => fn () => $otp->resendSecondsRemaining($registrationEmail, 'registration'),
            ],
            'login' => [
                'pending_email' => fn () => $loginEmail,
                'resend_seconds' => fn () => $otp->resendSecondsRemaining($loginEmail, 'login'),
            ],
            'passwordReset' => [
                'pending_email' => fn () => $resetEmail,
                'resend_seconds' => fn () => $otp->resendSecondsRemaining($resetEmail, 'password_reset'),
            ],
        ];
    }
}
