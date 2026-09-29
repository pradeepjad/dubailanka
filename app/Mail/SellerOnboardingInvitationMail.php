<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
class SellerOnboardingInvitationMail extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public readonly int $sellerUserId, public readonly string $sellerName) {}
    public function envelope(): Envelope { return new Envelope(subject:'Your Dubai Lanka seller account is ready to claim'); }
    public function content(): Content
    {
        return new Content(view:'emails.seller-onboarding-invitation',with:[
            'sellerName'=>$this->sellerName,
            'claimUrl'=>URL::temporarySignedRoute('seller.invitation.claim',now()->addDays(7),['user'=>$this->sellerUserId]),
        ]);
    }
    public function attachments(): array { return []; }
}
