<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CopayStrategyController extends Controller
{


    public function add(Request $request)
    {
        $createddate = date('y-m-d');


        if ($request->has('new')) {

            $accum_benfit_stat_names = DB::table('COPAY_STRATEGY_NAMES')->insert(
                [
                    'copay_strategy_id' => $request->copay_strategy_id,
                    'copay_strategy_name' => $request->copay_strategy_name,

                ]
            );


            $accum_benfit_stat = DB::table('COPAY_STRATEGY')->insert(
                [
                    'copay_strategy_id' => $request->copay_strategy_id,
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
                    'copay_schedule' => $request->copay_schedule

                ]
            );
        } else {

            $benefitcode = DB::table('COPAY_STRATEGY_NAMES')
                ->where('copay_strategy_id', $request->copay_strategy_id)
                ->update(
                    [
                        'copay_strategy_id' => $request->copay_strategy_id,
                        'copay_strategy_name' => $request->copay_strategy_name,


                    ]
                );

            $accum_benfit_stat = DB::table('COPAY_STRATEGY')
                ->where('copay_strategy_id', $request->copay_strategy_id)
                ->update(
                    [
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
                        'copay_schedule' => $request->copay_schedule




                    ]
                );


            $benefitcode = DB::table('COPAY_STRATEGY')->where('copay_strategy_id', 'like', $request->copay_strategy_id)->first();
        }
    }



    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $ndc = DB::table('COPAY_STRATEGY')
                ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                ->select('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' . $request->search . '%')
                ->orWhere('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME', 'like', '%' . $request->search . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $ndc);
        }
    }

    public function getList($ndcid)
    {
        $ndclist = DB::table('COPAY_STRATEGY')
            ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            // ->select( 'DIAGNOSIS_LIST', 'DIAGNOSIS_ID', 'PRIORITY' )
            ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' . $ndcid . '%')
            // ->orWhere( 'EXCEPTION_NAME', 'like', '%' . strtoupper( $ndcid ) . '%' )
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDetails($ndcid)
    {
        $ndc = DB::table('COPAY_STRATEGY')
            ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function CopayDropDown(Request $request)
    {

        $ndc = DB::table('COPAY_STRATEGY_NAMES')
            ->get();

        return $this->respondWithToken($this->token(), 'Data Fetched Suceefully', $ndc);
    }
    public function copay_delete(Request $request)
    {
        if (isset($request->copay_strategy_id) && ($request->copay_strategy_name)) {
            $all_exceptions_lists =  DB::table('COPAY_STRATEGY_NAMES')
                ->where('COPAY_STRATEGY_ID', $request->copay_strategy_id)
                ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->copay_strategy_id)) {

            $exception_delete =  DB::table('COPAY_STRATEGY')
                ->where('COPAY_STRATEGY_ID', $request->copay_strategy_id)
                ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}
