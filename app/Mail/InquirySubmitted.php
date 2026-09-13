<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquirySubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry,
        public bool $staffCopy,
    ) {}

    public function envelope(): Envelope
    {
        $requestName = $this->inquiry->inquiry_type === 'contact'
            ? 'contact message'
            : 'packing quote request';

        return new Envelope(
            replyTo: $this->staffCopy
                ? [new Address($this->inquiry->email, $this->inquiry->name)]
                : [],
            subject: $this->staffCopy
                ? "New {$requestName} — {$this->inquiry->reference}"
                : "We received your {$requestName} — {$this->inquiry->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry-submitted',
            text: 'emails.inquiry-submitted-text',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
