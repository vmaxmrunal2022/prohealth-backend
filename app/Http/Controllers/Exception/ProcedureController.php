<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcedureController extends Controller
{


    public function add(Request $request)
    {

        $createddate = date('y-m-d');

        if ($request->has('new')) {
            $exist = DB::table('PROCEDURE_EXCEPTION_LISTS')
            ->where('procedure_exception_list',strtoupper($request->procedure_exception_list))->first();

            if($exist){
                return $this->respondWithToken( $this->token(), 'Procedure List ID Already Existed');
            }


            // return $request->all();
            $accum_benfit_stat_names = DB::table('PROCEDURE_EXCEPTION_NAMES')->insert(
                [
                    'procedure_exception_list' => strtoupper($request->procedure_exception_list),

                    // 'exception_name' => $request->exception_name,


                    'exception_name'=>$request->exception_name,
                    

                ]
            );
            $accum_benfit_stat = DB::table('PROCEDURE_EXCEPTION_LISTS')->insert(
                [
                    'procedure_exception_list' => $request->procedure_exception_list,
                    'accum_bene_strategy_id' => $request->accum_bene_strategy_id,
                    'benefit_code' => $request->benefit_code,
                    'copay_strategy_id' => $request->copay_strategy_id,

                    'coverage_start_days' => $request->coverage_start_days,
                    'diagnosis_id' => $request->diagnosis_id,
                    'diagnosis_list' => $request->diagnosis_list,
                    'exception_list' => $request->exception_list,
                    'exception_name' => $request->exception_name,
                    'max_age' => $request->max_age,
                    'max_price' => $request->max_price,
                    'max_price_opt' => $request->max_price_opt,
                    'module_exit' => $request->module_exit,
                    'new_claim_status' => $request->new_claim_status,
                    'physician_list' => $request->physician_list,
                    'physician_specialty_list' => $request->physician_specialty_list,
                    'pricing_strategy_id' => $request->pricing_strategy_id,
                    'proc_code_list_id' => $request->proc_code_list_id,
                    'process_rule' => $request->process_rule,
                    'provider_type' => $request->provider_type,
                    'reject_only_msg_flag' => $request->reject_only_msg_flag,
                    'rx_qty_opt_multiplier' => $request->rx_qty_opt_multiplier,
                    'service_modifier' => $request->service_modifier,
                    'service_type' => $request->service_type,
                    'sex_restriction' => $request->sex_restriction,
                    'valid_relation_code' => $request->valid_relation_code,

                    'claim_type_variation_ind' => $request->claim_type_variation_ind,
                    'description' => $request->description,
                    'effective_date' => $request->effective_date,
                    'max_qty_over_time' => $request->max_qty_over_time,
                    'message' => $request->message,
                    'message_stop_date' => $request->message_stop_date,
                    'min_age' => $request->min_age,
                    'min_price' => $request->min_price,
                    'network_part_variation_ind' => $request->network_part_variation_ind,
                    'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                    'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                    'rx_network_rule_id' => $request->rx_network_rule_id,
                    'rx_network_rule_name' => $request->rx_network_rule_name,
                    'termination_date' => $request->termination_date,
                    'ucr' => $request->ucr
                ]
            );
            $benefitcode = DB::table('PROCEDURE_EXCEPTION_LISTS')->where('procedure_exception_list', 'like', '%' . $request->procedure_exception_list . '%')->first();
        } else {

            $exceptipon_names = DB::table('PROCEDURE_EXCEPTION_NAMES')
            ->where('procedure_exception_list',strtoupper($request->procedure_exception_list))
            // ->where('new_claim_status', $request->new_claim_status )
            ->update(
                [
                    'exception_name'=>$request->exception_name,

                ]);

            $benefitcode = DB::table('PROCEDURE_EXCEPTION_LISTS')
                ->where('procudure_exception_list', $request->procudure_exception_list)
                ->where('new_claim_status', $request->new_claim_status)
                ->update(
                    [
                        'accum_bene_strategy_id' => $request->accum_bene_strategy_id,
                        'benefit_code' => $request->benefit_code,
                        'copay_strategy_id' => $request->copay_strategy_id,
                        'coverage_start_days' => $request->coverage_start_days,
                        'diagnosis_id' => $request->diagnosis_id,
                        'diagnosis_list' => $request->diagnosis_list,
                        'exception_list' => $request->exception_list,
                        'exception_name' => $request->exception_name,
                        'max_age' => $request->max_age,
                        'max_price' => $request->max_price,
                        'max_price_opt' => $request->max_price_opt,
                        'module_exit' => $request->module_exit,
                        'new_claim_status' => $request->new_claim_status,
                        'physician_list' => $request->physician_list,
                        'physician_specialty_list' => $request->physician_specialty_list,
                        'pricing_strategy_id' => $request->pricing_strategy_id,
                        'proc_code_list_id' => $request->proc_code_list_id,
                        'procedure_exception_list' => $request->procedure_exception_list,
                        'process_rule' => $request->process_rule,
                        'provider_type' => $request->provider_type,
                        'reject_only_msg_flag' => $request->reject_only_msg_flag,
                        'rx_qty_opt_multiplier' => $request->rx_qty_opt_multiplier,
                        'service_modifier' => $request->service_modifier,
                        'service_type' => $request->service_type,
                        'sex_restriction' => $request->sex_restriction,
                        'valid_relation_code' => $request->valid_relation_code,

                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'description' => $request->description,
                        'effective_date' => $request->effective_date,
                        'max_qty_over_time' => $request->max_qty_over_time,
                        'message' => $request->message,
                        'message_stop_date' => $request->message_stop_date,
                        'min_age' => $request->min_age,
                        'min_price' => $request->min_price,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                        'rx_network_rule_id' => $request->rx_network_rule_id,
                        'rx_network_rule_name' => $request->rx_network_rule_name,
                        'termination_date' => $request->termination_date,
                        'ucr' => $request->ucr


                    ]
                );

            $benefitcode = DB::table('PROCEDURE_EXCEPTION_LISTS')->where('new_claim_status', 'like', '%' . $request->new_claim_status . '%')->first();


            // $accum_benfit_stat = DB::table('PROCEDURE_EXCEPTION_NAMES' )
            // ->where('procedure_exception_list', $request->procedure_exception_list )
            // ->update(
            //     [
            //         // 'exception_name'=>$request->exception_name,



            //     ]
            // );

            // $benefitcode = DB::table('PROCEDURE_EXCEPTION_NAMES')->where('proc_code_list', 'like', '%'.$request->proc_code_list .'%')->first();

        }


        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }




    public function search(Request $request)
    {
        $ndc = DB::table('PROCEDURE_EXCEPTION_NAMES')
            ->select('PROCEDURE_EXCEPTION_LIST', 'EXCEPTION_NAME')
            ->where('PROCEDURE_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getPCList($ndcid)
    {
        $ndclist = DB::table('PROCEDURE_EXCEPTION_LISTS')
            // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
            ->where('PROCEDURE_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getPCItemDetails($ndcid)
    {
        $ndc = DB::table('PROCEDURE_EXCEPTION_LISTS')
            ->select('PROCEDURE_EXCEPTION_LISTS.*', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST as exception_list', 'PROCEDURE_EXCEPTION_NAMES.EXCEPTION_NAME as exception_name')
            ->join('PROCEDURE_EXCEPTION_NAMES', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST', '=', 'PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST')
            ->where('PROCEDURE_EXCEPTION_LISTS.PROC_CODE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
