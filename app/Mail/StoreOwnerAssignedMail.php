<?php

namespace App\Mail;

use App\Models\Store;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreOwnerAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Store $store, public readonly User $owner) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "You've been assigned as Primary Owner of {$this->store->name}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.store-owner-assigned');
    }

    public function attachments(): array
    {
        return [];
    }
}
