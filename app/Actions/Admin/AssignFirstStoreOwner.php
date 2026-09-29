<?php
namespace App\Actions\Admin;
use App\Mail\BusinessOwnerAssignedMail;
use App\Mail\SellerOnboardingInvitationMail;
use App\Mail\StoreOwnerAssignedMail;
use App\Models\BusinessEntity;
use App\Models\OwnershipAssignment;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
class AssignFirstStoreOwner
{
    public function execute(BusinessEntity $business, ?Store $store, string $name, string $email, User $admin, bool $sendInvitation=true): User
    {
        $email=mb_strtolower(trim($email)); $send=false;
        $user=DB::transaction(function() use($business,$store,$name,$email,$admin,&$send){
            $business->loadMissing('stores');
            if($business->memberships()->where('is_primary_owner',true)->exists() || $business->stores()->whereHas('memberships',fn($q)=>$q->where('is_primary_owner',true))->exists()) throw ValidationException::withMessages(['owner_email'=>'A Primary Owner is already assigned. Ownership transfer must use the dedicated transfer workflow.']);
            $user=User::where('email',$email)->lockForUpdate()->first();
            if($user?->isClosed() || $user?->isSuspended()) throw ValidationException::withMessages(['owner_email'=>'This account is not available for ownership assignment.']);
            if(!$user){$user=User::create(['name'=>trim($name),'email'=>$email,'password'=>null]);$send=true;} elseif(!$user->hasPassword()) $send=true;
            $business->memberships()->updateOrCreate(['user_id'=>$user->id],['role'=>'owner','is_primary_owner'=>true]);
            foreach($business->stores as $businessStore) $businessStore->memberships()->updateOrCreate(['user_id'=>$user->id],['role'=>'owner','is_primary_owner'=>true]);
            OwnershipAssignment::create(['business_entity_id'=>$business->id,'store_id'=>$store?->id,'user_id'=>$user->id,'assigned_by_user_id'=>$admin->id,'assignment_type'=>'first_owner','assigned_at'=>now()]);
            return $user;
        });
        if($send&&$sendInvitation) Mail::to($user->email)->send(new SellerOnboardingInvitationMail($user->id,$user->name));
        elseif($store&&$user->hasPassword()) Mail::to($user->email)->send(new StoreOwnerAssignedMail($store,$user));
        elseif($user->hasPassword()) Mail::to($user->email)->send(new BusinessOwnerAssignedMail($business,$user));
        return $user;
    }
}
