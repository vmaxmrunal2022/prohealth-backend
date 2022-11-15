<?php

namespace App\Http\Controllers\validationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ProviderController extends Controller
{
    

    public function search(Request $request)
    {
        $ndc = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description')
                ->where('PROVIDER_TYPE_VALIDATION_NAMES.PROV_TYPE_LIST_ID', 'like', '%' .strtoupper($request->search). '%')
                ->orWhere('PROVIDER_TYPE_VALIDATION_NAMES.DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getProviderList($ndcid)
    {
        $ndclist = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
        ->where('PROV_TYPE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }


    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                    ->join('PROVIDER_TYPE_VALIDATIONS', 'PROVIDER_TYPE_VALIDATION_NAMES.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
                    // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description','DIAGNOSIS_VALIDATIONS.DIAGNOSIS_STATUS','DIAGNOSIS_VALIDATIONS.PRIORITY')

                    // ->where('NDC_EXCEPTION_LISTS.NDC', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
