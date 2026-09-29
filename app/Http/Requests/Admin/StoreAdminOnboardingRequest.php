<?php
namespace App\Http\Requests\Admin;
use App\Models\BusinessEntity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreAdminOnboardingRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->platformAdmin?->status==='active'; }
    public function rules(): array
    {
        $registered=$this->input('business_type')===BusinessEntity::TYPE_REGISTERED;
        $mode=$this->input('ownership_mode');
        $intent=$this->input('submit_intent','complete_setup');
        $needsBusiness=$intent!=='handover';
        $needsStore=$intent==='complete_setup';
        return [
            'submit_intent'=>['required',Rule::in(['handover','business_only','complete_setup'])],
            'ownership_mode'=>['required',Rule::in(['existing','invite','unassigned'])],
            'owner_name'=>[Rule::requiredIf($mode==='invite'),'nullable','string','max:150'],
            'owner_email'=>[Rule::requiredIf(in_array($mode,['existing','invite'],true)),'nullable','email','max:255'],
            'business_type'=>[Rule::requiredIf($needsBusiness),'nullable',Rule::in([BusinessEntity::TYPE_PERSONAL,BusinessEntity::TYPE_REGISTERED])],
            'legal_name'=>[Rule::requiredIf($needsBusiness),'nullable','string','min:2','max:150'],'trading_name'=>['nullable','string','max:150'],
            'registration_number'=>[Rule::requiredIf($needsBusiness&&$registered),'nullable','string','max:100'],
            'business_country_code'=>[Rule::requiredIf($needsBusiness),'nullable',Rule::in(['AE','LK'])],'business_address'=>[Rule::requiredIf($needsBusiness&&$registered),'nullable','string','max:500'],
            'business_phone'=>[Rule::requiredIf($needsBusiness),'nullable','string','max:30'],'business_whatsapp'=>['nullable','string','max:30'],'business_email'=>['nullable','email','max:255'],
            'store_name'=>[Rule::requiredIf($needsStore),'nullable','string','min:2','max:150'],
            'store_slug'=>[Rule::requiredIf($needsStore),'nullable','string','min:3','max:120','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/','unique:stores,slug'],
            'store_country_code'=>[Rule::requiredIf($needsStore),'nullable',Rule::in(['AE','LK'])],'store_city'=>['nullable','string','max:120'],'store_address'=>['nullable','string','max:500'],
            'store_phone'=>[Rule::requiredIf($needsStore),'nullable','string','max:30'],'store_whatsapp'=>['nullable','string','max:30'],'store_email'=>['nullable','email','max:255'],
            'store_website'=>['nullable','url:http,https','max:255'],'store_description'=>['nullable','string','max:2000'],'store_business_hours'=>['nullable','string','max:500'],
        ];
    }
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            if($this->input('submit_intent')==='handover' && $this->input('ownership_mode')==='unassigned') {
                $validator->errors()->add('ownership_mode','Owner Not Assigned cannot be handed over because no seller is identified yet. Continue with the Admin-managed setup instead.');
            }
            if($this->input('submit_intent')==='handover' && $this->input('ownership_mode')==='existing') {
                $validator->errors()->add('ownership_mode','An existing Dubai Lanka account does not need a handover. Select the account and continue the Admin setup instead.');
            }
            if($this->input('submit_intent')!=='complete_setup') return;
            $slug=(string)$this->input('store_slug');
            if($slug===''||$validator->errors()->has('store_slug')) return;
            $historical=\App\Models\StoreSlugHistory::where('slug',$slug)->exists();
            $pending=\App\Models\StoreChangeRequest::where('proposed_slug',$slug)->whereIn('status',['pending','under_review','needs_changes'])->exists();
            if($historical||$pending) $validator->errors()->add('store_slug','This Store URL is already reserved or was previously used. Please choose another one.');
        });
    }
    public function messages(): array
    {
        return ['store_name.required'=>'Please enter the Store name.','store_slug.required'=>'Please choose a Store URL.','store_slug.min'=>'Store URL must contain at least 3 characters.','store_slug.regex'=>'Store URL can use letters, numbers and hyphens only.','store_slug.unique'=>'This Store URL is already in use. Please choose another one.'];
    }
    protected function prepareForValidation(): void
    {
        foreach(['owner_email','business_email','store_email'] as $key) if($this->filled($key)) $this->merge([$key=>mb_strtolower(trim((string)$this->input($key)))]);
        foreach(['business_country_code','store_country_code'] as $key) if($this->filled($key)) $this->merge([$key=>strtoupper(trim((string)$this->input($key)))]);
        if($this->filled('store_slug')) $this->merge(['store_slug'=>strtolower(trim((string)$this->input('store_slug')))]);
        if(!$this->filled('submit_intent')) $this->merge(['submit_intent'=>'complete_setup']);
        if($this->input('ownership_mode')==='unassigned') $this->merge(['owner_name'=>null,'owner_email'=>null]);
    }
}
