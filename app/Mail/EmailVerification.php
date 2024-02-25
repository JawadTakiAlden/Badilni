<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function build()
    {
        return $this->from(env("MAIL_USERNAME"))->subject('Registration Code!')
            ->view('mails.activation')
            ->with('data', $this->data);
    }
}
