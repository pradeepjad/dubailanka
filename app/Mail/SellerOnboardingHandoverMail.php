<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerOnboardingHandoverMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $seller) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Continue your Dubai Lanka seller setup');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.seller-onboarding-handover', with: [
            'sellerName' => $this->seller->name,
            'continueUrl' => route('seller.start'),
        ]);
    }

    public function attachments(): array { return []; }
}
