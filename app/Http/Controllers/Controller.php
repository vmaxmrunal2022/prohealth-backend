<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function respondWithToken($token, $responseMessage, $data = [], $status = true, $code = 200)
    {
        return \response()->json([
            "success" => $status,
            "message" => $responseMessage,
            "data" => $data,
            "token" => $token ?? '',
            "token_type" => "bearer",
        ], $code);
    }

    public function defaultAuthGuard()
    {
        return Auth::guard('api');
    }

    public function token()
    {
        return Auth::check() ? $this->defaultAuthGuard()->user()->token() : 'dfsdvf';
    }

    public function Contries()
    {
        $countries = DB::table('COUNTRY_STATES')->where('country_code', 'Coun')->get();
        return $this->respondWithToken($this->token(), $countries);
    }

    public function getStatesOfCountry($countryid)
    {
        $states = DB::table('COUNTRY_STATES')->whereNot('state_code', '**')->get();
        return $this->respondWithToken($this->token(), $states);
    }
}
