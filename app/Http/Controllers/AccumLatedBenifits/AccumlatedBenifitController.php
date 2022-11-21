<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class AccumlatedBenifitController extends Controller
{
    public function search(Request $request)

    {
        $ndc = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                ->where('PLAN_ACCUM_DEDUCT_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('PLAN_ACCUM_DEDUCT_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList (Request $request)
    {
                    $ndc = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                    // ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                            ->where('PLAN_ACCUM_DEDUCT_ID', 'like', '%' .$request->search. '%')
                            ->orWhere('PLAN_ACCUM_DEDUCT_NAME', 'like', '%' . strtoupper($request->search) . '%')

                            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }



}
