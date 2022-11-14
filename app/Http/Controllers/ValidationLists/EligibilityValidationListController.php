<?php

namespace App\Http\Controllers\validationlists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class EligibilityValidationListController extends Controller
{
    
    public function search(Request $request)
    {
        $ndc = DB::table('ELIG_VALIDATION_LISTS')
                // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description')
                ->where('ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID', 'like', '%' .$request->search. '%')
                ->orWhere('ELIG_VALIDATION_LISTS.ELIG_VALIDATION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


   

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('ELIG_VALIDATION_LISTS')

                    // ->join('DIAGNOSIS_CODES', 'DIAGNOSIS_CODES.DIAGNOSIS_ID', '=', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID')
                    // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description','DIAGNOSIS_VALIDATIONS.DIAGNOSIS_STATUS','DIAGNOSIS_VALIDATIONS.PRIORITY')

                    ->where('ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID', 'like', '%' . $ndcid . '%')  
                    ->first();

                $ndc->agelimit_month='1';
                $ndc->age_limit_day='2';

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
