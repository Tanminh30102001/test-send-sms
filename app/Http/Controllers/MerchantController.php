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
    public function sendSMS(Request $request, $mode)
    {
       
        $message = $request->message ?? '';
        $phoneTo = $request->phone_number ?? '';
        $clientID=$request->cliend_id??'';
        $secret=$request->secret??'';
        $userId = auth()->user()->id;
        $projectId = $request->project_id ?? '';
        $merchant = Merchant::where('user_id', $userId)->first();
        if (!$message || !$phoneTo) {
            return response()->json([
                'status' => '400',
                'message' => 'Required message or phone number',
            ], 200);
        }
        if (! $projectId) {
            return response()->json([
                'status' => '400',
                'message' => 'Required project',
            ], 200);
        }
        if (strlen($phoneTo) > 10 || !preg_match('/^\d+$/', $phoneTo)) {
            return response()->json([
                'status' => '400',
                'message' => 'Invalid phone number. Must be at least 10 digits and contain only numbers.',
            ], 200);
        }
        if ($clientID != $merchant->clientID) {
            return response()->json([
                'status' => '400',
                'message' => 'wrong cliendID',
            ], 200);
        }
        if ($secret != $merchant->secretID) {
            return response()->json([
                'status' => '400',
                'message' => 'wrong cliendID',
            ], 200);
        }
        if (!in_array($mode, ['sandbox', 'production'])) {
            return response()->json([
                'status' => '400',
                'message' => 'Invalid mode specified',
            ], 200);
        }
        $sendMess = SendSMSHelper::send($phoneTo, $message, $mode);
        $logSendSMS = new Logsend();
        $logSendSMS->phone_from = auth()->user()->phone;
        $logSendSMS->phone_to = $phoneTo;
        $logSendSMS->message = $message;
        $logSendSMS->status = $sendMess['IsSent'];
        $logSendSMS->clientID = $clientID;
        $logSendSMS->secretID = $secret;
        $logSendSMS->message_id = $sendMess['MessageId'];
        $logSendSMS->merchant_no = $merchant->merchant_no;
        $logSendSMS->project_id = $projectId;
        $logSendSMS->save();

        return $sendMess;
    }
}
