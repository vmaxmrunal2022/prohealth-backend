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

    public function getSpecialityList($ndcid)
    {
        $ndclist = DB::table('SPECIALTY_VALIDATIONS')
        // ->select('SPECIALTY_LIST', 'DIAGNOSIS_ID','PRIORITY')
        ->where('SPECIALTY_ID', 'like', '%' . strtoupper($ndcid) . '%')
                ->orWhere('SPECIALTY_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('SPECIALTY_VALIDATIONS')
                    ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')

                    // ->where('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();


                    // $ndc = DB::table('SPECIALTY_VALIDATIONS')
                    // ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
                    //         ->select('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', 'SPECIALTY_VALIDATIONS.SPECIALTY_ID','SPECIALTY_EXCEPTIONS.EXCEPTION_NAME')
                    //         ->where('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                    //         ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
