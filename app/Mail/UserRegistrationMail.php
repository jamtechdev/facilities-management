<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password, string $role)
    {
        $this->user = $user;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Welcome to ' . config('app.name') . ' - Your Account Credentials';

        return $this->subject($subject)
                    ->view('emails.user-registration')
                    ->with([
                        'user' => $this->user,
                        'password' => $this->password,
                        'role' => ucfirst($this->role),
                        'loginUrl' => route('login'),
                    ]);
    }
}
