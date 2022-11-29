<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CopayStrategyController extends Controller
{

    public function search(Request $request)

    {
        $ndc = DB::table('COPAY_STRATEGY')
        ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                ->select('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getList($ndcid)
    {
        $ndclist = DB::table('COPAY_STRATEGY')
        // ->select('DIAGNOSIS_LIST', 'DIAGNOSIS_ID','PRIORITY')
        ->where('COPAY_STRATEGY_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDetails($ndcid)
    {
                    $ndc = DB::table('COPAY_STRATEGY')
                    ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                            ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' .$ndcid. '%')
                            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
