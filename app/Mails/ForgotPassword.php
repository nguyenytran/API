<?php

namespace App\Mails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * User model
     * @var User
     */
    public $user;

    /**
     * Reset token
     * @var string
     */
    public $token;

    /**
     * Reset mail
     * @var string
     */
    public $email;

    /**
     * ForgotPassword constructor.
     * @param User $user
     * @param string $token
     * @param string $email
     */
    public function __construct(User $user, string $token, string $email)
    {
        $this->user = $user;
        $this->token = $token;
        $this->email = $email;
        $this->subject('Reset Password');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.users.reset_password');
    }
}
