<?php
namespace App\Http\Requests\Auth; use Illuminate\Foundation\Http\FormRequest;
class OtpRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['email'=>['required','email','max:255'],'code'=>['required','digits:6'],'remember'=>['nullable','boolean']];} protected function prepareForValidation():void{$this->merge(['email'=>mb_strtolower(trim((string)$this->input('email'))),'code'=>trim((string)$this->input('code')),'remember'=>$this->boolean('remember')]);} }
