<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class ProviderDataProviderController extends Controller
{
    
    public function search(Request $request)
    {
        $ndc = DB::table('PHARMACY_TABLE')

                ->where('PHARMACY_NAME', 'like', '%' .strtoupper($request->search). '%')
                ->orWhere('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getProviderList($ndcid)
    {
        // $ndclist = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
        // ->where('PROV_TYPE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')
        //         // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        //         ->get();


                $ndclist = DB::table('PHARMACY_VALIDATIONS')
        ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')

                // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description')
                // ->where('PHARMACY_LIST', 'like', '%' .strtoupper($request->search). '%')
                // ->orWhere('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }


    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('PHARMACY_TABLE')
                    ->where('PHARMACY_NABP',$ndcid)  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
