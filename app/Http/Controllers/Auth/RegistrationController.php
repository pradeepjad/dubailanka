<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\StartRegistration;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StartRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use App\Actions\Auth\VerifyRegistrationOtp;
use App\Http\Requests\Auth\VerifyRegistrationOtpRequest;
use App\Actions\Auth\CompleteRegistration;
use App\Http\Requests\Auth\CompleteRegistrationRequest;

class RegistrationController extends Controller
{
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
