<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\SendMailable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;


class RegistrationConfirmation extends Notification implements ShouldQueue
{
	use Queueable;
	public static $toMailCallback;
    public $staff;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($staff)
	{
		$this->staff = $staff;
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
        $data['mail_type'] = 'staff_registration_confirmation';
        $data['from'] = env('MAIL_USERNAME');
        $data['to'] = $this->staff->s_email;
        $data['subject'] = 'Registration Confirmation';
        $data['line'] = 'Congratulations, Your account has been successfully created.Please login with following credentials';
        $data['uname'] = $this->staff->s_email;
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
