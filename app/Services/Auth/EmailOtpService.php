<?php

namespace App\Services\Auth;

use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Mail\AuthenticationOtpMail;
use Illuminate\Support\Facades\Mail;

class EmailOtpService
{
    public const EXPIRY_MINUTES = 10;

    public const RESEND_COOLDOWN_SECONDS = 60;

    public const MAX_ATTEMPTS = 3;

    /**
     * Generate a new OTP for the given email and purpose.
     *
     * @return array{record: EmailOtp, code: string}
     */
    public function generate(string $email, string $purpose): array
    {
        $email = $this->normalizeEmail($email);

        $latestOtp = EmailOtp::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if (
            $latestOtp
            && $latestOtp->created_at
            && $latestOtp->created_at->gt(
                now()->subSeconds(self::RESEND_COOLDOWN_SECONDS)
            )
        ) {
            $secondsRemaining = self::RESEND_COOLDOWN_SECONDS
                - $latestOtp->created_at->diffInSeconds(now());

            throw ValidationException::withMessages([
                'email' => "Please wait {$secondsRemaining} seconds before requesting another code.",
            ]);
        }

        // Invalidate any previous unused OTP for this email/purpose.
        EmailOtp::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update([
                'consumed_at' => now(),
            ]);

        $code = (string) random_int(100000, 999999);

        $otp = EmailOtp::create([
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        return [
            'record' => $otp,
            'code' => $code,
        ];
    }

    /**
     * Generate and send a new OTP.
     */
    public function send(string $email, string $purpose): EmailOtp
    {
        $result = $this->generate($email, $purpose);

        Mail::to($result['record']->email)->send(
            new AuthenticationOtpMail(
                $result['code'],
                $purpose
            )
        );

        return $result['record'];
    }

    /**
     * Verify an OTP.
     */
    public function verify(string $email, string $purpose, string $code): EmailOtp
    {
        $email = $this->normalizeEmail($email);

        $otp = EmailOtp::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $otp || $otp->isExpired()) {
            throw ValidationException::withMessages([
                'code' => 'This verification code is invalid or has expired. Please request a new code.',
            ]);
        }

        if (! $otp->hasAttemptsRemaining()) {
            $otp->update([
                'consumed_at' => now(),
            ]);

            throw ValidationException::withMessages([
                'code' => 'Too many incorrect attempts. Please request a new code.',
            ]);
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            $otp->refresh();

            if (! $otp->hasAttemptsRemaining()) {
                $otp->update([
                    'consumed_at' => now(),
                ]);

                throw ValidationException::withMessages([
                    'code' => 'Too many incorrect attempts. Please request a new code.',
                ]);
            }

            $remainingAttempts = self::MAX_ATTEMPTS - $otp->attempts;

            throw ValidationException::withMessages([
                'code' => "The verification code is incorrect. {$remainingAttempts} attempt(s) remaining.",
            ]);
        }

        $otp->update([
            'consumed_at' => now(),
        ]);

        return $otp->fresh();
    }

    /**
     * Normalize email addresses before storing/querying OTP records.
     */
    private function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }
}
