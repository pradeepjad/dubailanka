<?php
namespace App\Http\Requests\Auth; use Illuminate\Foundation\Http\FormRequest;
class LoginRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['email'=>['required','email','max:255'],'password'=>['required','string'],'remember'=>['nullable','boolean']];} protected function prepareForValidation():void{$this->merge(['email'=>mb_strtolower(trim((string)$this->input('email'))),'remember'=>$this->boolean('remember')]);} }
