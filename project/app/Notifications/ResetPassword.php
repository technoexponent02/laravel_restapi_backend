<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

use App\EmailTemplate;
use App\User;

class ResetPassword extends Notification
{
    public $token;

    public $email_template;

    public function __construct($token)
    {
        $this->token = $token;
        $this->email_template = EmailTemplate::find(2);
        
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
    	$reset_url = env('SITE_URL').'/password/reset/'.$this->token;

        $user = User::where('reset_verification_token', '=', $this->token )->first();

        return (new MailMessage)
            ->subject($this->email_template->email_subject ?? 'Reset Password')
            ->view('emails.reset', [
                'email_template' => $this->email_template,
                'reset_url' => $reset_url,
                'user' => $user
            ] );
            // ->line('You are receiving this email because we received a password reset request for your account.')
            // ->action('Reset Password', $reset_url)
            // ->line('If you did not request a password reset, no further action is required.');
    }
}