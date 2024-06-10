<?php

namespace App\Http\Controllers;
use App\Helpers\ClientIdHelper;
use App\Events\UserRegistered;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|min:6',
            'phone' => ['required', 'string', 'max:15', 'regex:/^[0-9]+$/', 'unique:users,phone'],
        ];

        // Thông báo lỗi tùy chỉnh
        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'Email has already been taken',
            'phone.unique' => 'Phone number has already been taken',
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name cannot exceed 255 characters',
            'phone.required' => 'Phone number is required',
            'phone.string' => 'Phone number must be a string',
            'phone.max' => 'Phone number cannot exceed 11 characters',
            'phone.regex' => 'Phone number must contain only digits',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ];
        try {
            $validatedData = $request->validate($rules, $messages);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            // event(new UserRegistered($user));
            return response()->json([
                'status' => '200',
                'message' => 'User created successfully',
                'data' => $user,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => '400',
                'message' => 'User created failed',
                'errors' => $e->errors(),
            ], 200);
        }
    }
    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');
    //     if ($request->email === "" || $request->pasword === "") {
    //         return response()->json([
    //             'status' => '400',
    //             'message' => 'Login failed',
    //             'errors' => 'Required email or password',
    //         ], 200);
    //     }
    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();
    //         $token = $user->createToken('authToken')->plainTextToken;
    //         return response()->json(['token' => $token], 200);
    //     } else {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }
    // }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (empty($credentials['email']) || empty($credentials['password'])) {
            return response()->json([
                'status' => '400',
                'message' => 'Login failed',
                'errors' => 'Required email or password',
            ], 200);
        }

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'status' => '200',
                'token' => $token,
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function generateClient(Request $request){
        $params=$request->all();
        $merchant_no=$params['merchant_no']??'';
        $email=$params['email']??"";
        
        if(!$merchant_no||!$email){
            return response()->json([
                'status' => '400',
                'message' => 'Required merchantID or email',
            ], 200);
        }
        return ClientIdHelper::generateClientId( $merchant_no, $email);
    }
    public function generateSecret(Request $request){
        $params=$request->all();
        $merchant_no=$params['merchant_no']??'';
        $phone=$params['phone']??"";
        
        if(!$merchant_no||!$phone){
            return response()->json([
                'status' => '400',
                'message' => 'Required message or phone number',
            ], 200);
        }
        return ClientIdHelper::generateClientId( $merchant_no, $phone);
    }
}
