<?php
namespace App\Actions\Auth;
use App\Models\User; use App\Services\Auth\EmailOtpService; use Illuminate\Support\Facades\Auth; use Illuminate\Validation\ValidationException;
class ResetPasswordWithOtp { public function __construct(private readonly EmailOtpService $otp){} public function execute(string $email,string $code,string $password): User { $email=mb_strtolower(trim($email)); $u=User::where('email',$email)->first(); if(!$u || !$u->hasPassword() || $u->isClosed()) throw ValidationException::withMessages(['code'=>'The verification code is invalid or has expired.']); $this->otp->verify($email,'password_reset',$code); $u->password=$password; $u->save(); Auth::login($u); request()->session()->regenerate(); return $u; } }
