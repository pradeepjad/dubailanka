<?php

namespace Tests\Feature;

use App\Actions\Auth\CompleteRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CompleteRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private CompleteRegistration $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = app(CompleteRegistration::class);
    }

    public function test_verified_registration_can_create_new_user(): void
    {
        session([
            'pending_registration_name' => 'Pradeep',
            'pending_registration_email' => 'test@example.com',
            'registration_verified' => true,
            'registration_verified_email' => 'test@example.com',
        ]);

        $user = $this->action->execute('password123');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Pradeep',
            'email' => 'test@example.com',
            'status' => 'active',
        ]);

        $this->assertNotNull(
            $user->email_verified_at
        );

        $this->assertTrue(
            Hash::check('password123', $user->password)
        );

        $this->assertTrue(
            Auth::check()
        );

        $this->assertSame(
            $user->id,
            Auth::id()
        );
    }

    public function test_existing_lightweight_user_is_completed_without_duplicate(): void
    {
        $existingUser = User::create([
            'name' => 'Existing Buyer',
            'email' => 'buyer@example.com',
            'password' => null,
            'email_verified_at' => now(),
        ]);

        session([
            'pending_registration_name' => 'Different Name',
            'pending_registration_email' => 'buyer@example.com',
            'registration_verified' => true,
            'registration_verified_email' => 'buyer@example.com',
        ]);

        $user = $this->action->execute('password123');

        $this->assertSame(
            $existingUser->id,
            $user->id
        );

        $this->assertSame(
            1,
            User::where('email', 'buyer@example.com')->count()
        );

        $this->assertSame(
            'Existing Buyer',
            $user->name
        );

        $this->assertTrue(
            Hash::check('password123', $user->password)
        );

        $this->assertSame(
            $user->id,
            Auth::id()
        );
    }

    public function test_registration_cannot_complete_without_verification(): void
    {
        session([
            'pending_registration_name' => 'Pradeep',
            'pending_registration_email' => 'test@example.com',
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->action->execute('password123');
    }

    public function test_registration_cannot_complete_when_verified_email_does_not_match(): void
    {
        session([
            'pending_registration_name' => 'Pradeep',
            'pending_registration_email' => 'first@example.com',
            'registration_verified' => true,
            'registration_verified_email' => 'second@example.com',
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->action->execute('password123');
    }

    public function test_temporary_registration_session_is_cleared_after_completion(): void
    {
        session([
            'pending_registration_name' => 'Pradeep',
            'pending_registration_email' => 'test@example.com',
            'registration_verified' => true,
            'registration_verified_email' => 'test@example.com',
        ]);

        $this->action->execute('password123');

        $this->assertNull(
            session('pending_registration_name')
        );

        $this->assertNull(
            session('pending_registration_email')
        );

        $this->assertNull(
            session('registration_verified')
        );

        $this->assertNull(
            session('registration_verified_email')
        );
    }

    public function test_completed_existing_account_cannot_be_completed_again(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => 'existing-password',
            'email_verified_at' => now(),
        ]);

        session([
            'pending_registration_name' => 'Existing User',
            'pending_registration_email' => 'existing@example.com',
            'registration_verified' => true,
            'registration_verified_email' => 'existing@example.com',
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->action->execute('new-password123');
    }
}
