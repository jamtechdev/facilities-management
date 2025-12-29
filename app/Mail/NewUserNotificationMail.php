<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $newUser;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct(User $newUser, string $role)
    {
        $this->newUser = $newUser;
        $this->role = $role;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'New ' . ucfirst($this->role) . ' Registration - ' . config('app.name');

        return $this->subject($subject)
                    ->view('emails.new-user-notification')
                    ->with([
                        'newUser' => $this->newUser,
                        'role' => ucfirst($this->role),
                    ]);
    }
}
