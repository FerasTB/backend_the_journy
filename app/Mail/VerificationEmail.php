<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\Variable;
// use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;
    public $user;
    public $code;
    /**
     * Create a new message instance.
     */
    public function __construct() {}

    public function build()
    {
        $to = $this->user->email;

        return $this->view('emails.verification_html')
            ->text('emails.verification_text')
            ->mailersend(
                null, // Template ID if you're using a template
                [
                    new Variable($to, [
                        'name' => $this->user->name,
                        'code' => $this->code
                    ])
                ],
                ['verification'],
                [
                    new Personalization($to, [
                        'name' => $this->user->name,
                        'code' => $this->code,
                    ])
                ]
            );
    }
}
