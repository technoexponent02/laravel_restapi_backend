<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Notifications\Events\NotificationSent;
use Log;


class LogNotification
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
     * @param  object  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        $content = json_encode($event->response);
        //mail("saibal@technoexponent.com","My subject",$content);

        Log::debug($content);
        
    }
}
