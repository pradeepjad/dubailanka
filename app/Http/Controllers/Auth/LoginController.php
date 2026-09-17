<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginWithPassword;
use App\Actions\Auth\SendLoginOtp;
use App\Actions\Auth\VerifyLoginOtp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function create(): Response { return Inertia::render('Auth/Login'); }

    public function store(LoginRequest $request, LoginWithPassword $action): RedirectResponse
    {
        $user = $action->execute($request->string('email'), $request->string('password'), $request->boolean('remember'));
        return redirect()->route($user->isSuspended() ? 'account.suspended' : 'home');
    }

    public function sendOtp(EmailRequest $request, SendLoginOtp $action): RedirectResponse
    {
        $email = (string) $request->string('email');
        $action->execute($email);
        $request->session()->put('pending_login_email', $email);
        return back();
    }

    public function resendOtp(Request $request, SendLoginOtp $action): RedirectResponse
    {
        $email = (string) $request->session()->get('pending_login_email');
        if ($email) $action->execute($email);
        return back();
    }

    public function cancelOtp(Request $request): RedirectResponse
    {
        $request->session()->forget('pending_login_email');
        return redirect()->route('login');
    }

    public function verifyOtp(OtpRequest $request, VerifyLoginOtp $action): RedirectResponse
    {
        $user = $action->execute($request->string('email'), $request->string('code'), $request->boolean('remember'));
        $request->session()->forget('pending_login_email');
        return redirect()->route($user->isSuspended() ? 'account.suspended' : 'home');
    }
}
