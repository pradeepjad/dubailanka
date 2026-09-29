<?php

namespace App\Mail;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreSubmittedForReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Store $store, public readonly bool $resubmission = false) {}

    public function envelope(): Envelope
    {
        $action = $this->resubmission ? 'resubmitted' : 'submitted';
        return new Envelope(subject: "{$this->store->name} was {$action} for review");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.store-submitted-for-review');
    }

    public function attachments(): array
    {
        return [];
    }
}
