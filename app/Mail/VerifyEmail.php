<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;
use Carbon\Carbon;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email Verification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $to = Arr::get($this->to, '0.address');

        // Use MailerSend API features
        $this->mailersend(
            template_id: null, // Replace with your MailerSend template ID if you have one
            tags: ['email_verification'],
            personalization: [
                new Personalization($to, [
                    'name' => $this->user->name,
                    'verification_link' => url('/api/verify-email/' . $this->user->verification_code),
                ])
            ],
            precedenceBulkHeader: true,
            sendAt: Carbon::now(),
        );

        return new Content(
            view: 'emails.verify_email',
            with: [
                'user' => $this->user,
                'verification_link' => url('/api/verify-email/' . $this->user->verification_code),
            ],
        );
    }
}
