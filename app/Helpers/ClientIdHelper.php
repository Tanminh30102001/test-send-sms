<?php

namespace App\Helpers;

class ClientIdHelper
{
    public static function generateClientId($merchantNo,$emailOrphone)
    {
        $clientId = md5($merchantNo . $emailOrphone);
        return $clientId;
    }

}
