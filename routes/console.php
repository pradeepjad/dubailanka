<?php

use App\Models\PlatformAdmin;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('dubailanka:make-admin {email} {--super}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error('No user found with that email.');
        return 1;
    }

    $isSuperAdmin = (bool) $this->option('super');

    PlatformAdmin::updateOrCreate(
        ['user_id' => $user->id],
        [
            'status' => 'active',
            'is_super_admin' => $isSuperAdmin,
        ]
    );

    $role = $isSuperAdmin ? 'Super Admin' : 'Admin';

    $this->info("{$user->email} is now an active {$role}.");

    return 0;
})->purpose('Grant temporary platform-admin identity before Milestone 04 permissions');
