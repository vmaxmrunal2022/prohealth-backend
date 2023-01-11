<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemParameterController extends Controller
{
    public function getSystemParameters(Request $request)
    {
        $systemParameter = DB::table("GLOBAL_PARAMS")
                           ->first();
        return $this->respondWithToken($this->token(), '', $systemParameter);
    }

    public function getState(Request $request)
    {
        $data = DB::table('COUNTRY_STATES')
                    ->select('COUNTRY_STATES.state_code','ZIP_CODES.ZIP_CODES')
                    ->join('ZIP_CODES', 'ZIP_CODES.state', '=', 'COUNTRY_STATES.state_code')
                    ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getCountries(Request $request)
    {
        $countries = DB::table('country_states')
                     ->select('country_code')
                     ->get();

        return $this->respondWithToken($this->token(), '', $countries);
    }

}
