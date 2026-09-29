<?php

namespace App\Mail;

use App\Models\BusinessEntity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessOwnerAssignedMail extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public readonly BusinessEntity $business, public readonly User $owner) {}
    public function envelope(): Envelope { return new Envelope(subject: 'You have been assigned as a Dubai Lanka Business owner'); }
    public function content(): Content { return new Content(view:'emails.business-owner-assigned',with:['ownerName'=>$this->owner->name,'businessName'=>$this->business->trading_name ?: $this->business->legal_name,'sellerUrl'=>route('seller.start')]); }
    public function attachments(): array { return []; }
}
