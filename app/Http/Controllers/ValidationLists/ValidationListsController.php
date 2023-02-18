<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidationListsController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('DIAGNOSIS_VALIDATIONS')
            ->join('DIAGNOSIS_CODES', 'DIAGNOSIS_CODES.DIAGNOSIS_ID', '=', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID')
            ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID', 'DIAGNOSIS_CODES.DESCRIPTION as Description')
            ->where('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getDiagnosisList($ndcid)
    {
        $ndclist = DB::table('DIAGNOSIS_VALIDATIONS')
            ->select('DIAGNOSIS_LIST', 'DIAGNOSIS_ID', 'PRIORITY')
            ->where('DIAGNOSIS_ID', 'like', '%' . strtoupper($ndcid) . '%')
            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('DIAGNOSIS_VALIDATIONS')
            ->join('DIAGNOSIS_CODES', 'DIAGNOSIS_CODES.DIAGNOSIS_ID', '=', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID')
            ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID', 'DIAGNOSIS_CODES.DESCRIPTION as Description', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_STATUS', 'DIAGNOSIS_VALIDATIONS.PRIORITY')

            // ->where('NDC_EXCEPTION_LISTS.NDC', 'like', '%' . strtoupper($ndcid) . '%')  
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
