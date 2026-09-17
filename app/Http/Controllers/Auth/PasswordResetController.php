<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResetPasswordWithOtp;
use App\Actions\Auth\SendPasswordResetOtp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\ResetPasswordOtpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    public function create(): Response { return Inertia::render('Auth/ForgotPassword'); }

    public function send(EmailRequest $request, SendPasswordResetOtp $action): RedirectResponse
    {
        $email = (string) $request->string('email');
        $action->execute($email);
        $request->session()->put('pending_password_reset_email', $email);
        return back();
    }

    public function resend(Request $request, SendPasswordResetOtp $action): RedirectResponse
    {
        $email = (string) $request->session()->get('pending_password_reset_email');
        if ($email) $action->execute($email);
        return back();
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget('pending_password_reset_email');
        return redirect()->route('password.request');
    }

    public function reset(ResetPasswordOtpRequest $request, ResetPasswordWithOtp $action): RedirectResponse
    {
        $user = $action->execute($request->string('email'), $request->string('code'), $request->string('password'));
        $request->session()->forget('pending_password_reset_email');
        return redirect()->route($user->isSuspended() ? 'account.suspended' : 'home');
    }
}
