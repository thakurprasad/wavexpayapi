<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $table = 'wxp_customers';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id','customer_id','name', 'contact','email', 'gstin', 'notes', 'created_at','updated_at'
    ];

}
