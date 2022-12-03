<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class GpiExclusionController extends Controller
{


    public function search(Request $request)

    {
        $ndc =DB::table('GPI_EXCLUSION_LISTS')
        ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
                ->where('GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', 'like', '%' .$request->search. '%')
                ->orWhere('GPI_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' .$request->search. '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($ndcid)
    {
        $ndc =DB::table('GPI_EXCLUSION_LISTS')
        ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
                ->where('GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', 'like', '%' .$ndcid. '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
        ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
                ->where('GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', 'like', '%' .$ndcid. '%')
                ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
    
}
 