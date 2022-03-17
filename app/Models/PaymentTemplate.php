<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentTemplate extends Model
{

    protected $table = 'wxp_payment_templates';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title','subtitle','bg_image', 'status', 'description', 'created_at','updated_at'
    ];

}
