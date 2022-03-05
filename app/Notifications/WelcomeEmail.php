<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Mail\SendMailable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class WelcomeEmail extends Notification implements ShouldQueue
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
		$systemInfo = getSystemInfo();
	    $data = array();
        $data['mail_type'] = 'welcome_email';
        $data['from'] = $systemInfo->s_email;
        $data['to'] = $this->user->s_email;
        $data['subject'] = 'Welcome Mail';
        $data['line'] = 'Please follow the link to Login';
        $data['greetings'] = "Hello!";
        $data['end_greetings'] = "Regards,";
        $data['from_user'] = $systemInfo->s_site_name;
        $loginUrl = $this->loginUrl($notifiable);
      
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }
        $data['link']=$loginUrl;
       
        return (new SendMailable($data));	
	}

	  /**
     * Get the login URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function loginUrl($notifiable)
    {
        return URL::to('/').'/login';
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
