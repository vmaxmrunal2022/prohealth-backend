<?php

namespace App\Http\Controllers\validationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderController extends Controller
{


    public function search(Request $request)
    {
            $pharmacyExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                ->where(DB::raw('UPPER(PHARMACY_LIST)'), 'like', '%' .strtoupper($request->search). '%')
                ->orderBy('PHARMACY_LIST','ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $pharmacyExceptionData);
    }



    public function getProviderValidationList($pharmacy_list)
    {

                $pharmacyValidationData = DB::table('PHARMACY_VALIDATIONS')
                ->select('PHARMACY_TABLE.PHARMACY_NABP','PHARMACY_VALIDATIONS.PHARMACY_LIST','PHARMACY_VALIDATIONS.PHARMACY_STATUS','PHARMACY_NAME')
                ->join('PHARMACY_TABLE','PHARMACY_TABLE.PHARMACY_NABP','=','PHARMACY_VALIDATIONS.PHARMACY_NABP')
                ->where('PHARMACY_LIST',$pharmacy_list)
                ->get();

        return $this->respondWithToken($this->token(), '', $pharmacyValidationData);
    }


    public function getProviderDetails($pharmacy_list,$pharmacy_nabp)
    {
        $data = DB::table('PHARMACY_VALIDATIONS as a')
                    ->select('a.PHARMACY_LIST','a.PHARMACY_NABP','a.PHARMACY_STATUS','c.PHARMACY_NAME','b.EXCEPTION_NAME')
                    ->join('PHARMACY_EXCEPTIONS as b','b.PHARMACY_LIST','=','a.PHARMACY_LIST')
                    ->join('PHARMACY_TABLE as c','c.PHARMACY_NABP','=','a.PHARMACY_NABP')
                    ->where('a.PHARMACY_LIST',  $pharmacy_list)
                    ->where('a.PHARMACY_NABP',  $pharmacy_nabp)
                    ->first();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addProvider(Request $request){
        $getProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
        ->where('PHARMACY_LIST',$request->parmacy_list)
        ->first();

        $getProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
        ->where('PHARMACY_LIST',$request->parmacy_list)
        ->where('PHARMACY_NABP',$request->pharmacy_nabp)
        ->first();

        if($request->has('new')){
            if(!$getProviderExceptionData){
                $addProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                ->insert([
                    'PHARMACY_EXCEPTIONS'
                ]);
            }
        }

    }
}
