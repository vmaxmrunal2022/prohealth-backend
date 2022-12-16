<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class BenefitDerivationController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('BENEFIT_DERIVATION_NAMES')
                ->select('BENEFIT_DERIVATION_ID', 'DESCRIPTION')
                ->where('BENEFIT_DERIVATION_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getBLList($ndcid)
    {
        $ndclist = DB::table('BENEFIT_DERIVATION')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('BENEFIT_DERIVATION_ID', $ndcid)
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getBLItemDetails($ndcid,$ndcid2)
    {
        $ndclist = DB::table('BENEFIT_DERIVATION')
        // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
        ->join('BENEFIT_DERIVATION_NAMES', 'BENEFIT_DERIVATION_NAMES.BENEFIT_DERIVATION_ID', '=', 'BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID')
        ->where('BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID', $ndcid)
        ->where('BENEFIT_DERIVATION.BENEFIT_CODE', $ndcid2)



        
        // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        ->first();

return $this->respondWithToken($this->token(), '', $ndclist);

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
