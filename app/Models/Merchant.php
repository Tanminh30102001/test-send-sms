<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\Merchant as Authenticatable;
class Merchant extends Authenticatable
{
   use HasFactory, HasApiTokens;
    protected $table='merchant';
    protected $fillable = [
        'email',
        'secretID',
        'clientID',
        'merchant_no',
        'password',
        'merchant_name',
        'merchant_phone',
        'merchant_url',
        'created_by',
        'API_KEY'
    ];
    protected $hidden = [
        'password',
    ];
    public $timestamps=true;
    
}
