<?php

namespace Tests\Feature;

use App\Actions\Auth\VerifyRegistrationOtp;
use App\Models\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VerifyRegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    private VerifyRegistrationOtp $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = app(VerifyRegistrationOtp::class);
    }

    public function test_valid_registration_otp_can_be_verified(): void
    {
        session([
            'pending_registration_name' => 'Pradeep',
            'pending_registration_email' => 'test@example.com',
        ]);

        $otp = EmailOtp::create([
            'email' => 'test@example.com',
            'purpose' => 'registration',
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->action->execute(
            'test@example.com',
            '123456'
        );

        $this->assertTrue(
            session('registration_verified')
        );

        $this->assertSame(
            'test@example.com',
            session('registration_verified_email')
        );

        $this->assertNotNull(
            $otp->fresh()->consumed_at
        );
    }

    public function test_verification_fails_without_pending_registration_session(): void
    {
        EmailOtp::create([
            'email' => 'test@example.com',
            'purpose' => 'registration',
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->action->execute(
            'test@example.com',
            '123456'
        );
    }

    public function test_verification_fails_when_email_does_not_match_pending_registration(): void
    {
        session([
            'pending_registration_name' => 'Pradeep',
            'pending_registration_email' => 'correct@example.com',
        ]);

        EmailOtp::create([
            'email' => 'wrong@example.com',
            'purpose' => 'registration',
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->action->execute(
            'wrong@example.com',
            '123456'
        );
    }

    public function test_invalid_otp_does_not_mark_registration_as_verified(): void
    {
        session([
            'pending_registration_name' => 'Pradeep',
            'pending_registration_email' => 'test@example.com',
        ]);

        EmailOtp::create([
            'email' => 'test@example.com',
            'purpose' => 'registration',
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            $this->action->execute(
                'test@example.com',
                '999999'
            );
        } catch (ValidationException) {
            // Expected.
        }

        $this->assertFalse(
            (bool) session('registration_verified', false)
        );

        $this->assertNull(
            session('registration_verified_email')
        );

        $this->assertDatabaseHas('email_otps', [
            'email' => 'test@example.com',
            'purpose' => 'registration',
            'attempts' => 1,
            'consumed_at' => null,
        ]);
    }
}
