<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosisValidationListController extends Controller
{
    public function search(Request $request)
    {
        $data = DB::table('DIAGNOSIS_EXCEPTIONS as a')
        ->where(DB::raw('UPPER(a.DIAGNOSIS_LIST)'), 'like', '%' .strtoupper($request->search). '%')
        ->orWhere(DB::raw('UPPER(a.EXCEPTION_NAME)'), 'like', '%' .strtoupper($request->search). '%')
        ->get();

    return $this->respondWithToken($this->token(), '', $data);
    }



    public function getDiagnosisList($ndcid)
    {
        $ndclist = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
        ->where('PROV_TYPE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDiagnosisLimitations ($diagnosis_list)
        {
            $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                        ->where('DIAGNOSIS_LIST', 'like', '%' . $diagnosis_list . '%')
                        ->get();

            return $this->respondWithToken($this->token(), '', $data);

        }








}