<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class AccumlatedBenifitController extends Controller
{



    public function add( Request $request ) {


        if ( $request->has( 'new' ) ) {


            $accum_benfit_stat = DB::table('PLAN_ACCUM_DEDUCT_TABLE' )->insert(
                [
                    'plan_accum_deduct_id'=>$request->plan_accum_deduct_id,
                    'plan_accum_deduct_name' => $request->plan_accum_deduct_name,
                    'ndc_exclusion_list'=>$request->ndc_exclusion_list,
                    'accum_bene_price_schedule'=>$request->accum_bene_price_schedule,
                    'aggregate_type_ded'=>$request->aggregate_type_ded,
                    'aggregate_type_max'=>$request->aggregate_type_max,
                    'aggregate_type_mop'=>$request->aggregate_type_mop,
                    'apply_indiv_limit_ded'=>$request->apply_indiv_limit_ded,
                    'apply_indiv_limit_max'=>$request->apply_indiv_limit_max,
                    'apply_indiv_limit_mop'=>$request->apply_indiv_limit_mop,
                    'benefit_grouping_type'=>$request->benefit_grouping_type,
                    'deduc_period'=>$request->deduc_period,
                    'exclude_brand_generics'=>$request->exclude_brand_generics,
                    'exclude_in_network'=>$request->exclude_in_network,
                    'exclude_in_network_ded'=>$request->exclude_in_network_ded,
                    'exclude_in_network_mop'=>$request->exclude_in_network_mop,
                    'exclude_mail_ord_ded'=>$request->exclude_mail_ord_ded,
                    'exclude_mail_ord_mop'=>$request->exclude_mail_ord_mop,
                    'exclude_mail_order'=>$request->exclude_mail_order,
                    'exclude_maint_drug_ded'=>$request->exclude_maint_drug_ded,
                    'exclude_maint_drug_mop'=>$request->exclude_maint_drug_mop,
                    'exclude_maintenance_drugs'=>$request->exclude_maintenance_drugs,
                    'exclude_patient_differential'=>$request->exclude_patient_differential,
                    'family_action_1'=>$request->family_action_1,
                    'family_action_2'=>$request->family_action_2,
                    'family_benefit_1'=>$request->family_benefit_1,
                    'family_benefit_2'=>$request->family_benefit_2,
                    'family_copay_1'=>$request->family_copay_1,
                    'family_copay_2'=>$request->family_copay_2,
                    'family_deductible'=>$request->family_deductible,
                    'family_out_of_pocket_action'=>$request->family_out_of_pocket_action,
                    'family_sched_1'=>$request->family_sched_1,
                    'family_sched_2'=>$request->family_sched_2,
                    'grouping_type'=>$request->grouping_type,
                    'ind_out_of_pocket_action'=>$request->ind_out_of_pocket_action,
                    'gpi_exclusion_list'=>$request->gpi_exclusion_list,
                    'gpi_exclusion_list_ded'=>$request->gpi_exclusion_list_ded,
                    'gpi_exclusion_list_mop'=>$request->gpi_exclusion_list_mop
                    
                  
                   

                ]
            );

            $benefitcode = DB::table('PLAN_ACCUM_DEDUCT_TABLE' )->where('plan_accum_deduct_id', 'like', $request->plan_accum_deduct_id)->first();



        }else{

            $createddate = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
            ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id )
            ->update(
                [
                    'plan_accum_deduct_name' => $request->plan_accum_deduct_name,
                    'ndc_exclusion_list'=>$request->ndc_exclusion_list,
                    'accum_bene_price_schedule'=>$request->accum_bene_price_schedule,
                    'aggregate_type_ded'=>$request->aggregate_type_ded,
                    'aggregate_type_max'=>$request->aggregate_type_max,
                    'aggregate_type_mop'=>$request->aggregate_type_mop,
                    'apply_indiv_limit_ded'=>$request->apply_indiv_limit_ded,
                    'apply_indiv_limit_max'=>$request->apply_indiv_limit_max,
                    'apply_indiv_limit_mop'=>$request->apply_indiv_limit_mop,
                    'benefit_grouping_type'=>$request->benefit_grouping_type,
                    'deduc_period'=>$request->deduc_period,
                    'exclude_brand_generics'=>$request->exclude_brand_generics,
                    'exclude_in_network'=>$request->exclude_in_network,
                    'exclude_in_network_ded'=>$request->exclude_in_network_ded,
                    'exclude_in_network_mop'=>$request->exclude_in_network_mop,
                    'exclude_mail_ord_ded'=>$request->exclude_mail_ord_ded,
                    'exclude_mail_ord_mop'=>$request->exclude_mail_ord_mop,
                    'exclude_mail_order'=>$request->exclude_mail_order,
                    'exclude_maint_drug_ded'=>$request->exclude_maint_drug_ded,
                    'exclude_maint_drug_mop'=>$request->exclude_maint_drug_mop,
                    'exclude_maintenance_drugs'=>$request->exclude_maintenance_drugs,
                    'exclude_patient_differential'=>$request->exclude_patient_differential,
                    'family_action_1'=>$request->family_action_1,
                    'family_action_2'=>$request->family_action_2,
                    'family_benefit_1'=>$request->family_benefit_1,
                    'family_benefit_2'=>$request->family_benefit_2,
                    'family_copay_1'=>$request->family_copay_1,
                    'family_copay_2'=>$request->family_copay_2,
                    'family_deductible'=>$request->family_deductible,
                    'family_out_of_pocket_action'=>$request->family_out_of_pocket_action,
                    'family_sched_1'=>$request->family_sched_1,
                    'family_sched_2'=>$request->family_sched_2,
                    'grouping_type'=>$request->grouping_type,
                    'ind_out_of_pocket_action'=>$request->ind_out_of_pocket_action,
                    'gpi_exclusion_list'=>$request->gpi_exclusion_list,
                    'gpi_exclusion_list_ded'=>$request->gpi_exclusion_list_ded,
                    'gpi_exclusion_list_mop'=>$request->gpi_exclusion_list_mop,

                    
                 

                ]
            );

        $benefitcode = DB::table('PLAN_ACCUM_DEDUCT_TABLE')->where('plan_accum_deduct_id', 'like', '%'.$request->plan_accum_deduct_id .'%')->first();

        }

       

           


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }


    


    public function get_all(Request $request)

    {
        $accumlated_benefit_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')->get();

        if($accumlated_benefit_names){
            return $this->respondWithToken($this->token(), 'Data fetched Successfully', $accumlated_benefit_names);
        }

        else{
            return $this->respondWithToken($this->token(), 'Data Not Found', $accumlated_benefit_names);

        }

    }



    


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

                            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function getDetails ($ncdid)
    {
                    $ndc = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                    // ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                            ->where('PLAN_ACCUM_DEDUCT_ID', $ncdid)
                            ->orWhere('PLAN_ACCUM_DEDUCT_NAME',$ncdid)
                            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }



}
