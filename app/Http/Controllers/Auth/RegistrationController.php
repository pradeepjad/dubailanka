<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\StartRegistration;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StartRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Actions\Auth\VerifyRegistrationOtp;
use App\Http\Requests\Auth\VerifyRegistrationOtpRequest;
use App\Actions\Auth\CompleteRegistration;
use App\Actions\Auth\ResendRegistrationOtp;
use App\Http\Requests\Auth\CompleteRegistrationRequest;

class RegistrationController extends Controller
{
    public function create(Request $request): Response
    {
        $email = (string) $request->session()->get('pending_registration_email');

        if ($email && User::query()->where('email', $email)->whereNotNull('password')->exists()) {
            $request->session()->forget([
                'pending_registration_name',
                'pending_registration_email',
                'registration_verified',
                'registration_verified_email',
            ]);
        }

        return Inertia::render('Auth/Register');
    }

    public function claimInvitation(Request $request, User $user, StartRegistration $startRegistration): RedirectResponse
    {
        abort_if($user->hasPassword(), 422, 'This seller account has already been claimed. Please log in.');
        abort_if($user->isClosed() || $user->isSuspended(), 403, 'This account is not available.');

        $startRegistration->execute($user->name, $user->email);

        $request->session()->put([
            'pending_registration_name' => $user->name,
            'pending_registration_email' => $user->email,
            'seller_invitation_claim' => true,
        ]);
        $request->session()->forget(['registration_verified','registration_verified_email']);

        return redirect()->route('register');
    }

    /**
     * Start a new registration or continue an existing
     * lightweight account registration.
     */
    public function store(
        StartRegistrationRequest $request,
        StartRegistration $startRegistration
    ): RedirectResponse {
        $data = $request->validated();

        $startRegistration->execute(
            $data['name'],
            $data['email']
        );

        $request->session()->put([
            'pending_registration_name' => $data['name'],
            'pending_registration_email' => $data['email'],
        ]);

        $request->session()->forget([
            'registration_verified',
            'registration_verified_email',
            'seller_invitation_claim',
        ]);

        return back()->with([
            'registration_email' => $data['email'],
            'registration_name' => $data['name'],
            'registration_started' => true,
        ]);
    }

    public function verify(
        VerifyRegistrationOtpRequest $request,
        VerifyRegistrationOtp $verifyRegistrationOtp
    ): RedirectResponse {
        $data = $request->validated();

        $verifyRegistrationOtp->execute(
            $data['email'],
            $data['code']
        );

        return back()->with([
            'registration_otp_verified' => true,
        ]);
    }

    public function resend(ResendRegistrationOtp $resend): RedirectResponse
    {
        $resend->execute();
        return back()->with('otp_resent', true);
    }

    public function cancel(): RedirectResponse
    {
        session()->forget([
            'pending_registration_name',
            'pending_registration_email',
            'registration_verified',
            'registration_verified_email',
            'seller_invitation_claim',
        ]);
        return redirect()->route('register');
    }

    public function complete(
        CompleteRegistrationRequest $request,
        CompleteRegistration $completeRegistration
    ): RedirectResponse {
        $data = $request->validated();

        $invitationClaim = (bool) $request->session()->get('seller_invitation_claim', false);

        $user = $completeRegistration->execute(
            $data['password']
        );

        $request->session()->forget('seller_invitation_claim');

        if ($invitationClaim) {
            return redirect()->route($user->stores()->exists() ? 'seller.dashboard' : 'seller.start');
        }

        return redirect()->route('home');
    }
}
