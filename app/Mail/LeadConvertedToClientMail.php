<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeadConvertedToClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leadData;
    public $client;

    /**
     * Create a new message instance.
     *
     * @param array $leadData Array containing lead information (name, company, email, phone)
     * @param Client $client The client that was created from the lead
     */
    public function __construct(array $leadData, Client $client)
    {
        $this->leadData = $leadData;
        $this->client = $client;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Lead Converted to Client - ' . config('app.name');

        return $this->subject($subject)
                    ->view('emails.lead-converted-to-client')
                    ->with([
                        'leadData' => $this->leadData,
                        'client' => $this->client,
                    ]);
    }
}
