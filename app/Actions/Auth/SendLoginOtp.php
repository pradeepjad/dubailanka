<?php
namespace App\Actions\Auth;
use App\Models\User; use App\Services\Auth\EmailOtpService;
class SendLoginOtp { public function __construct(private readonly EmailOtpService $otp){} public function execute(string $email): void { $email=mb_strtolower(trim($email)); $u=User::where('email',$email)->first(); if($u && !$u->isClosed()) $this->otp->send($email,'login'); } }
