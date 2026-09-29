<?php
namespace App\Actions\Admin;
use App\Models\BusinessEntity;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class CreateAdminSellerSetup
{
    public function __construct(private readonly AssignFirstStoreOwner $assignOwner, private readonly CreateAdminStoreForBusiness $createStore) {}
    public function execute(User $admin,array $data): BusinessEntity
    {
        if($data['ownership_mode']==='existing' && !User::where('email',$data['owner_email'])->exists()) throw ValidationException::withMessages(['owner_email'=>'No Dubai Lanka account exists for this email. Choose Invite Seller instead.']);
        return DB::transaction(function() use($admin,$data){
            $business=BusinessEntity::create(['type'=>$data['business_type'],'legal_name'=>$data['legal_name'],'trading_name'=>$data['trading_name']??null,'registration_number'=>$data['registration_number']??null,'country_code'=>$data['business_country_code'],'address'=>$data['business_address']??null,'phone'=>$data['business_phone'],'whatsapp'=>$data['business_whatsapp']??null,'email'=>$data['business_email']??null,'status'=>'active']);
            $store=null;
            if(($data['submit_intent']??'complete_setup')==='complete_setup') $store=$this->createStore->execute($business,$data);
            if($data['ownership_mode']!=='unassigned') $this->assignOwner->execute($business,$store,$data['owner_name']??'Dubai Lanka Seller',$data['owner_email'],$admin,$data['ownership_mode']==='invite');
            return $business->fresh(['stores','memberships.user']);
        });
    }
}
