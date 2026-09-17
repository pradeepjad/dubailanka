<?php
namespace App\Actions\Auth;
use App\Models\User; use App\Services\Auth\EmailOtpService;
class SendPasswordResetOtp { public function __construct(private readonly EmailOtpService $otp){} public function execute(string $email): void { $email=mb_strtolower(trim($email)); $u=User::where('email',$email)->first(); if($u && $u->hasPassword() && !$u->isClosed()) $this->otp->send($email,'password_reset'); } }
