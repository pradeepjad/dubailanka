<?php
namespace App\Actions\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class LoginWithPassword { public function execute(string $email,string $password,bool $remember=false): User { $email=mb_strtolower(trim($email)); $user=User::where('email',$email)->first(); if(!$user || !$user->hasPassword() || !Hash::check($password,$user->password)) throw ValidationException::withMessages(['email'=>'The email or password is incorrect.']); if($user->isClosed()) throw ValidationException::withMessages(['email'=>'This account is not available. Please contact Dubai Lanka support.']); Auth::login($user,$remember); request()->session()->regenerate(); return $user; } }
