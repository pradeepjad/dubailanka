<?php

namespace Tests\Feature;

use App\Models\EmailOtp;
use App\Services\Auth\EmailOtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Mail\AuthenticationOtpMail;
use Illuminate\Support\Facades\Mail;

class EmailOtpServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmailOtpService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(EmailOtpService::class);
    }

    public function test_it_generates_and_sends_an_otp_email(): void
    {
        Mail::fake();

        $otp = $this->service->send(
            'TEST@Example.com',
            'registration'
        );

        $this->assertSame('test@example.com', $otp->email);
        $this->assertSame('registration', $otp->purpose);
        $this->assertNull($otp->consumed_at);

        $this->assertDatabaseHas('email_otps', [
            'id' => $otp->id,
            'email' => 'test@example.com',
            'purpose' => 'registration',
            'attempts' => 0,
        ]);

        Mail::assertSent(AuthenticationOtpMail::class, function ($mail) {
            return $mail->hasTo('test@example.com')
                && $mail->purpose === 'registration'
                && preg_match('/^\d{6}$/', $mail->code) === 1;
        });
    }

    public function test_it_generates_a_six_digit_otp_and_stores_only_its_hash(): void
    {
        $result = $this->service->generate(
            'TEST@Example.com',
            'login'
        );

        $otp = $result['record'];
        $code = $result['code'];

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);

        $this->assertSame('test@example.com', $otp->email);
        $this->assertSame('login', $otp->purpose);
        $this->assertSame(0, $otp->attempts);
        $this->assertNull($otp->consumed_at);

        $this->assertNotSame($code, $otp->code_hash);
        $this->assertTrue(Hash::check($code, $otp->code_hash));

        $this->assertTrue(
            $otp->expires_at->between(
                now()->addMinutes(9),
                now()->addMinutes(11)
            )
        );
    }

    public function test_it_prevents_resending_within_sixty_seconds(): void
    {
        $this->service->generate(
            'test@example.com',
            'login'
        );

        $this->expectException(ValidationException::class);

        $this->service->generate(
            'test@example.com',
            'login'
        );
    }

    public function test_a_new_otp_invalidates_the_previous_unused_otp(): void
    {
        $first = $this->service->generate(
            'test@example.com',
            'login'
        );

        $this->travel(61)->seconds();

        $second = $this->service->generate(
            'test@example.com',
            'login'
        );

        $firstOtp = $first['record']->fresh();

        $this->assertNotNull($firstOtp->consumed_at);
        $this->assertNull($second['record']->consumed_at);
    }

    public function test_it_successfully_verifies_a_valid_otp_and_consumes_it(): void
    {
        $result = $this->service->generate(
            'test@example.com',
            'login'
        );

        $verifiedOtp = $this->service->verify(
            'test@example.com',
            'login',
            $result['code']
        );

        $this->assertNotNull($verifiedOtp->consumed_at);
    }

    public function test_a_successfully_used_otp_cannot_be_used_again(): void
    {
        $result = $this->service->generate(
            'test@example.com',
            'login'
        );

        $this->service->verify(
            'test@example.com',
            'login',
            $result['code']
        );

        $this->expectException(ValidationException::class);

        $this->service->verify(
            'test@example.com',
            'login',
            $result['code']
        );
    }

    public function test_expired_otp_cannot_be_verified(): void
    {
        $result = $this->service->generate(
            'test@example.com',
            'login'
        );

        $this->travel(11)->minutes();

        $this->expectException(ValidationException::class);

        $this->service->verify(
            'test@example.com',
            'login',
            $result['code']
        );
    }

    public function test_incorrect_code_increases_attempt_count(): void
    {
        $result = $this->service->generate(
            'test@example.com',
            'login'
        );

        try {
            $this->service->verify(
                'test@example.com',
                'login',
                '000000'
            );
        } catch (ValidationException) {
            // Expected.
        }

        $this->assertSame(
            1,
            $result['record']->fresh()->attempts
        );
    }

    public function test_otp_is_invalidated_after_three_incorrect_attempts(): void
    {
        $result = $this->service->generate(
            'test@example.com',
            'login'
        );

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $this->service->verify(
                    'test@example.com',
                    'login',
                    '000000'
                );
            } catch (ValidationException) {
                // Expected.
            }
        }

        $otp = $result['record']->fresh();

        $this->assertSame(3, $otp->attempts);
        $this->assertNotNull($otp->consumed_at);
    }
}
