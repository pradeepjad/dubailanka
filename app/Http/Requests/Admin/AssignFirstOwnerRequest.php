<?php
namespace App\Http\Requests\Admin;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
class AssignFirstOwnerRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->platformAdmin?->status==='active'; }
    public function rules(): array { return ['owner_name'=>['nullable','string','max:150'],'owner_email'=>['required','email','max:255']]; }
    protected function prepareForValidation(): void { $this->merge(['owner_name'=>trim((string)$this->input('owner_name')) ?: null,'owner_email'=>mb_strtolower(trim((string)$this->input('owner_email')))]); }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function(Validator $validator){
            if($validator->errors()->has('owner_email')) return;
            if(!User::where('email',$this->input('owner_email'))->exists() && !$this->input('owner_name')) {
                $validator->errors()->add('owner_name','Please enter the seller name for the new Dubai Lanka account.');
            }
        });
    }
}
