<?php

namespace App\Http\Controllers\plan_design;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanEditController extends Controller
{
    public function get(Request $request)
    {
        $planEdit = DB::table('PLAN_BENEFIT_TABLE')
                    ->where('PLAN_ID', 'like', '%'. strtoupper($request->search) .'%')
                    ->orWhere('PLAN_NAME', 'like', '%'. strtoupper($request->search) .'%')
                    ->get();
        return $this->respondWithToken($this->token(), '', $planEdit);
    }

    public function getPlanEditData(Request $request)
    {
        $plandata = DB::table('plan_benefit_table')
                    ->leftJoin('PLAN_VALIDATION_LISTS', 'plan_benefit_table.plan_id', '=', 'plan_benefit_table.plan_id')
                    // ->leftJoin('PLAN_RX_NETWORK_RULES', 'plan_benefit_table.plan_id', '=', 'PLAN_RX_NETWORK_RULES.plan_id')
                    // ->leftJoin('PLAN_RX_NETWORKS', 'plan_benefit_table.plan_id', '=', 'PLAN_RX_NETWORKS.plan_id')
                    ->where('plan_benefit_table.plan_id', 'like', '%'.$request->search.'%')
                    ->first();


        return $this->respondWithToken($this->token(), '', $plandata);
    }
}
