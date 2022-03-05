<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\SendMailable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;


class PasswordResetSuccess extends Notification implements ShouldQueue
{
	use Queueable;
	public static $toMailCallback;
    public $user;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{

	    $data = array();
        $data['mail_type'] = 'email_verified';
        $data['from'] = env('MAIL_USERNAME');
        $data['to'] = $this->user->s_email;
        $data['subject'] = 'Password Reset Successful';
        $data['line'] = 'Your password reset has been completed.
		If you did not initiate the password reset, immediately contact administrator';
        $data['greetings'] = "Hello!";
        $data['end_greetings'] = "Regards,";
        $data['from_user'] = "Little Laureates";
      
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }
       
        return (new SendMailable($data));	
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
