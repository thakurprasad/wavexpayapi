<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{

    protected $table = 'wxp_merchants';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_name','contact_name', 'contact_phone','status', 'created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'access_salt',
    ];

}
