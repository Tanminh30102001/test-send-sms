<?php

namespace App\Http\Controllers;

use App\Helpers\ClientIdHelper;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Helpers\SendSMSHelper;
use App\Models\Logsend;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Merchant $merchant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Merchant $merchant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Merchant $merchant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchant $merchant)
    {
        //
    }
    public function sendSMS(Request $request)
    {
        $message = $request->message ?? '';
        $phoneTo = $request->phone_number ?? '';
        $userId = auth()->user()->id;
        $merchant = Merchant::where('user_id', $userId)->first();
        $checkClient = ClientIdHelper::generateClientId($merchant->merchant_no, auth()->user()->email);
        $checkSecret = ClientIdHelper::generateClientId($merchant->merchant_no, auth()->user()->phone);
        if (!$message || !$phoneTo) {
            return response()->json([
                'status' => '400',
                'message' => 'Required message or phone number',
            ], 200);
        }
        if ($checkClient != $merchant->clientID) {
            return response()->json([
                'status' => '400',
                'message' => 'wrong cliendID',
            ], 200);
        }
        if ($checkSecret != $merchant->secretID) {
            return response()->json([
                'status' => '400',
                'message' => 'wrong cliendID',
            ], 200);
        }
        $sendMess = SendSMSHelper::send($phoneTo,$message);
        $logSendSMS= new Logsend();
        $logSendSMS->phone_from=auth()->user()->phone;
        $logSendSMS->phone_to=$phoneTo;
        $logSendSMS->message= $message;
        $logSendSMS->status=$sendMess['IsSent'];
        $logSendSMS->clientID=$checkClient;
        $logSendSMS->secretID=$checkSecret;
        $logSendSMS->message_id=$sendMess['MessageId'];
        
        $logSendSMS->save();
        return $sendMess;
    }
}
