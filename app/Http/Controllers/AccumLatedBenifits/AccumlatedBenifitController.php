<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccumlatedBenifitController extends Controller
{
    use AuditTrait;
    public function add(Request $request)
    {
        $createddate = date('Ymd');
        $recordcheck = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
            ->where(DB::raw('UPPER(plan_accum_deduct_id)'), strtoupper($request->plan_accum_deduct_id))
            ->first();



            // $validator = Validator::make($request->all(), [

            //     "plan_accum_deduct_id" =>['required','max:10'],
            //      "plan_accum_deduct_name" => ['string','max:35'],


            // ]);

            $validator = Validator::make($request->all(), [

                                'plan_accum_deduct_id' =>['required','string','max:10'],
                                'plan_accum_deduct_name' => ['string','max:35'],
                                'ndc_exclusion_list' => ['max:10'],
                                'accum_bene_price_schedule' => ['max:10'],
                                'aggregate_type_ded' => ['max:1'],
                                'aggregate_type_max' => ['string','max:1'],
                                'aggregate_type_mop' => ['string','max:1'],
                                'apply_indiv_limit_ded' =>['string','max:1'],
                                'apply_indiv_limit_max' => ['string','max:1'],
                                'apply_indiv_limit_mop' => ['string','max:1'],
                                'benefit_grouping_type' => ['nullable'],
                                'deduc_period' => ['max:2'],
                                'exclude_brand_generics' => ['max:1'],
                                'exclude_in_network' => ['max:1'],
                                'exclude_in_network_ded' => ['max:1'],
                                'exclude_in_network_mop' => ['max:1'],
                                'exclude_mail_ord_ded' => ['max:1'],
                                'exclude_mail_ord_mop' => ['max:1'],
                                'exclude_mail_order' => ['max:1'],
                                'exclude_maint_drug_ded' => ['max:1'],
                                'exclude_maint_drug_mop' => ['max:1'],
                                'exclude_maintenance_drugs' => ['max:1'],
                                'exclude_patient_differential' => ['max:1'],
                                'family_action_1' => ['max:2'],
                                'family_action_2' => ['max:2'],
                                'family_benefit_1' => ['numeric:digits:10','nullable'],
                                'family_benefit_2' => ['numeric:digits:10','nullable'],
                                'family_copay_1' => ['max:10'],
                                'family_copay_2' => ['max:10'],
                                'family_deductible' =>['numeric:digits:10','nullable'],
                                'family_out_of_pocket_action' => ['max:10'],
                                'family_sched_1' => ['max:10'],
                                'family_sched_2' => ['max:10'],
                                'grouping_type' => ['max:1'],
                                'ind_out_of_pocket_action' => ['max:2'],
                                'gpi_exclusion_list' => ['max:10'],
                                'gpi_exclusion_list_ded' => ['max:10'],
                                'gpi_exclusion_list_mop' => ['max:10'],
                                'deduc_start' =>  ['numeric:digits:10','nullable'],
                                'deduc_start_day' => ['numeric:digits:2','nullable'],
                                'deduct_refresh_option' => ['max:1'],
                                'exclude_brand_generics_ded' => ['max:1'],
                                'exclude_brand_generics_mop' => ['max:1'],
                                'exclude_ded_from_mop_flag' => ['max:1'],
                                'exclude_drug_over_days' =>   ['numeric:digits:4','nullable'],
                                'exclude_drug_over_days_ded' =>  ['numeric:digits:3','nullable'],
                                'exclude_drug_over_days_mop' =>  ['numeric:digits:3','nullable'],
                                'exclude_generics' => ['max:1'],
                                'exclude_generics_ded' => ['max:1'],
                                'exclude_generics_mop' => ['max:1'],
                                'fam_max_rxs_action' => ['max:2'],
                                'fam_max_rxs_copay_schedule' => ['max:10'],
                                'fam_max_rxs_per_ded_period' =>  ['numeric:digits:4','nullable'],
                                'family_out_of_pocket' =>['numeric:digits:10','nullable'],
                                'family_out_of_pocket_schedule' => ['nullable','max:10'],
                                'ind_action_1' => ['max:2'],
                                'ind_action_2' => ['max:2'],
                                'ind_benefit_1' => ['numeric:digits:10','nullable'],
                                'ind_benefit_2' =>['numeric:digits:10','nullable'],
                                'ind_copay_1' => ['max:10'],
                                'ind_copay_2' =>['max:10'],
                                'ind_deductible' => ['numeric:digits:10','nullable'],
                                'ind_out_of_pocket' => ['numeric:digits:10','nullable'],
                                'ind_out_of_pocket_schedule' => ['max:10'],
                                'ind_sched_1' => ['max:10'],
                                'ind_sched_2' =>['max:10'],
                                'max_rxs_action' => ['max:2'],
                                'max_rxs_copay_schedule' => ['max:10'],
                                'max_rxs_per_ded_period' => ['max:4'],
                                'ndc_exclusion_list_ded' => ['max:10'],
                                'ndc_exclusion_list_mop' => ['max:10'],

            ]);


            if ($validator->fails()) {


                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                                // dd($request->all());
                     $recordcheck = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                     ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                     ->first();


                if ($request->has('new')) {
                    if ($recordcheck) {
                        return $this->respondWithToken($this->token(), 'Plan ID already exists', $recordcheck, false);
                    } else {

                        // dd($request->all());

        
                        $accum_benfit_stat = DB::table('PLAN_ACCUM_DEDUCT_TABLE')->insert(
                            [
                                'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                                'plan_accum_deduct_name' => $request->plan_accum_deduct_name,
                                'ndc_exclusion_list' => $request->ndc_exclusion_list,
                                'accum_bene_price_schedule' => $request->accum_bene_price_schedule,
                                'aggregate_type_ded' => $request->aggregate_type_ded,
                                'aggregate_type_max' => $request->aggregate_type_max,
                                'aggregate_type_mop' => $request->aggregate_type_mop,
                                'apply_indiv_limit_ded' => $request->apply_indiv_limit_ded,
                                'apply_indiv_limit_max' => $request->apply_indiv_limit_max,
                                'apply_indiv_limit_mop' => $request->apply_indiv_limit_mop,
                                'benefit_grouping_type' => $request->benefit_grouping_type,
                                'deduc_period' => $request->deduc_period,
                                'exclude_brand_generics' => $request->exclude_brand_generics,
                                'exclude_in_network' => $request->exclude_in_network,
                                'exclude_in_network_ded' => $request->exclude_in_network_ded,
                                'exclude_in_network_mop' => $request->exclude_in_network_mop,
                                'exclude_mail_ord_ded' => $request->exclude_mail_ord_ded,
                                'exclude_mail_ord_mop' => $request->exclude_mail_ord_mop,
                                'exclude_mail_order' => $request->exclude_mail_order,
                                'exclude_maint_drug_ded' => $request->exclude_maint_drug_ded,
                                'exclude_maint_drug_mop' => $request->exclude_maint_drug_mop,
                                'exclude_maintenance_drugs' => $request->exclude_maintenance_drugs,
                                'exclude_patient_differential' => $request->exclude_patient_differential,
                                'family_action_1' => $request->family_action_1,
                                'family_action_2' => $request->family_action_2,
                                'family_benefit_1' => $request->family_benefit_1,
                                'family_benefit_2' => $request->family_benefit_2,
                                'family_copay_1' => $request->family_copay_1,
                                'family_copay_2' => $request->family_copay_2,
                                'family_deductible' => $request->family_deductible,
                                'family_out_of_pocket_action' => $request->family_out_of_pocket_action,
                                'family_sched_1' => $request->family_sched_1,
                                'family_sched_2' => $request->family_sched_2,
                                'grouping_type' => $request->grouping_type,
                                'ind_out_of_pocket_action' => $request->ind_out_of_pocket_action,
                                'gpi_exclusion_list' => $request->gpi_exclusion_list,
                                'gpi_exclusion_list_ded' => $request->gpi_exclusion_list_ded,
                                'gpi_exclusion_list_mop' => $request->gpi_exclusion_list_mop,
                                'deduc_start' => $request->deduc_start,
                                'deduc_start_day' => $request->deduc_start_day,
                                'deduct_refresh_option' => $request->deduct_refresh_option,
                                'exclude_brand_generics_ded' => $request->exclude_brand_generics_ded,
                                'exclude_brand_generics_mop' => $request->exclude_brand_generics_mop,
                                'exclude_ded_from_mop_flag' => $request->exclude_ded_from_mop_flag,
                                'exclude_drug_over_days' => $request->exclude_drug_over_days,
                                'exclude_drug_over_days_ded' => $request->exclude_drug_over_days_ded,
                                'exclude_drug_over_days_mop' => $request->exclude_drug_over_days_mop,
                                'exclude_generics' => $request->exclude_generics,
                                'exclude_generics_ded' => $request->exclude_generics_ded,
                                'exclude_generics_mop' => $request->exclude_generics_mop,
                                'fam_max_rxs_action' => $request->fam_max_rxs_action,
                                'fam_max_rxs_copay_schedule' => $request->fam_max_rxs_copay_schedule,
                                'fam_max_rxs_per_ded_period' => $request->fam_max_rxs_per_ded_period,
                                'family_out_of_pocket' => $request->family_out_of_pocket,
                                'family_out_of_pocket_schedule' => $request->family_out_of_pocket_schedule,
                                'ind_action_1' => $request->ind_action_1,
                                'ind_action_2' => $request->ind_action_2,
                                'ind_benefit_1' => $request->ind_benefit_1,
                                'ind_benefit_2' => $request->ind_benefit_2,
                                'ind_copay_1' => $request->ind_copay_1,
                                'ind_copay_2' => $request->ind_copay_2,
                                'ind_deductible' => $request->ind_deductible,
                                'ind_out_of_pocket' => $request->ind_out_of_pocket,
                                'ind_out_of_pocket_schedule' => $request->ind_out_of_pocket_schedule,
                                'ind_sched_1' => $request->ind_sched_1,
                                'ind_sched_2' => $request->ind_sched_2,
                                'max_rxs_action' => $request->max_rxs_action,
                                'max_rxs_copay_schedule' => $request->max_rxs_copay_schedule,
                                'max_rxs_per_ded_period' => $request->max_rxs_per_ded_period,
                                'ndc_exclusion_list_ded' => $request->ndc_exclusion_list_ded,
                                'ndc_exclusion_list_mop' => $request->ndc_exclusion_list_mop,
                                'DATE_TIME_CREATED' => $createddate,
                                'DATE_TIME_MODIFIED' => $createddate,
                                'USER_ID' => Cache::get('userId'),
                                'USER_ID_CREATED' => Cache::get('userId'),
        
        
                            ]
                        );
        
                        $accum_bene  = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                            ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                            ->first();
                        $record_snap = json_encode($accum_bene);
                        // $save_audit = $this->auditMethod('IN', $record_snap, 'PLAN_ACCUM_DEDUCT_TABLE');
                        return $this->respondWithToken($this->token(), 'Record Added Succesfully', $accum_bene);
                    }
                } else {
        
                    $createddate = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                        ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                        ->update(
                            [

                                'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                                'plan_accum_deduct_name' => $request->plan_accum_deduct_name,
                                'ndc_exclusion_list' => $request->ndc_exclusion_list,
                                'accum_bene_price_schedule' => $request->accum_bene_price_schedule,
                                'aggregate_type_ded' => $request->aggregate_type_ded,
                                'aggregate_type_max' => $request->aggregate_type_max,
                                'aggregate_type_mop' => $request->aggregate_type_mop,
                                'apply_indiv_limit_ded' => $request->apply_indiv_limit_ded,
                                'apply_indiv_limit_max' => $request->apply_indiv_limit_max,
                                'apply_indiv_limit_mop' => $request->apply_indiv_limit_mop,
                                'benefit_grouping_type' => $request->benefit_grouping_type,
                                'deduc_period' => $request->deduc_period,
                                'exclude_brand_generics' => $request->exclude_brand_generics,
                                'exclude_in_network' => $request->exclude_in_network,
                                'exclude_in_network_ded' => $request->exclude_in_network_ded,
                                'exclude_in_network_mop' => $request->exclude_in_network_mop,
                                'exclude_mail_ord_ded' => $request->exclude_mail_ord_ded,
                                'exclude_mail_ord_mop' => $request->exclude_mail_ord_mop,
                                'exclude_mail_order' => $request->exclude_mail_order,
                                'exclude_maint_drug_ded' => $request->exclude_maint_drug_ded,
                                'exclude_maint_drug_mop' => $request->exclude_maint_drug_mop,
                                'exclude_maintenance_drugs' => $request->exclude_maintenance_drugs,
                                'exclude_patient_differential' => $request->exclude_patient_differential,
                                'family_action_1' => $request->family_action_1,
                                'family_action_2' => $request->family_action_2,
                                'family_benefit_1' => $request->family_benefit_1,
                                'family_benefit_2' => $request->family_benefit_2,
                                'family_copay_1' => $request->family_copay_1,
                                'family_copay_2' => $request->family_copay_2,
                                'family_deductible' => $request->family_deductible,
                                'family_out_of_pocket_action' => $request->family_out_of_pocket_action,
                                'family_sched_1' => $request->family_sched_1,
                                'family_sched_2' => $request->family_sched_2,
                                'grouping_type' => $request->grouping_type,
                                'ind_out_of_pocket_action' => $request->ind_out_of_pocket_action,
                                'gpi_exclusion_list' => $request->gpi_exclusion_list,
                                'gpi_exclusion_list_ded' => $request->gpi_exclusion_list_ded,
                                'gpi_exclusion_list_mop' => $request->gpi_exclusion_list_mop,
                                'deduc_start' => $request->deduc_start,
                                'deduc_start_day' => $request->deduc_start_day,
                                'deduct_refresh_option' => $request->deduct_refresh_option,
                                'exclude_brand_generics_ded' => $request->exclude_brand_generics_ded,
                                'exclude_brand_generics_mop' => $request->exclude_brand_generics_mop,
                                'exclude_ded_from_mop_flag' => $request->exclude_ded_from_mop_flag,
                                'exclude_drug_over_days' => $request->exclude_drug_over_days,
                                'exclude_drug_over_days_ded' => $request->exclude_drug_over_days_ded,
                                'exclude_drug_over_days_mop' => $request->exclude_drug_over_days_mop,
                                'exclude_generics' => $request->exclude_generics,
                                'exclude_generics_ded' => $request->exclude_generics_ded,
                                'exclude_generics_mop' => $request->exclude_generics_mop,
                                'fam_max_rxs_action' => $request->fam_max_rxs_action,
                                'fam_max_rxs_copay_schedule' => $request->fam_max_rxs_copay_schedule,
                                'fam_max_rxs_per_ded_period' => $request->fam_max_rxs_per_ded_period,
                                'family_out_of_pocket' => $request->family_out_of_pocket,
                                'family_out_of_pocket_schedule' => $request->family_out_of_pocket_schedule,
                                'ind_action_1' => $request->ind_action_1,
                                'ind_action_2' => $request->ind_action_2,
                                'ind_benefit_1' => $request->ind_benefit_1,
                                'ind_benefit_2' => $request->ind_benefit_2,
                                'ind_copay_1' => $request->ind_copay_1,
                                'ind_copay_2' => $request->ind_copay_2,
                                'ind_deductible' => $request->ind_deductible,
                                'ind_out_of_pocket' => $request->ind_out_of_pocket,
                                'ind_out_of_pocket_schedule' => $request->ind_out_of_pocket_schedule,
                                'ind_sched_1' => $request->ind_sched_1,
                                'ind_sched_2' => $request->ind_sched_2,
                                'max_rxs_action' => $request->max_rxs_action,
                                'max_rxs_copay_schedule' => $request->max_rxs_copay_schedule,
                                'max_rxs_per_ded_period' => $request->max_rxs_per_ded_period,
                                'ndc_exclusion_list_ded' => $request->ndc_exclusion_list_ded,
                                'ndc_exclusion_list_mop' => $request->ndc_exclusion_list_mop,
                                'DATE_TIME_CREATED' => $createddate,
                                'DATE_TIME_MODIFIED' => $createddate,
                                'USER_ID' => Cache::get('userId'),
                                'USER_ID_CREATED' => Cache::get('userId'),
                                
                            ]
                        );
                        // dd($request->all());

                    $accum_bene  = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                        ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                        ->first();
                    $record_snap = json_encode($accum_bene);
                    // $save_audit = $this->auditMethod('UP', $record_snap, 'PLAN_ACCUM_DEDUCT_TABLE');
                    return $this->respondWithToken($this->token(), 'Record Updated Succesfully', $accum_bene);
                }

            }

        
    }

    public function delete(Request $request)
    {
        if (isset($request->plan_accum_deduct_id)) {
            $to_delete =  DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                ->first();
            $save_audit_delete  = $this->auditMethod('DE', json_encode($to_delete), 'PLAN_ACCUM_DEDUCT_TABLE');

            $delete_plan_accum_deduct_id =  DB::table('PLAN_ACCUM_DEDUCT_TABLE')
                ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                ->delete();
            if ($delete_plan_accum_deduct_id) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
        }
    }

    public function searchNew(Request $request)

    {
        $ndc = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
            //     ->where('PLAN_ACCUM_DEDUCT_ID', 'like', '%' . $request->search . '%')
            ->whereRaw('LOWER(PLAN_ACCUM_DEDUCT_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orWhere('PLAN_ACCUM_DEDUCT_NAME', 'like', '%' . $request->search . '%')
            ->paginate(100);

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function get_all(Request $request)

    {
        $accumlated_benefit_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')->get();

        if ($accumlated_benefit_names) {
            return $this->respondWithToken($this->token(), 'Data fetched Successfully', $accumlated_benefit_names);
        } else {
            return $this->respondWithToken($this->token(), 'Data Not Found', $accumlated_benefit_names);
        }
    }






    public function search(Request $request)

    {
        $ndc = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
            ->where(DB::raw('UPPER(PLAN_ACCUM_DEDUCT_ID)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere(DB::raw('UPPER(PLAN_ACCUM_DEDUCT_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList(Request $request)
    {
        $ndc = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
            // ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            ->where('PLAN_ACCUM_DEDUCT_ID', 'like', '%' . $request->search . '%')
            ->orWhere('PLAN_ACCUM_DEDUCT_NAME', 'like', '%' . $request->search . '%')

            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($ncdid)
    {
        $ndc = DB::table('PLAN_ACCUM_DEDUCT_TABLE')
            // ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            ->where('PLAN_ACCUM_DEDUCT_ID', $ncdid)
            ->orWhere('PLAN_ACCUM_DEDUCT_NAME', $ncdid)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
