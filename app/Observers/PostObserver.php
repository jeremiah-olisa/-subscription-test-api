<?php

namespace App\Observers;

use App\Http\Services\Mailer;
use App\Models\Post;
use App\Mail\PostCreated;
use App\Models\Subscriber;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function created(Post $post)
    {
        //
        $mailer = new Mailer();

        $subscribers = Subscriber::with('user:id,email')->where('website_id', $post->website_id)->select(['user_id'])->get();

        $mails = [];

        foreach ($subscribers as $user) {
            array_push($mails, $user['user']['email']);
        }

        // dd($mails);

        foreach ($mails as $mail) {
            $mailer->send($mail, new PostCreated($post));
        }
    }


    /**
     * Handle the Post "updated" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function updated(Post $post)
    {
        //
    }

    /**
     * Handle the Post "deleted" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function deleted(Post $post)
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function restored(Post $post)
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function forceDeleted(Post $post)
    {
        //
    }
}
