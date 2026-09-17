<?php
use App\Models\PlatformAdmin; use App\Models\User; use Illuminate\Foundation\Inspiring; use Illuminate\Support\Facades\Artisan;
Artisan::command('inspire', function () { $this->comment(Inspiring::quote()); })->purpose('Display an inspiring quote');
Artisan::command('dubailanka:make-admin {email}', function (string $email) { $user=User::where('email',$email)->first(); if(!$user){$this->error('No user found with that email.');return 1;} PlatformAdmin::updateOrCreate(['user_id'=>$user->id],['status'=>'active','is_super_admin'=>true]);$this->info("{$user->email} is now an active platform admin.");return 0;})->purpose('Grant temporary platform-admin identity before Milestone 04 permissions');
