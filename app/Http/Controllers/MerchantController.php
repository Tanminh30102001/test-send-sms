<?php

namespace App\Http\Controllers;

use App\Helpers\ClientIdHelper;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Helpers\SendSMSHelper;
use App\Models\Logsend;
use App\Models\Project;
use App\Repositories\MerchantRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class MerchantController extends Controller
{
    protected $merchantRepository;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }
    public function index()
    {
        $merchant=$this->merchantRepository->all();
       
        return response()->json([
            'status' => '200',
            'merchant' => $merchant,
        ], 200);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password'=>'required|string|max:255',
            'merchant_name'=>'required|string|max:255',
            'merchant_phone' => 'required|string|max:10|regex:/^\d+$/|unique:merchant',
            'email' => 'required|string|email|max:255|unique:merchant',
            'merchant_url'=>'required|string|max:255',
        ]);

        $user = Auth::user();
        $data = [
            'name' => $request->name,
            'merchant_no' => 'XIN' . mt_rand(100000, 999999),
            'password' => Hash::make($request->password),
            'merchant_name' => $request->merchant_name,
            'merchant_phone' => $request->merchant_phone,
            'secretID'=>ClientIdHelper::generateKey(),
            'email' => $request->email,
            'merchant_url' => $request->merchant_url,
            'created_by' => $user->id,
            'clientID'=>ClientIdHelper::generateClientId(),
            'API_KEY'=>ClientIdHelper::generateSecretAuto()
        ];
        $merchant = $this->merchantRepository->create($data);
        return response()->json([
            'status' => '200',
            'merchant' => $merchant,
        ], 200);
    }
    public function show($merchant_no)
    {
        $merchant = $this->merchantRepository->findByKey('merchant_no',$merchant_no);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
        return response()->json([
            'status' => '200',
            'merchant' => $merchant,
        ], 200);
    }
    public function edit( $merchant_no,Request $request)
    {
         $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'merchant_phone' => 'sometimes|required|string|max:10|regex:/^\d+$/|unique:merchant',
            'email' => 'sometimes|required|string|email|max:255|unique:merchant,email,',
            'password' => 'sometimes|required|string|min:6', 
            'merchant_url'=>'sometimes|required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'merchant_phone.required' => 'Vui lòng nhập số điện thoại.',
            'merchant_phone.regex' => 'Vui lòng nhập số điện thoại hợp lệ.',
            'merchant_phone.max' => 'Vui lòng nhập số điện thoại có tối đa 10 số.',
            'merchant_phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Vui lòng nhập email hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'merchant_url.required' => 'Vui lòng nhập URL.',
            'merchant_url.max' => 'URL không được vượt quá 255 ký tự.',
        ]);

        $merchant = $this->merchantRepository->findByKey('merchant_no',$merchant_no);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        $data = $request->all();
        if (isset($data['password'])) {
            $data['password'] =Hash::make($data['password']); 
        }

        $updatedMerchant = $this->merchantRepository->update($data,$merchant->id);
        return response()->json([
            'status' => '200',
            'message'=>'Cập nhập thành công ',
            'merchant' => $updatedMerchant,
        ], 200);
    }
    public function destroy( $merchant_no)
    {
        $merchant = $this->merchantRepository->findByKey('merchant_no',$merchant_no);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
        $this->merchantRepository->delete($merchant->id);
        return response()->json([
            'status' => '200',
            'message'=>'Xóa thành công ',
            'merchant' =>  $this->merchantRepository->delete($merchant->id),
        ], 200);
    }
    public function sendSMS(Request $request, $mode)
    {
       
       
        $message = $request->message ?? '';
        $phoneTo = $request->phone_number ?? '';
        $clientID=$request->cliend_id??'';
        $api_key=$request->API_KEY??'';

        $message = $request->message ?? '';
        $phoneTo = $request->phone_number ?? '';
        $clientID=$request->cliend_id??'';
        $secret=$request->secret??'';
        $userId = auth()->user()->id;
     
        $merchant =  $this->merchantRepository->find($userId);
        if (!$message || !$phoneTo) {
            return response()->json([
                'status' => '400',
                'message' => 'Required message or phone number',
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
        if ($api_key != $merchant->API_KEY) {
            return response()->json([
                'status' => '400',
                'message' => 'wrong API KEY',
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
        $logSendSMS->message_id = $sendMess['MessageId'];
        $logSendSMS->merchant_no = $merchant->merchant_no;
        $logSendSMS->enviroment=$mode;
        $logSendSMS->save();
        return $sendMess;
    }
    public function merchantLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (empty($credentials['email']) || empty($credentials['password'])) {
            return response()->json([
                'status' => '400',
                'message' => 'Login failed',
                'errors' => 'Required email or password',
            ], 200);
        }

        if (Auth::guard('merchant')->attempt($credentials)) {
            $merchant = Auth::guard('merchant')->user();
            $token = $merchant->createToken('authToken')->plainTextToken;
            return response()->json([
                'status' => '200',
                'token' => $token,
                'merchant' => $merchant
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function updateByMerchant(Request $request)
{
    try {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'merchant_phone' => 'sometimes|required|string|max:10|regex:/^\d+$/|unique:merchant,merchant_phone,' . auth()->id(),
            'email' => 'sometimes|required|string|email|max:255|unique:merchant,email,' . auth()->id(),
            'password' => 'sometimes|required|string|min:6',
            'merchant_url' => 'sometimes|required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'merchant_phone.required' => 'Vui lòng nhập số điện thoại.',
            'merchant_phone.regex' => 'Vui lòng nhập số điện thoại hợp lệ.',
            'merchant_phone.max' => 'Vui lòng nhập số điện thoại có tối đa 10 số.',
            'merchant_phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Vui lòng nhập email hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'merchant_url.required' => 'Vui lòng nhập URL.',
            'merchant_url.max' => 'URL không được vượt quá 255 ký tự.',
        ]);

        // Get the authenticated merchant
        $merchant = auth()->user();
        $data = $request->all();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $updatedMerchant = $this->merchantRepository->update($data, $merchant->id);

        return response()->json([
            'status' => '200',
            'message' => 'Cập nhập thành công',
            'merchant' => $updatedMerchant,
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => '400',
            'message' => 'Validation Error',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'status' => '500',
            'message' => 'Internal Server Error',
            'errors' => $e->getMessage(),
        ], 500);
    }
}

}
