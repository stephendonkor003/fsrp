<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $fullName,
        public string $email
    ) {}

    public function build()
    {
        return $this->subject('Application Received - FSRP Administration')
            ->view('emails.hr.application_received');
    }
}
