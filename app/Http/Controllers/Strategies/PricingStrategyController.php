<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PricingStrategyController extends Controller
{


    public function add(Request $request)
    {
        $createddate = date('y-m-d');


        if ($request->has('new')) {

            $accum_benfit_stat_names = DB::table('PRICING_STRATEGY_NAMES')->insert(
                [
                    'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                    'pricing_strategy_name' => $request->pricing_strategy_name,

                ]
            );


            $accum_benfit_stat = DB::table('PRICING_STRATEGY')->insert(
                [
                    'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                    'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                    'formulary_variation_ind' => $request->formulary_variation_ind,
                    'network_part_variation_ind' => $request->network_part_variation_ind,
                    'claim_type_variation_ind' => $request->claim_type_variation_ind,
                    'date_time_created' => $createddate,
                    'user_id' => '',
                    'date_time_modified' => '',
                    'form_id' => '',
                    'user_id_created' => '',
                    'effective_date' => $request->effective_date,
                    'module_exit' => '',
                    'price_schedule' => $request->price_schedule,
                    'mac_list' => $request->mac_list,




                ]
            );
        } else {

            $benefitcode = DB::table('PRICING_STRATEGY_NAMES')
                ->where('pricing_strategy_id', $request->pricing_strategy_id)
                ->update(
                    [
                        'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                        'pricing_strategy_name' => $request->pricing_strategy_name,


                    ]
                );

            $accum_benfit_stat = DB::table('PRICING_STRATEGY')
                ->where('pricing_strategy_id', $request->pricing_strategy_id)
                ->update(
                    [
                        'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'formulary_variation_ind' => $request->formulary_variation_ind,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'date_time_created' => $createddate,
                        'user_id' => '',
                        'date_time_modified' => '',
                        'form_id' => '',
                        'user_id_created' => '',
                        'effective_date' => $request->effective_date,
                        'module_exit' => '',
                        'price_schedule' => $request->price_schedule,
                        'mac_list' => $request->mac_list,



                    ]
                );


            $benefitcode = DB::table('PRICING_STRATEGY')->where('pricing_strategy_id', 'like', $request->pricing_strategy_id)->first();
        }
    }




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
            ->where('PRICING_STRATEGY.PRICING_STRATEGY_ID', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
