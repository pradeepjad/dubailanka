<?php

namespace Tests\Feature\Auth;

use App\Actions\Admin\AssignFirstStoreOwner;
use App\Models\BusinessEntity;
use App\Models\PlatformAdmin;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SellerInvitationClaimTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_invitation_starts_otp_claim_without_retyping_name_or_email(): void
    {
        $admin=User::factory()->create();
        PlatformAdmin::create(['user_id'=>$admin->id,'status'=>'active']);
        $business=BusinessEntity::create(['type'=>'personal_business','legal_name'=>'Vertex','country_code'=>'AE','phone'=>'0550000000','email'=>null,'status'=>'active']);
        $store=Store::create(['business_entity_id'=>$business->id,'name'=>'Vertex','slug'=>'vertex','country_code'=>'AE','phone'=>'0550000000','email'=>null,'status'=>Store::STATUS_NEEDS_CHANGES]);

        $owner=app(AssignFirstStoreOwner::class)->execute($business,$store,'Sameera','sameera@example.com',$admin,false);

        $url=URL::temporarySignedRoute('seller.invitation.claim',now()->addHour(),['user'=>$owner->id]);
        $this->get($url)->assertRedirect('/register');

        $this->assertSame('Sameera',session('pending_registration_name'));
        $this->assertSame('sameera@example.com',session('pending_registration_email'));
        $this->assertTrue(session('seller_invitation_claim'));
    }
}
