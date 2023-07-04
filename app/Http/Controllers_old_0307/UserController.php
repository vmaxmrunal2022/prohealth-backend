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
// use App\getUserData;
use Illuminate\Support\Facades\Cache;

use Illuminate\Redis\Connections\Connection;
use DB;

class UserController extends Controller
{
    protected $user;
    protected $redis;
    public function __construct()
    {
        $this->middleware("auth:api", ["except" => ["login", "register", "changePassword"]]);
        $this->user = new User;
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
        $password = $request->USER_PASSWORD;
        $validator = FacadesValidator::make($request->all(), [
            'USER_ID' => 'required|string',
            'USER_PASSWORD' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 422);
        }
        $userCheck = User::where('USER_ID', $request->USER_ID)->first();
        if ($userCheck) {
            if ($userCheck && Hash::check($password, $userCheck->user_password)) {
                FacadesAuth::login($userCheck);
                if (!FacadesAuth::check()) {
                    $responseMessage = "Invalid username or password";
                    return response()->json([
                        "success" => false,
                        "message" => $responseMessage,
                        "error" => $responseMessage
                    ], 422);
                }
                $accessToken = '';
                $user = FacadesAuth::user();
                $responseMessage = "Login Successful";
                Session::put('user', auth()->user()->user_id);
                $userid = auth()->user()->user_id;
                $a = getUserData($userid);
                $usersData = auth()->user()->user_id;
                Cache::put('userId', $usersData, 86400);
                return $this->respondWithToken($accessToken, $responseMessage, $user);
            } else {
                $responseMessage = "Password does not match";
                return response()->json([
                    "success" => true,
                    "message" => $responseMessage,
                    "error" => $responseMessage
                ], 422);
            }
        } else {
            $responseMessage = "Sorry, this user does not exist";
            return response()->json([
                "success" => true,
                "message" => $responseMessage,
                "error" => $responseMessage
            ], 422);
        }
    }

    public function changePassword(Request $request)
    {
        // return $request->all();
        $validator = FacadesValidator::make($request->all(), [
            'user_id' => 'required',
            'user_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'confirm_password' => 'required|string|min:8|same:user_password',
        ], [
            'user_password.regex' => 'Password must contain alphanumeric characters and at least one special character, one uppercase letter, and one lowercase letter.'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray(),
                'error' => $validator->messages()->toArray()
            ], 422);
        }

        $password = $request->user_password;
        $userCheck = User::where('USER_ID', $request->user_id)->first();
        if ($userCheck) {
            $accessToken = '';
            $user = $userCheck;
            $responseMessage = "Successfully password changed";
            $hashed = Hash::make($password);

            $update = DB::table('FE_USERS')->where('USER_ID', $request->user_id)->update([
                'USER_PASSWORD' => $hashed,
                'USER_ID_MODIFIED' => Cache::get('userId'),
                'DATE_TIME_MODIFIED' => date('Ymd'),
                'PSWD_LAST_CHG_DATE' => date('Ymd'),
            ]);

            if ($update) {
                return $this->respondWithToken($accessToken, $responseMessage, $user);
            }
        } else {
            $responseMessage = "Sorry, this user does not exist";
            return response()->json([
                "success" => true,
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
}
