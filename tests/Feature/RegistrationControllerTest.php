<?php

namespace Tests\Feature;

use App\Mail\AuthenticationOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_verified_guest_can_complete_registration(): void
    {
        $response = $this
            ->withSession([
                'pending_registration_name' => 'Pradeep',
                'pending_registration_email' => 'test@example.com',
                'registration_verified' => true,
                'registration_verified_email' => 'test@example.com',
            ])
            ->post('/register/complete', [
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('home'));

        $user = \App\Models\User::where(
            'email',
            'test@example.com'
        )->first();

        $this->assertNotNull($user);

        $this->assertSame(
            'Pradeep',
            $user->name
        );

        $this->assertNotNull(
            $user->email_verified_at
        );

        $this->assertTrue(
            Hash::check('password123', $user->password)
        );

        $this->assertAuthenticatedAs($user);

        $response->assertSessionMissing(
            'pending_registration_name'
        );

        $response->assertSessionMissing(
            'pending_registration_email'
        );

        $response->assertSessionMissing(
            'registration_verified'
        );

        $response->assertSessionMissing(
            'registration_verified_email'
        );
    }

    public function test_guest_can_verify_registration_otp(): void
    {
        $otp = EmailOtp::create([
            'email' => 'test@example.com',
            'purpose' => 'registration',
            'code_hash' => Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this
            ->withSession([
                'pending_registration_name' => 'Pradeep',
                'pending_registration_email' => 'test@example.com',
            ])
            ->from('/register')
            ->post('/register/verify', [
                'email' => 'TEST@Example.com',
                'code' => '123456',
            ]);

        $response->assertRedirect('/register');

        $response->assertSessionHas(
            'registration_otp_verified',
            true
        );

        $response->assertSessionHas(
            'registration_verified',
            true
        );

        $response->assertSessionHas(
            'registration_verified_email',
            'test@example.com'
        );

        $this->assertNotNull(
            $otp->fresh()->consumed_at
        );
    }

    public function test_guest_can_start_registration(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Pradeep',
            'email' => 'TEST@Example.com',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHas(
            'registration_email',
            'test@example.com'
        );

        $response->assertSessionHas(
            'registration_name',
            'Pradeep'
        );

        $response->assertSessionHas(
            'registration_started',
            true
        );

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('email_otps', [
            'email' => 'test@example.com',
            'purpose' => 'registration',
        ]);

        Mail::assertSent(
            AuthenticationOtpMail::class,
            fn($mail) =>
            $mail->hasTo('test@example.com')
                && $mail->purpose === 'registration'
        );
    }

    public function test_registration_requires_name_and_valid_email(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors([
            'name',
            'email',
        ]);

        $this->assertDatabaseCount('email_otps', 0);

        Mail::assertNothingSent();
    }
}
