<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MerchantKey extends Model
{

    protected $table = 'wxp_merchant_keys';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id','api_title','api_key', 'created_at','updated_at'
    ];

}
