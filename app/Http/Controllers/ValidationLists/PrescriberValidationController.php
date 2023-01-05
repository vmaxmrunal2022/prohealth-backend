<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriberValidationController extends Controller
{
    public function search(Request $request)
    {
        $physicianExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), 'like', '%' .strtoupper($request->search). '%')
                ->where(DB::raw('UPPER(EXCEPTION_NAME)'), 'like', '%' .strtoupper($request->search). '%')
                ->orderBy('PHYSICIAN_LIST','ASC')
                ->get();
                return $this->respondWithToken($this->token(), '', $physicianExceptionData);
    }



    public function getProviderList($ndcid)
    {
        // $ndclist = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
        // ->where('PROV_TYPE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')
        //         // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        //         ->get();


                $ndclist = DB::table('PHYSICIAN_EXCEPTIONS')
        ->join('PHYSICIAN_VALIDATIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST')
        ->join('PHYSICIAN_TABLE','PHYSICIAN_TABLE.PHYSICIAN_ID','=','PHYSICIAN_VALIDATIONS.PHYSICIAN_ID')

                // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description')
                ->where('PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', 'like', '%' .$ndcid. '%')
                // ->orWhere('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }


    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('PHYSICIAN_EXCEPTIONS')
        ->join('PHYSICIAN_VALIDATIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST')
        ->join('PHYSICIAN_TABLE','PHYSICIAN_TABLE.PHYSICIAN_ID','=','PHYSICIAN_VALIDATIONS.PHYSICIAN_ID')

                // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description')
                ->where('PHYSICIAN_TABLE.PHYSICIAN_ID', 'like', '%' .$ndcid. '%')
                // ->orWhere('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
                ->first();
        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function searchDropDownPrescriberList(){
        $data = DB::table('PHYSICIAN_TABLE')
        // ->where('PHYSICIAN_ID','LIKE','%'.strtoupper($pharmacy_list).'%')
        ->orWhere('PHYSICIAN_LAST_NAME','LIKE','%'.strtoupper('campB').'%')
        ->get();

        return $this->respondWithToken($this->token(),'',$data);
    }
}
