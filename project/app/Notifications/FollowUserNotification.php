<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Notifications\Messages\BroadcastMessage;

//use App\UserFriend;

class FollowUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $followed_friend;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($followed_friend)
    {
        $this->followed_friend = $followed_friend;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','fcm', 'broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        $user_sending_the_request = $this->followed_friend->user;
        $notification_user_image = $user_sending_the_request->profile_picture ?? null;
            // if ($notification_user_image != null)
            // {
            //     $notification_user_image = secure_url('uploads/profile_pictures', [$notification_user_image]);
            // }
        //dump($notifiable);
        return new BroadcastMessage([
            'notify_channel' => 'User'.$notifiable->id,
            'title'        => env('APP_NAME'),
            'body'         => $user_sending_the_request->first_name.' has followed you',
            'icon'         => env('SITE_URL')."/favicon.ico", // Optional
            'click_action' => env('SITE_URL')."/".$notifiable->username."/friends/follow-list", // Optional
            'notification_id' => $this->id, // Optional
            'notification_type' => get_class(),
            'notification_user_image' => $notification_user_image

        ]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }



    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        //return $this->followed_friend->toArray();
        $user_sending_the_request = $this->followed_friend->user;
        //return $this->followed_friend->toArray();
        return [
            'sender_id' => $user_sending_the_request->id,
            'title' => env('APP_NAME'),
            'body'  => $user_sending_the_request->first_name.' has followed you',
            'icon' => env('SITE_URL')."/favicon.ico",
            'url'  => env('SITE_URL')."/".$notifiable->username."/friends/follow-list",
            'notification_data' => $this->followed_friend->toArray()
        ];
    }

    public function toFcm($notifiable)
    {
        $user_sending_the_request = $this->followed_friend->user;
        // dd($this->followed_friend->toJson(JSON_PRETTY_PRINT));
        //dd($notifiable->user_devices);
        $user_devices = $notifiable->user_devices;
        $user_devices_fcm_tokens = array();
        // dd($user_devices->count());
        $message = new \Benwilkins\FCM\FcmMessage();
        if ( $user_devices->count() > 0 )
        {
            //dd($user_devices->count());
            foreach ($user_devices as $key_user_device => $user_device) {
              array_push($user_devices_fcm_tokens, $user_device->fcm_token);
            }

            //var_dump($user_devices_fcm_tokens);
            $notification_user_image = $user_sending_the_request->profile_picture ?? null;
            if ($notification_user_image != null)
            {
                $notification_user_image = secure_url('uploads/profile_pictures', [$notification_user_image]);
            }

            $message->to($user_devices_fcm_tokens)
                ->content([
                    'title'        => env('APP_NAME'),
                    'body'         => $user_sending_the_request->first_name.' has followed you',
                    'sound'        => '', // Optional
                    'icon'         => env('SITE_URL')."/favicon.ico", // Optional
                    'click_action' => env('SITE_URL')."/".$notifiable->username."/friends/follow-list" // Optional
                ])->data([
                    'notification_id' => $this->id, // Optional
                    'notification_type' => get_class(),
                    'notification_user_image' => $notification_user_image
                ])->priority(\Benwilkins\FCM\FcmMessage::PRIORITY_HIGH); // Optional - Default is 'normal'.
            //var_dump($message);
            return $message;
        }
        //var_dump($message);
        return $message;
    }
}
