<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
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

    public function Contries(Request $request)
    {
        //$countries = DB::table('COUNTRY_STATES')->where('country_code', 'Coun')->get();
        $countries = DB::table('COUNTRY_STATES')
            ->select('country_code', 'description')
            ->where(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $countries);
    }
    
    public function ContriesSearch($c_id='')
    {
        if(!empty($c_id)){
            $countries = DB::table('COUNTRY_STATES')->where(DB::raw('UPPER(DESCRIPTION)'), 'like','%'.strtoupper($c_id).'%')->get();
        }else{
            $countries = DB::table('COUNTRY_STATES')->get();
        }

        return $this->respondWithToken($this->token(),'', $countries);
        // return $countries;
    }

    //public function getStatesOfCountry($countryid)
    public function getStatesOfCountry(Request $request)
    {

        $states = DB::table('COUNTRY_STATES')->whereNot('state_code', '**')->get();
        return $this->respondWithToken($this->token(), '', $states);

        // $states = DB::table('COUNTRY_STATES')
        //     ->select('COUNTRY_STATES.state_code', 'ZIP_CODES.ZIP_CODE')
        //     ->join('ZIP_CODES', 'ZIP_CODES.state', '=', 'COUNTRY_STATES.state_code')
        //     ->where(DB::raw('UPPER(COUNTRY_STATES.state_code)'), 'like', '%' . strtoupper($request->search) . '%')
        //     ->get();

        // return $this->respondWithToken($this->token(), '', $states);
    }

    public function getStatesOfCountrySearch($state_code='')
    {
        if(!empty($state_code)){
            $states = DB::table('COUNTRY_STATES')->where(DB::raw('UPPER(STATE_CODE)'), 'like','%'.strtoupper($state_code).'%')->get();
        }else{
            $states = DB::table('COUNTRY_STATES')->get();
        }

        return $this->respondWithToken($this->token(), '', $states);
    }
      //Member
      public function getMember(Request $request)
      {        
          $memberIds = DB::table('member')     
                       ->where('member_id', 'like', '%'. $request->search .'%') 
                       ->get();
          return $this->respondWithToken($this->token(), '', $memberIds);
      }

      //Provider
      public function getProvider(Request $request)
      {
        $providers = DB::table('pharmacy_table')
                     ->where(DB::raw('UPPER(pharmacy_nabp)'), 'like', '%'. strtoupper($request->search) .'%')
                     ->get();
        return $this->respondWithToken($this->token(), '', $providers);
      }

}
