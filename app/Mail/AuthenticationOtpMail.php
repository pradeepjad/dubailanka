<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthenticationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $purpose,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectForPurpose(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.authentication-otp',
            with: [
                'code' => $this->code,
                'purpose' => $this->purpose,
                'heading' => $this->headingForPurpose(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function subjectForPurpose(): string
    {
        return match ($this->purpose) {
            'registration' => 'Verify your Dubai Lanka account',
            'login' => 'Your Dubai Lanka login code',
            'email_change' => 'Verify your new email address',
            'invitation' => 'Your Dubai Lanka invitation code',
            default => 'Your Dubai Lanka verification code',
        };
    }

    private function headingForPurpose(): string
    {
        return match ($this->purpose) {
            'registration' => 'Verify your email address',
            'login' => 'Your login code',
            'email_change' => 'Verify your new email address',
            'invitation' => 'Complete your invitation',
            default => 'Your verification code',
        };
    }
}
