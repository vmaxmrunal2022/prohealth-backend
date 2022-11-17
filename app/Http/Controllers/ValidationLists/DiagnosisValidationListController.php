<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DiagnosisValidationListController extends Controller
{
    public function search(Request $request)
    {

        $ndc = DB::table('DIAGNOSIS_EXCEPTIONS')
        ->where('DIAGNOSIS_EXCEPTIONS.DIAGNOSIS_LIST', 'like', '%' .strtoupper($request->search). '%')
       
        ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getDiagnosisList($ndcid)
    {
        $ndclist = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
        ->where('PROV_TYPE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails ($ndcid)
        {
            $ndc = DB::table('DIAGNOSIS_VALIDATIONS')
                        // ->join('PROVIDER_TYPE_VALIDATIONS', 'PROVIDER_TYPE_VALIDATION_NAMES.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
                        // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST')

                        ->where('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'like', '%' . strtoupper($ndcid) . '%')  
                        // ->groupBy('DIAGNOSIS_LIST')

                        ->get();

            return $this->respondWithToken($this->token(), '', $ndc);

        }


                   


             
        

}