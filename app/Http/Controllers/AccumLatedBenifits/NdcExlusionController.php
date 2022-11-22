<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class NdcExlusionController extends Controller
{
    public function search(Request $request)

    {
        $ndc =DB::table('NDC_EXCLUSION_LISTS')
        ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
                // ->where('NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', 'like', '%' .$request->search. '%')
                // ->orWhere('NDC_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' .$request->search. '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($ndcid)
    {
        $ndc =DB::table('NDC_EXCLUSION_LISTS')
        ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
                ->where('NDC_EXCLUSION_LISTS.NDC', 'like', '%' .$ndcid. '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('NDC_EXCLUSION_LISTS')
        ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
                ->where('NDC_EXCLUSION_LISTS.NDC', 'like', '%' .$ndcid. '%')
                ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
