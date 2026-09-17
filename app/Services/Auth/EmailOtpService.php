<?php

namespace App\Services\Auth;

use App\Mail\AuthenticationOtpMail;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EmailOtpService
{
    public const EXPIRY_MINUTES = 10;
    public const RESEND_COOLDOWN_SECONDS = 60;
    public const MAX_ATTEMPTS = 3;

    public function generate(string $email, string $purpose): array
    {
        $email = $this->normalizeEmail($email);
        $secondsRemaining = $this->resendSecondsRemaining($email, $purpose);

        if ($secondsRemaining > 0) {
            throw ValidationException::withMessages([
                'email' => "Please wait {$secondsRemaining} seconds before requesting another code.",
            ]);
        }

        EmailOtp::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = (string) random_int(100000, 999999);

        $otp = EmailOtp::create([
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        return ['record' => $otp, 'code' => $code];
    }

    public function send(string $email, string $purpose): EmailOtp
    {
        $result = $this->generate($email, $purpose);
        Mail::to($result['record']->email)->send(
            new AuthenticationOtpMail($result['code'], $purpose)
        );
        return $result['record'];
    }

    public function resendSecondsRemaining(?string $email, string $purpose): int
    {
        if (! $email) return 0;

        $latest = EmailOtp::query()
            ->where('email', $this->normalizeEmail($email))
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if (! $latest?->created_at) return 0;

        $availableAt = $latest->created_at->copy()->addSeconds(self::RESEND_COOLDOWN_SECONDS);
        return max(0, now()->diffInSeconds($availableAt, false));
    }

    public function verify(string $email, string $purpose, string $code): EmailOtp
    {
        $email = $this->normalizeEmail($email);
        $otp = EmailOtp::query()->where('email', $email)->where('purpose', $purpose)
            ->whereNull('consumed_at')->latest('id')->first();

        if (! $otp || $otp->isExpired()) {
            throw ValidationException::withMessages(['code' => 'This verification code is invalid or has expired. Please request a new code.']);
        }
        if (! $otp->hasAttemptsRemaining()) {
            throw ValidationException::withMessages(['code' => 'Too many incorrect attempts. Please request a new code.']);
        }
        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            if ($otp->fresh()->attempts >= self::MAX_ATTEMPTS) $otp->update(['consumed_at' => now()]);
            throw ValidationException::withMessages(['code' => 'The verification code is incorrect. Please try again.']);
        }
        $otp->update(['consumed_at' => now()]);
        return $otp->fresh();
    }

    private function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }
}
