<?php

namespace App\Actions\Auth;

use App\Services\Auth\EmailOtpService;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class VerifyRegistrationOtp
{
    public function __construct(
        private readonly EmailOtpService $emailOtpService,
    ) {}

    public function execute(string $email, string $code): void
    {
        $email = mb_strtolower(trim($email));

        $pendingEmail = Session::get('pending_registration_email');

        if (! $pendingEmail) {
            throw ValidationException::withMessages([
                'code' => 'Your registration session has expired. Please start registration again.',
            ]);
        }

        $pendingEmail = mb_strtolower(trim((string) $pendingEmail));

        if ($email !== $pendingEmail) {
            throw ValidationException::withMessages([
                'code' => 'The verification request does not match your registration. Please start again.',
            ]);
        }

        $this->emailOtpService->verify(
            $email,
            'registration',
            $code
        );

        Session::put('registration_verified', true);
        Session::put('registration_verified_email', $email);
    }
}
