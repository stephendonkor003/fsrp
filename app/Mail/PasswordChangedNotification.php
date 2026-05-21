<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newPassword;
    public $changedAt;

    public function __construct($user, $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
        $this->changedAt = now()->format('F j, Y \a\t h:i A');
    }

    public function build()
    {
        return $this->subject('Your Password Has Been Successfully Updated')
                    ->view('emails.auth.password-changed')
                    ->with([
                        'user' => $this->user,
                        'password' => $this->newPassword,
                        'changedAt' => $this->changedAt,
                    ]);
    }
}
