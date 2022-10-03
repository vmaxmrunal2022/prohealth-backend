<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function respondWithToken($token, $responseMessage, $data = [], $status = true, $code = 200)
    {
        return \response()->json([
            "success" => $status,
            "message" => $responseMessage,
            "data" => $data,
            "token" => $token,
            "token_type" => "bearer",
        ], $code);
    }

    public function defaultAuthGuard()
    {
        return Auth::guard('api');
    }

    public function token()
    {
        return Auth::check() ? $this->defaultAuthGuard()->user()->token() : null;
    }
}
