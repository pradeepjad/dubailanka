<?php
namespace App\Http\Requests\Auth; use Illuminate\Foundation\Http\FormRequest;
class ResetPasswordOtpRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['email'=>['required','email','max:255'],'code'=>['required','digits:6'],'password'=>['required','string','min:8','confirmed']];} protected function prepareForValidation():void{$this->merge(['email'=>mb_strtolower(trim((string)$this->input('email'))),'code'=>trim((string)$this->input('code'))]);} }
