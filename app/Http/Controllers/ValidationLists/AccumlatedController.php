<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class AccumlatedController extends Controller
{
    
    public function search(Request $request)

    {
        $ndc = DB::table('ACCUM_BENEFIT_STRATEGY')
        ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID')
                ->select('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getList($ndcid)
    {
        $ndclist = DB::table('ACCUM_BENEFIT_STRATEGY')
        // ->select('DIAGNOSIS_LIST', 'DIAGNOSIS_ID','PRIORITY')
        ->where('ACCUM_BENE_STRATEGY_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDetails($ndcid)
    {
                    $ndc = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID')
                            ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'like', '%' .$ndcid. '%')
                            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
