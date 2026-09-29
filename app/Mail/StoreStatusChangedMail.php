<?php

namespace App\Mail;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Store $store,
        public readonly string $status,
        public readonly ?string $note = null,
    ) {}

    public function envelope(): Envelope
    {
        $label = match ($this->status) {
            Store::STATUS_NEEDS_CHANGES => 'Changes requested',
            Store::STATUS_APPROVED => 'Store approved',
            Store::STATUS_REJECTED => 'Store rejected',
            Store::STATUS_SUSPENDED => 'Store suspended',
            default => 'Store status updated',
        };

        return new Envelope(subject: "{$label}: {$this->store->name}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.store-status-changed');
    }

    public function attachments(): array
    {
        return [];
    }
}
