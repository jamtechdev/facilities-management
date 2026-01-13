<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeadWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;
    public $password;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Lead $lead, $user = null, $password = null)
    {
        $this->lead = $lead;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Thank You for Your Interest - ' . config('app.name');

        return $this->subject($subject)
                    ->view('emails.lead-welcome')
                    ->with([
                        'lead' => $this->lead,
                        'user' => $this->user,
                        'password' => $this->password,
                        'loginUrl' => route('login'),
                    ]);
    }
}
