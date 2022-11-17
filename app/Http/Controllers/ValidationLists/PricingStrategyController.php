<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PricingStrategyController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('PRICING_STRATEGY')
        ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.PRICING_STRATEGY_ID', '=', 'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_ID')
                ->select('PRICING_STRATEGY.PRICING_STRATEGY_ID', 'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_NAME as pricing_strategy_name')
                ->where('PRICING_STRATEGY.PRICING_STRATEGY_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('PRICING_STRATEGY_NAMES.PRICING_STRATEGY_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getProviderList($ndcid)
    {
        $ndclist = DB::table('PRICING_STRATEGY')
        // ->select('DIAGNOSIS_LIST', 'DIAGNOSIS_ID','PRIORITY')
        ->where('PRICING_STRATEGY_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
                    $ndc = DB::table('PRICING_STRATEGY')
                    ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.PRICING_STRATEGY_ID', '=', 'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_ID')
                            ->where('PRICING_STRATEGY.PRICING_STRATEGY_ID', 'like', '%' .$ndcid. '%')
                            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
