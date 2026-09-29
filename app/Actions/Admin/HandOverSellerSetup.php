<?php

namespace App\Actions\Admin;

use App\Mail\SellerOnboardingHandoverMail;
use App\Mail\SellerOnboardingInvitationMail;
use App\Models\SellerOnboardingHandover;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class HandOverSellerSetup
{
    public function execute(User $admin, string $mode, ?string $name, string $email): SellerOnboardingHandover
    {
        if ($mode !== 'invite') {
            throw ValidationException::withMessages(['ownership_mode' => 'Only Invite Seller can be handed over before a Business Entity exists.']);
        }

        $email = mb_strtolower(trim($email));
        $user = User::where('email', $email)->first();

        if ($mode === 'existing' && ! $user) {
            throw ValidationException::withMessages(['owner_email' => 'No Dubai Lanka account exists for this email. Choose Invite Seller instead.']);
        }
        if ($mode === 'invite' && $user?->hasPassword()) {
            throw ValidationException::withMessages(['owner_email' => 'A Dubai Lanka account already exists for this email. Choose Existing Account instead.']);
        }
        if ($user?->isClosed() || $user?->isSuspended()) {
            throw ValidationException::withMessages(['owner_email' => 'This account is not available for seller onboarding.']);
        }

        $handover = DB::transaction(function () use ($admin, $mode, $name, $email, &$user) {
            if (! $user) {
                $user = User::create(['name' => trim((string)$name), 'email' => $email, 'password' => null]);
            }

            return SellerOnboardingHandover::updateOrCreate(
                ['owner_email' => $email, 'status' => SellerOnboardingHandover::STATUS_PENDING],
                [
                    'ownership_mode' => $mode,
                    'user_id' => $user->id,
                    'owner_name' => $user->name,
                    'handed_over_by_user_id' => $admin->id,
                ]
            );
        });

        if ($user->hasPassword()) {
            Mail::to($user->email)->send(new SellerOnboardingHandoverMail($user));
        } else {
            Mail::to($user->email)->send(new SellerOnboardingInvitationMail($user->id, $user->name));
        }

        return $handover;
    }
}
