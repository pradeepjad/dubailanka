<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CompleteRegistration
{
    public function execute(string $password): User
    {
        $name = trim(
            (string) Session::get('pending_registration_name')
        );

        $email = mb_strtolower(
            trim((string) Session::get('pending_registration_email'))
        );

        $verified = (bool) Session::get(
            'registration_verified',
            false
        );

        $verifiedEmail = mb_strtolower(
            trim((string) Session::get('registration_verified_email'))
        );

        if (
            ! $verified
            || ! $email
            || ! $verifiedEmail
            || $email !== $verifiedEmail
        ) {
            throw ValidationException::withMessages([
                'password' => 'Your registration verification has expired. Please start registration again.',
            ]);
        }

        $user = DB::transaction(function () use (
            $name,
            $email,
            $password
        ) {
            $user = User::query()
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if ($user?->isClosed()) {
                throw ValidationException::withMessages([
                    'password' => 'This account is not available. Please contact Dubai Lanka support.',
                ]);
            }

            if ($user?->isSuspended()) {
                throw ValidationException::withMessages([
                    'password' => 'This account is currently suspended. Please contact Dubai Lanka support.',
                ]);
            }

            if ($user && $user->hasPassword()) {
                throw ValidationException::withMessages([
                    'password' => 'This account has already been completed. Please log in instead.',
                ]);
            }

            if (! $user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]);
            } else {
                /*
                 * Existing lightweight buyer:
                 *
                 * Keep the existing User record and its history.
                 * Do not overwrite its existing name.
                 */
                $user->password = $password;
            }

            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
            }

            $user->save();

            return $user->fresh();
        });

        Auth::login($user);

        Session::regenerate();

        Session::forget([
            'pending_registration_name',
            'pending_registration_email',
            'registration_verified',
            'registration_verified_email',
        ]);

        return $user;
    }
}
