<?php

namespace App\Helpers;

use App\Models\Logsend;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SendSMSHelper
{
    private static function getConfig($key, $mode)
    {
        return Config::get("sms.{$mode}.{$key}");
    }

    private static function getTokenUrl($mode)
    {
        return self::getConfig('token_url', $mode);
    }

    private static function getSmsUrl($mode)
    {
        return self::getConfig('sms_url', $mode);
    }

    private static function getClientId($mode)
    {
        return self::getConfig('client_id', $mode);
    }

    private static function getClientSecret($mode)
    {
        return self::getConfig('client_secret', $mode);
    }

    private static function getBrandName()
    {
        return Config::get('sms.brand_name');
    }
    public static function sendSMS($mode)
    {
        // $url = 'https://sandbox.sms.fpt.net/oauth2/token';
        $url = self::getTokenUrl($mode);
        $scope = "send_brandname_otp send_brandname";
        // $client_id = "888a1ad38442ea0d8dcc6B75f843b32FD5d10c88";
        // $client_secret = "f13F32a055082062d9c508ce81292880EF4987e4763e2D55509073872e8423dC4a6ca42d";
        $client_id = self::getClientId($mode);
        $client_secret = self::getClientSecret($mode);
        $sessionId = Str::random(31);
        $grant_type = "client_credentials";
        $arrData = [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'scope' => $scope,
            'grant_type' => $grant_type,
            'session_id' => $sessionId

        ];
        

        $getTokenToSendSMS = Http::post($url, $arrData);
        $token = $getTokenToSendSMS->json();
        $access_token = $token['access_token'];
        $data = [
            'access_token' => $access_token,
            'session_id' => $sessionId
        ];
        return $data;
    }
    public static function send($phone, $message,$mode)
    {
        $url = 'http://sandbox.sms.fpt.net/api/push-brandname-otp';
        $otp = self::sendSMS($mode);
        $uuid = \Ramsey\Uuid\Uuid::uuid4();
        $requestID = $uuid->toString();
        $arrData = [
            'access_token' => $otp['access_token'],
            'session_id' => $otp['session_id'],
            'Phone' => $phone,
            'Message' => base64_encode($message),
            'BrandName' => 'XINTEL.VN',
            "RequestId" => $requestID
        ];
        $sendMessage = Http::post($url, $arrData);

        return $sendMessage;
    }
}
