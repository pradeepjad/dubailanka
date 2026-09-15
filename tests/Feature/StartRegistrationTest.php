<?php

namespace Tests\Feature;

use App\Actions\Auth\StartRegistration;
use App\Mail\AuthenticationOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StartRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private StartRegistration $action;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->action = app(StartRegistration::class);
    }

    public function test_new_email_can_start_registration_and_receives_otp(): void
    {
        $this->action->execute(
            'Pradeep',
            'NEW@Example.com'
        );

        $this->assertDatabaseMissing('users', [
            'email' => 'new@example.com',
        ]);

        $this->assertDatabaseHas('email_otps', [
            'email' => 'new@example.com',
            'purpose' => 'registration',
            'attempts' => 0,
        ]);

        Mail::assertSent(
            AuthenticationOtpMail::class,
            fn($mail) =>
            $mail->hasTo('new@example.com')
                && $mail->purpose === 'registration'
        );
    }

    public function test_existing_lightweight_account_can_continue_registration(): void
    {
        User::create([
            'name' => 'Existing Buyer',
            'email' => 'buyer@example.com',
            'password' => null,
            'email_verified_at' => now(),
        ]);

        $this->action->execute(
            'Different Name',
            'buyer@example.com'
        );

        $this->assertSame(
            1,
            User::where('email', 'buyer@example.com')->count()
        );

        $this->assertDatabaseHas('users', [
            'email' => 'buyer@example.com',
            'name' => 'Existing Buyer',
        ]);

        $this->assertDatabaseHas('email_otps', [
            'email' => 'buyer@example.com',
            'purpose' => 'registration',
        ]);

        Mail::assertSent(
            AuthenticationOtpMail::class,
            fn($mail) => $mail->hasTo('buyer@example.com')
        );
    }

    public function test_completed_account_cannot_start_registration_again(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $this->expectException(ValidationException::class);

        $this->action->execute(
            'Existing User',
            'existing@example.com'
        );
    }

    public function test_suspended_account_cannot_start_registration(): void
    {
        User::create([
            'name' => 'Suspended User',
            'email' => 'suspended@example.com',
            'password' => null,
        ])->forceFill([
            'status' => 'suspended',
            'suspended_at' => now(),
        ])->save();

        $this->expectException(ValidationException::class);

        $this->action->execute(
            'Suspended User',
            'suspended@example.com'
        );
    }

    public function test_closed_account_cannot_start_registration(): void
    {
        User::create([
            'name' => 'Closed User',
            'email' => 'closed@example.com',
            'password' => null,
        ])->forceFill([
            'status' => 'closed',
            'closed_at' => now(),
        ])->save();

        $this->expectException(ValidationException::class);

        $this->action->execute(
            'Closed User',
            'closed@example.com'
        );
    }
}
