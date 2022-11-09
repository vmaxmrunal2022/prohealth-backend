<?php

namespace App\Http\Controllers\Speciality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class SpecialityController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('SPECIALTY_VALIDATIONS')
        ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
                ->select('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', 'SPECIALTY_VALIDATIONS.SPECIALTY_ID','SPECIALTY_EXCEPTIONS.EXCEPTION_NAME')
                ->where('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getDiagnosisList($ndcid)
    {
        $ndclist = DB::table('SPECIALTY_VALIDATIONS')
        ->select('DIAGNOSIS_LIST', 'DIAGNOSIS_ID','PRIORITY')
        ->where('DIAGNOSIS_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('SPECIALTY_VALIDATIONS')
                    ->join('DIAGNOSIS_CODES', 'DIAGNOSIS_CODES.DIAGNOSIS_ID', '=', 'SPECIALTY_VALIDATIONS.DIAGNOSIS_ID')
                    ->select('SPECIALTY_VALIDATIONS.DIAGNOSIS_LIST', 'SPECIALTY_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description','SPECIALTY_VALIDATIONS.DIAGNOSIS_STATUS','SPECIALTY_VALIDATIONS.PRIORITY')

                    // ->where('NDC_EXCEPTION_LISTS.NDC', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
