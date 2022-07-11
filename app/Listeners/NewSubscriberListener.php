<?php

namespace App\Listeners;

use App\Events\NewSubscriberEvent;
use App\Http\Services\Mailer;
use App\Mail\NewSubscriber;
use App\Models\Subscriber;
use App\Models\Website;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewSubscriberListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\NewSubscriberEvent  $event
     * @return void
     */
    public function handle(NewSubscriberEvent $event)
    {
        //
        $res = Website::with('owner:id,email')->where('id', $event->subscriber->website_id)->select('id', 'owner_id')->first();
        $user = $res?->owner?->email ?? null;
        
        
        $subscribingUserRes = Subscriber::with(['user:id,email'])->where('id', $event->subscriber->id)->select(['id', 'user_id'])->first();
        $subscribingUser = $subscribingUserRes?->user?->email ?? null;

        // dd($res, $user, $subscribingUserRes, $subscribingUser);

        $mailer = new Mailer();
        
        $mailer->send($user, new NewSubscriber($subscribingUser ?? $user));
        
        
    }
}
