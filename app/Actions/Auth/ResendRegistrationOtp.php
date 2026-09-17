<?php
namespace App\Actions\Auth;
use App\Services\Auth\EmailOtpService;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
class ResendRegistrationOtp { public function __construct(private readonly EmailOtpService $otp) {} public function execute(): void { $email=(string)Session::get('pending_registration_email'); if(!$email) throw ValidationException::withMessages(['code'=>'Your registration session has expired. Please start again.']); $this->otp->send($email,'registration'); } }
