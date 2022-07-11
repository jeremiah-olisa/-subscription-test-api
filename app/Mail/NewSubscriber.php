<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewSubscriber extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

    public string $subscriber;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.new-subscriber', ['subscriber' =>  $this->subscriber]);
    }
}
