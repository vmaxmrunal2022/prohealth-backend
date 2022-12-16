<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class UserController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->middleware("auth:api", ["except" => ["login", "register"]]);
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

        $validator = Validator::make($request->all(), [
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
            ->where('USER_PASSWORD', $request->USER_PASSWORD)
            ->first();


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

            Auth::login($user);
        //    print_r(Auth::login($user));
            if (!Auth::check()) {
                $responseMessage = "Invalid username or password";
                return response()->json([
                    "success" => false,
                    "message" => $responseMessage,
                    "error" => $responseMessage
                ], 422);
            }

            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $responseMessage = "Login Successful";
            Session::put('user', auth()->user()->user_id);
            return $this->respondWithToken($accessToken, $responseMessage, ['name' => auth()->user()->user_id]);
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
        $user = Auth::guard("api")->user()->token();
        $user->revoke();
        $responseMessage = "successfully logged out";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }
}
