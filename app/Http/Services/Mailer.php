<?php

namespace App\Http\Services;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class Mailer
{
    private Mail $mailer;

    public function __construct()
    {
        $this->mailer = new Mail();
    }

    public function send(string $email, Mailable $template, $cc = null, $bcc = null)
    {
        if ($email) return $this->mailer::to($email)
            ->cc($cc)
            ->bcc($bcc)
            ->send($template);

        return null;
    }

    public function queue_mail(string $email, Mailable $template, $cc = null, $bcc = null)
    {
        if ($email) return $this->mailer::to($email)
            ->cc($cc)
            ->bcc($bcc)
            ->queue($template);

        return null;
    }
}
