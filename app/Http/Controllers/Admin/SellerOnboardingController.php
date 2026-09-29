<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\AssignFirstStoreOwner;
use App\Actions\Admin\CreateAdminSellerSetup;
use App\Actions\Admin\CreateAdminStoreForBusiness;
use App\Actions\Admin\HandOverSellerSetup;
use App\Actions\Store\ChangeStoreStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreForBusinessRequest;
use App\Http\Requests\Admin\AssignFirstOwnerRequest;
use App\Http\Requests\Admin\StoreAdminOnboardingRequest;
use App\Http\Requests\Admin\UpdateAdminManagedStoreRequest;
use App\Models\BusinessEntity;
use App\Models\SellerOnboardingHandover;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SellerOnboardingController extends Controller
{
    public function index(): Response
    {
        $businesses = BusinessEntity::with(['memberships.user','stores.statusHistories'])->latest()->get()->map(function(BusinessEntity $business){
            $membership=$business->memberships->firstWhere('is_primary_owner',true);
            $owner=$membership?->user;
            return [
                'id'=>$business->id,
                'business_name'=>$business->trading_name ?: $business->legal_name,
                'type'=>$business->type,
                'owner'=>$owner?->only(['id','name','email']),
                'owner_state'=>$owner ? ($owner->hasPassword() ? 'assigned' : 'invitation_pending') : 'unassigned',
                'stores'=>$business->stores->map(fn(Store $store)=>[
                    'id'=>$store->id,'name'=>$store->name,'slug'=>$store->slug,'status'=>$store->status,
                    'status_note'=>$store->statusHistories->sortByDesc('id')->first()?->note,
                    'can_resubmit'=>$this->canResubmitAfterNeedsChanges($store),
                ])->values(),
            ];
        });

        $handovers=SellerOnboardingHandover::with('user:id,name,email,password')->where('status',SellerOnboardingHandover::STATUS_PENDING)->latest()->get()->map(fn($h)=>[
            'id'=>$h->id,'ownership_mode'=>$h->ownership_mode,'owner_name'=>$h->owner_name,'owner_email'=>$h->owner_email,
            'state'=>$h->user?->hasPassword() ? 'waiting_for_seller' : 'invitation_pending','created_at'=>$h->created_at?->toISOString(),
        ]);

        return Inertia::render('Admin/Onboarding/Index',['businesses'=>$businesses,'handovers'=>$handovers]);
    }

    public function create(): Response { return Inertia::render('Admin/Onboarding/Create'); }

    public function setupOwnerLookup(): JsonResponse
    {
        $email=mb_strtolower(trim((string)request('email')));
        validator(['email'=>$email],['email'=>['required','email','max:255']])->validate();
        $user=User::where('email',$email)->first();
        if(!$user) return response()->json(['exists'=>false,'email'=>$email]);
        if($user->isClosed()||$user->isSuspended()) return response()->json(['exists'=>true,'available'=>false,'message'=>'This account is not available for seller onboarding.']);
        return response()->json(['exists'=>true,'available'=>true,'email'=>$email,'user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email]]);
    }

    public function store(StoreAdminOnboardingRequest $request, CreateAdminSellerSetup $action, HandOverSellerSetup $handover): RedirectResponse
    {
        $data=$request->validated();
        if($data['submit_intent']==='handover') {
            $handover->execute($request->user(),$data['ownership_mode'],$data['owner_name']??null,$data['owner_email']);
            return to_route('admin.onboarding.index')->with('success','Seller setup handed over successfully. Dubai Lanka will keep it visible here until the seller creates their Business Entity.');
        }
        $business=$action->execute($request->user(),$data);
        $message=$data['submit_intent']==='business_only' ? "{$business->legal_name} was created. You can add a Store later." : "{$business->legal_name} and its first Store were created successfully.";
        return to_route('admin.onboarding.index')->with('success',$message);
    }

    public function createStore(BusinessEntity $business): Response
    {
        return Inertia::render('Admin/Onboarding/AddStore',['business'=>['id'=>$business->id,'name'=>$business->trading_name ?: $business->legal_name]]);
    }

    public function storeForBusiness(AdminStoreForBusinessRequest $request, BusinessEntity $business, CreateAdminStoreForBusiness $action): RedirectResponse
    {
        $store=$action->execute($business,$request->validated());
        return to_route('admin.onboarding.index')->with('success',"{$store->name} was created as a Draft Store.");
    }

    public function edit(Store $store): Response
    {
        $this->ensureAdminManagedEditable($store); $store->load(['businessEntity:id,legal_name,trading_name','statusHistories'=>fn($q)=>$q->limit(1)]);
        return Inertia::render('Admin/Onboarding/EditStore',['store'=>$store,'statusNote'=>$store->statusHistories->first()?->note]);
    }
    public function update(UpdateAdminManagedStoreRequest $request, Store $store): RedirectResponse
    {
        $this->ensureAdminManagedEditable($store); $store->fill($request->validated());
        if(!$store->isDirty()) return back()->withErrors(['store'=>'No changes were detected. Update at least one Store field before resubmitting for review.']);
        $store->save(); if($store->status===Store::STATUS_NEEDS_CHANGES) $store->forceFill(['review_changes_saved_at'=>now()])->saveQuietly();
        return to_route('admin.onboarding.edit-store',$store)->with('success','Store updated successfully.');
    }
    public function submit(Store $store, ChangeStoreStatus $action): RedirectResponse
    {
        abort_unless(in_array($store->status,[Store::STATUS_DRAFT,Store::STATUS_NEEDS_CHANGES],true),422,'Only Draft or Needs Changes stores can be submitted for review.');
        if($store->status===Store::STATUS_NEEDS_CHANGES) abort_unless($this->canResubmitAfterNeedsChanges($store),422,'Please edit and save at least one Store field after the Changes Required request before resubmitting.');
        $action->execute($store,Store::STATUS_PENDING,request()->user(),'Submitted for review from Admin Seller Onboarding.');
        return back()->with('success',"{$store->name} was submitted for review.");
    }
    private function ensureAdminManagedEditable(Store $store): void { abort_unless(in_array($store->status,[Store::STATUS_DRAFT,Store::STATUS_NEEDS_CHANGES],true),403,'This Store cannot be edited from onboarding while it is in review.'); }
    private function canResubmitAfterNeedsChanges(Store $store): bool { return $store->status===Store::STATUS_NEEDS_CHANGES && $store->review_changes_saved_at!==null; }

    public function ownerLookup(Store $store): JsonResponse { return $this->lookupOwnerForBusiness($store->businessEntity); }
    public function businessOwnerLookup(BusinessEntity $business): JsonResponse { return $this->lookupOwnerForBusiness($business); }
    private function lookupOwnerForBusiness(BusinessEntity $business): JsonResponse
    {
        abort_unless(!$business->memberships()->where('is_primary_owner',true)->exists(),422,'This Business already has a Primary Owner.');
        $email=mb_strtolower(trim((string)request('email'))); validator(['email'=>$email],['email'=>['required','email','max:255']])->validate();
        $user=User::where('email',$email)->first(); if(!$user) return response()->json(['exists'=>false,'email'=>$email]);
        if($user->isClosed()||$user->isSuspended()) return response()->json(['exists'=>true,'available'=>false,'message'=>'This account is not available for ownership assignment.']);
        return response()->json(['exists'=>true,'available'=>true,'email'=>$email,'user'=>['name'=>$user->name,'email'=>$user->email],'has_password'=>$user->hasPassword()]);
    }
    public function assign(AssignFirstOwnerRequest $request, Store $store, AssignFirstStoreOwner $action): RedirectResponse { return $this->assignBusinessOwner($request,$store->businessEntity,$action,$store); }
    public function assignBusiness(AssignFirstOwnerRequest $request, BusinessEntity $business, AssignFirstStoreOwner $action): RedirectResponse { return $this->assignBusinessOwner($request,$business,$action,null); }
    private function assignBusinessOwner(AssignFirstOwnerRequest $request,BusinessEntity $business,AssignFirstStoreOwner $action,?Store $store): RedirectResponse
    {
        $data=$request->validated(); $existing=User::where('email',$data['owner_email'])->first(); $name=$existing?->name??$data['owner_name'];
        $action->execute($business,$store,$name,$data['owner_email'],$request->user());
        return back()->with('success','Primary Owner assigned to the Business and all existing Stores successfully.');
    }
}
