<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $name;
    private string $otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct(string $name, string $otp)
    {
        $this->name = $name;
        $this->otp = $otp;
        $this->subject('Welcome to Substribe, ' . $name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.welcome', [
            'name' => $this->name,
            'otp' => $this->otp
        ]);
    }
}
