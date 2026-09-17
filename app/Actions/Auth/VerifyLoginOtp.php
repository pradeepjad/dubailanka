<?php
namespace App\Actions\Auth;
use App\Models\User; use App\Services\Auth\EmailOtpService; use Illuminate\Support\Facades\Auth; use Illuminate\Validation\ValidationException;
class VerifyLoginOtp { public function __construct(private readonly EmailOtpService $otp){} public function execute(string $email,string $code,bool $remember=false): User { $email=mb_strtolower(trim($email)); $u=User::where('email',$email)->first(); if(!$u || $u->isClosed()) throw ValidationException::withMessages(['code'=>'The verification code is invalid or has expired.']); $this->otp->verify($email,'login',$code); if(!$u->email_verified_at){$u->email_verified_at=now();$u->save();} Auth::login($u,$remember); request()->session()->regenerate(); return $u; } }
