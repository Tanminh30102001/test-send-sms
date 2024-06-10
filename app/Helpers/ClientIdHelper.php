<?php

namespace App\Helpers;
use Illuminate\Support\Str;
class ClientIdHelper
{
    public static function generateClientId()
    {

        return strtolower(Str::random(40));
    }
    public static function generateSecretAuto(){
        return strtolower(Str::random(72));
    }
    public static function generateKey(){
        return strtolower(Str::random(16));
    }
}
