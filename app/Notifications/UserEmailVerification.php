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



class UserEmailVerification extends Notification implements ShouldQueue
{
	use Queueable;

	public static $toMailCallback;
    public $user;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(User $user)
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
		// d($notifiable);
		$systemInfo = getSystemInfo();
		if($this->user->i_role_id == 6){
			$data = array();
	        $data['mail_type'] = 'email_verification';
	        $data['from'] = $systemInfo->s_email;
	        $data['to'] = $this->user->s_email;
	        $data['subject'] = 'Verify Mail';
	        $data['greetings'] = "Hello!";
	        $data['line'] = 'Hello! You registered an account on '.$systemInfo->s_site_name.', before being able to use your account you need to verify that this is your email address.';
	        $data['end_greetings'] = "Regards,";
	        $data['from_user'] = $systemInfo->s_site_name;
	        $verificationUrl = $this->verificationUrl($notifiable);
	        // d($verificationUrl);
	        if (static::$toMailCallback) {
	            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
	        }
	        $data['link']=$verificationUrl;
	        $data['contact_email']= $systemInfo->s_email;
	        $data['contact_number']= $systemInfo->s_phone;
	        $data['footer']= $systemInfo->s_footer;
	        
		}else{
			$data = array();
	        $data['mail_type'] = 'email_verification';
	        $data['from'] = $systemInfo->s_email;
	        $data['to'] = $this->user->s_email;
	        $data['subject'] = 'Verify Mail';
	        $data['greetings'] = "Hello!";
	        $data['line'] = 'Hello! You registered an account as on'.$systemInfo->s_site_name.', before being able to use your account you need to verify that this is your email address.';
	        $data['end_greetings'] = "Regards,";
	        $data['from_user'] = $systemInfo->s_site_name;
	        $verificationUrl = $this->verificationUrl($notifiable);
	        // d($verificationUrl);
	        if (static::$toMailCallback) {
	            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
	        }
	        $data['link']=$verificationUrl;
	        $data['contact_email']= $systemInfo->s_email;
	        $data['contact_number']= $systemInfo->s_phone;
	        $data['footer']= $systemInfo->s_footer;
		}
		
       
        return (new SendMailable($data));




	    // $data = array();
     //    $data['mail_type'] = 'email_verify';
     //    $data['from'] = 'antaraghosh81@gmail.com';
     //    $data['to'] = $this->user->s_email;
     //    $data['subject'] = 'Verify Mail';
     //    $data['greetings'] = "Hello!";
     //    $data['line'] = "Please click on the button below to verify your email address.";
     //    $data['end_greetings'] = "Regards,";
     //    $data['from_user'] = "Little Laureates";
     //    $verificationUrl = $this->verificationUrl($notifiable);
     //    d($verificationUrl);
     //    if (static::$toMailCallback) {
     //        return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
     //    }
     //    $data['link']=$verificationUrl;
       
     //    return (new SendMailable($data));				
	}


	 /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
    	$emailEncrypt = encrypt($notifiable->email);

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
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
