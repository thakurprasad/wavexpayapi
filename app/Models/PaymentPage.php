<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentPage extends Model
{

    protected $table = 'wxp_merchant_payment_pages';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_id', 'merchant_id', 'page_title', 'page_content', 'status', 'fb_link', 'twitter_link', 'whatsapp', 'support_email', 'support_phone', 'term_conditions', 'payment_form_json', 'custom_url', 'theme', 'is_page_expiry', 'expiry_date', 'successful_custom_message', 'successful_redirect_url', 'facebook_pixel', 'google_analytics', 'created_at', 'updated_at'
    ];

}
