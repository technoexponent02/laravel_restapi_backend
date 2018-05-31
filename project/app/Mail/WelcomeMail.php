<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

use App\EmailTemplate;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * The user instance.
     *
     * @var User
     */
    public $user;
    
        /**
         * The reset password link
         *
         * @var string
         */
    // public $confirm_link;

    /**
     * The email_template instance.
     *
     * @var User
     */
    public $email_template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
        // $this->confirm_link = $confirm_link;
        $this->email_template = EmailTemplate::find(3);
        $this->subject = $this->email_template->email_subject ?? 'Welcome Mail';
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.welcome_mail');
    }
}
