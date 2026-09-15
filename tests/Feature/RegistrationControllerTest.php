<?php

namespace Tests\Feature;

use App\Mail\AuthenticationOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
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
