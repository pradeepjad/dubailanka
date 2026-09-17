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
        ]);
        return redirect()->route('register');
    }

    public function complete(
        CompleteRegistrationRequest $request,
        CompleteRegistration $completeRegistration
    ): RedirectResponse {
        $data = $request->validated();

        $completeRegistration->execute(
            $data['password']
        );

        return redirect()->route('home');
    }
}
