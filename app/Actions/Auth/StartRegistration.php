<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Services\Auth\EmailOtpService;
use Illuminate\Validation\ValidationException;

class StartRegistration
{
    public function __construct(
        private readonly EmailOtpService $emailOtpService,
    ) {}

    public function execute(string $name, string $email): void
    {
        $email = mb_strtolower(trim($email));

        $user = User::query()
            ->where('email', $email)
            ->first();

        if ($user?->isClosed()) {
            throw ValidationException::withMessages([
                'email' => 'This account is not available. Please contact Dubai Lanka support.',
            ]);
        }

        if ($user?->isSuspended()) {
            throw ValidationException::withMessages([
                'email' => 'This account is currently suspended. Please contact Dubai Lanka support.',
            ]);
        }

        /*
         * Existing completed account:
         *
         * Registration must not create another account.
         * The user should use the normal login flow instead.
         */
        if ($user && $user->hasPassword()) {
            throw ValidationException::withMessages([
                'email' => 'An account already exists for this email. Please log in instead.',
            ]);
        }

        /*
         * No User record is created here.
         *
         * New registrations must first prove ownership of the email.
         * Existing lightweight OTP-only users can use the same verification
         * flow and complete their account without creating a duplicate.
         */
        $this->emailOtpService->send(
            $email,
            'registration'
        );
    }
}
