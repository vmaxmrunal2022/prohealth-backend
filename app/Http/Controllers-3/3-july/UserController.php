<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
// use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Facade;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\DB;
// use App\getUserData;

use Illuminate\Support\Facades\Cache;

use Illuminate\Redis\Connections\Connection;


class UserController extends Controller
{
    protected $user;
    protected $redis;
    public function __construct()
    {
        $this->middleware("auth:api", ["except" => ["login", "register",'resetPassword']]);
        $this->user = new User;
        $this->middleware('throttle:3,1')->only('login');
    }

    public function register(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        $data = [
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ];
        $this->user->create($data);
        $responseMessage = "Registration Successful";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }
    public function login(Request $request)
    {

        //return Session::get('user');
        // return FacadesAuth::user();
        $prefix = '$2y$';
        $cost = '10';
        $salt = '$thisisahardcodedsalt$';
        $blowfishPrefix = $prefix . $cost . $salt;
        $password = $request->USER_PASSWORD;
        $hash = crypt($password, $blowfishPrefix);
        $hashToThirdParty = substr($hash, -32);
        $hashFromThirdParty = $hashToThirdParty;
        // dd($blowfishPrefix);
        // $pw = $request->USER_PASSWORD;
        $hashed = Hash::make($hashFromThirdParty);
        $check_password = Hash::check($hashFromThirdParty, $hashed);
        if ($check_password) {
            $user_password = $hashFromThirdParty;
        } else {
            $user_password = null;
        }
        // print_r($hashFromThirdParty);
        $validator = FacadesValidator::make($request->all(), [
            'USER_ID' => 'required|string',
            'USER_PASSWORD' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }


        $credentials = $request->only(["USER_ID", "USER_PASSWORD"]);
        // $user = User::where('USER_ID', $credentials['USER_ID'])->first();



        $user = User::where('USER_ID', $request->USER_ID)
            // ->where('USER_PASSWORD', $request->USER_PASSWORD)
            ->where('USER_PASSWORD', $user_password)
            ->first();
        //return $user;
        // dd($user);
        // exit();
        // return response()->json([
        //     'data' => Auth::check(),
        //     "success" => false,
        //     "message" => '',
        //     "error" => ''
        // ], 422);

        // if ($user) {
        //     Auth::loginUsingId($user->id);
        //     // -- OR -- //
        //     Auth::login($user);
        //     return redirect()->route('account');
        // } else {
        //     return redirect()->back()->withInput();
        // }

        if ($user) {
            FacadesAuth::login($user);

            // FacadesAuth::loginUsingId($user->id);
            // print_r(Auth::login($user));
            if (!FacadesAuth::check()) {

                $responseMessage = "Invalid username or password";
                return response()->json([
                    "success" => false,
                    "message" => $responseMessage,
                    "error" => $responseMessage
                ], 422);
            }

            //$accessToken = auth()->user()->createToken('authToken')->accessToken;
            $accessToken = '';
            $user1 = FacadesAuth::user();
            //return $user1;
            $responseMessage = "Login Successful";
            Session::put('user', auth()->user()->user_id);
            $userid = auth()->user()->user_id;
            // $a = getUserData($userid);
            // app()->instance('my_global_data', $userid);
            // $usersData =
            $usersData = auth()->user()->user_id;
            // $usersData = auth()->user();

            Cache::put('userId', $usersData, 86400);
            // return Cache::get('users.active');

            // Session::put('login_id', auth()->user()->user_id);
            // define('USER_ID', auth()->user()->user_id);
            //config(['user_id' => auth()->user()->user_id]);

            // return auth()->user()->user_id;

            // return $this->respondWithToken($accessToken, $responseMessage, ['name' => auth()->user()->user_id]);
            return $this->respondWithToken($accessToken, $responseMessage, $usersData);

        } else {
            $responseMessage = "Sorry, this user does not exist";
            return response()->json([
                "success" => false,
                "message" => $responseMessage,
                "error" => $responseMessage
            ], 422);
        }
    }
    public function viewProfile()
    {
        $responseMessage = "user profile";
        $data = FacadesAuth::guard("api")->user();
        return response()->json([
            "success" => true,
            "message" => $responseMessage,
            "data" => $data
        ], 200);
    }
    public function logout()
    {
        $user = FacadesAuth::guard("api")->user()->token();
        Cache::flush();

        $user->revoke();
        $responseMessage = "successfully logged out";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }

    public function resetPassword(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'user_id' => 'required',
            'new_password' => 'required|string|min:8',
            'confirm_new_password' => 'required|string|min:8|same:new_password',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $user = User::where('USER_ID', $request->user_id)->first();
        if($user){
            $passwordUpdate = DB::table('FE_USERS')->where('USER_ID', $request->user_id)->update([
                'USER_PASSWORD' => Hash::make($request->new_password),
            ]);
            return response()->json(['success' => true,'message' => 'Password has been changed successful'],200);
        } elseif (!$user) {
            return response()->json(['success' => false,'message' => 'Sorry, this user does not exist'], 422);
        }
    }
}
