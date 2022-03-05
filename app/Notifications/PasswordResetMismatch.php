<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\SendMailable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;


class PasswordResetMismatch extends Notification implements ShouldQueue
{
	use Queueable;
	public static $toMailCallback;
    public $email;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($email)
	{
		// dd($email);
		$this->email = $email;
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
        $data['mail_type'] = 'email_verify_if_not_exist';
        $data['from'] = env('MAIL_USERNAME');
        $data['to'] = $this->email;
        $data['subject'] = 'Reset Password Request';
        $data['line'] = 'We received a request to reset the password to access Little Laureates with your email address '.$this->email.', but we were unable to find an account associated with this address.<br><br> 
If you use Little Laureates and were expecting this email, consider trying to request a password reset using the email address associated with your account';
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
