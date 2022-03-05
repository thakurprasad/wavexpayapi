<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use App\Mail\SendMailable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;



class PasswordResetRequest extends Notification implements ShouldQueue
{
	use Queueable;

	public static $toMailCallback;
    public $user;
    public $token;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($user,$token)
	{
		$this->user = $user;
		$this->token = $token;
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
		$link = 'https://'.\Config::get('school_domain');
	    $data = array();
        $data['mail_type'] = 'password_reset_request';
        $data['from'] = env('MAIL_USERNAME');
        $data['to'] = $this->user->s_email;
        $data['subject'] = __('Reset Password Request');
        $data['greetings'] = "Hello!";
        $data['line'] = "Use the button below to reset it.";
        $data['end_greetings'] = "Regards,";
        $data['from_user'] = "Little Laureates";
        //$resetUrl = $this->resetUrl($notifiable);
		$resetUrl = $link.'/password/reset/'.$this->token;
       
        $data['link']=$resetUrl;
        return (new SendMailable($data));				
	}


	 /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {    	

        return URL::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'email' => $notifiable->email,
                'token' => $this->token,
            ]
        );
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
