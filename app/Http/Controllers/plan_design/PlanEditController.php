<?php

namespace App\Http\Controllers\plan_design;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\AuditTrait;
use App\Models\PlanBenefitTable;

class PlanEditController extends Controller
{

    use AuditTrait;

    public function getCopaydropDown()

    {

        $PolicyAnnualMonth = [

            ['name' => 'January', 'code' => 'Jan'],

            ['name' => 'February', 'code' => 'Feb'],

            ['name' => 'March', 'code' => 'Mar'],

            ['name' => 'April', 'code' => 'Apl'],

            ['name' => 'May', 'code' => 'May'],

            ['name' => 'June', 'code' => ''],

            ['name' => 'July', 'code' => ''],

            ['name' => 'August', 'code' => 'Aug'],

            ['name' => 'September', 'code' => 'Sep'],

            ['name' => 'October', 'code' => 'Oct'],

            ['name' => 'November', 'code' => 'Nov'],

            ['name' => 'December', 'code' => 'Dec'],

        ];
        return $this->respondWithToken($this->token(), '', $PolicyAnnualMonth);
    }






    public function add(Request $request)
    {
        $getData = DB::table('PLAN_BENEFIT_TABLE')
            ->where('PLAN_ID', $request->plan_id)
            // ->Where('LABEL_NAME',strtoupper($request->label_name))
            // ->Where('GENERIC_NAME',strtoupper($request->generic_name))
            // ->Where('PACKAGE_SIZE',strtoupper($request->package_size))
            ->first();

        if ($request->add_new == "1") {

            // if ($getData) {
            //     return $this->respondWithToken($this->token(), ['Plan ID is Already Exists','kjyutdfs'], $getData, false);
            // }

            $validator = Validator::make($request->all(), [
                'plan_id' => ['required', 'max:15', Rule::unique('PLAN_BENEFIT_TABLE')->where(function ($q) {
                    $q->whereNotNull('plan_id');
                })],
                'effective_date' => ['required', 'max:10'],
                'termination_date' => ['required', 'after:effective_date'],
                'plan_name' => ['max:35'],
                // 'default_drug_status' => ['max:2'],
                // 'default_price_schedule' => ['max:10'],
                // 'mac_list' => ['max:10'],
                // 'pharmacy_exceptions_flag' => ['max:1'],
                // 'eligibility_exceptions_flag' => ['max:1'],
                // 'prescriber_exceptions_flag' => ['max:1'],
                // 'drug_catgy_excpt_flag' => ['max:1'],
                // 'ndc_exception_list' => ['max:10'],
                // 'gpi_exception_list' => ['max:10'],
                // 'ther_class_exception_list' => ['max:10'],
                'min_rx_qty' => ['nullable'],
                'max_rx_qty' => ['nullable','gt:min_rx_qty'],
                'min_rx_days' => ['nullable'],
                'max_rx_days' => ['nullable','gt:min_rx_days'],
                'min_ctl_days' => ['nullable'],
                'max_ctl_days' => ['nullable','gt:min_ctl_days'],
                // 'max_refills' => ['max:6'],
                // 'max_days_per_fill' => ['max:6'],
                // 'max_dose' => ['max:6'],
                'min_age' => ['nullable'],
                'max_age' => ['nullable','gt:min_age'],
                // 'min_price' => ['max:12'],
                // 'max_price' => ['max:12'],
                // 'max_rxs_patient' => ['max:6'],
                // 'max_price_patiennt' => ['max:12'],
                // 'generic_copay_amt' => ['max:12'],
                // 'brand_copay_amt' => ['max:12'],
                // 'max_rxs_time_flag' => ['max:6'],
                // 'max_price_time_flag' => ['max:6'],
                // 'qty_dsup_compare_rule' => ['max:6'],
                // 'plan_classification' => ['max:1'],
                // 'dmr_price_schedule' => ['max:10'],
                // 'max_days_supply_opt' => ['max:1'],
                // 'retail_max_fills_opt' => ['max:1'],
                // 'mail_ord_max_fills_opt' => ['max:1'],
                // 'min_price_opt' => ['max:1'],
                // 'max_price_opt' => ['max:1'],
                // 'min_brand_copay_amt' => ['max:12'],
                // 'max_brand_copay_amt' => ['max:12'],
                // 'max_brand_copay_opt' => ['max:1'],
                // 'min_generic_copay_amt' => ['max:12'],
                // 'max_generic_copay_amt' => ['max:12'],
                // 'drug_catgy_exception_list' => ['max:10'],
                // 'starter_dose_days' => ['max:3'],
                // 'starter_dose_bypass_days' => ['max:3'],
                // 'drug_cov_start_days' => ['max:3'],
                // 'super_rx_network_id' => ['max:10'],
                // 'max_rx_qty_opt' => ['max:1'],
                // 'max_qty_over_time' => ['max:6'],
                // 'max_days_over_time' => ['max:6'],
                // 'starter_dose_maint_bypass_days' => ['max:3'],
                // 'max_qty_per_fill' => ['max:8'],
                // 'age_limit_opt' => ['max:1'],
                // 'age_limit_mmdd' => ['max:4'],
                // 'pricing_strategy_id' => ['max:10'],
                // 'accume_bene_strategy_id' => ['max:10'],
                // 'copay_strategy_id' => ['max:10'],
                // 'exhausted_benefit_opt' => ['max:10'],
                // 'exhausted_benefit_plan_id' => ['max:15'],
                // 'coverage_start_days' => ['max:6'],
                // 'benefit_derivation_id' => ['max:10'],
                // 'prov_type_proc_assoc_id' => ['max:10'],
                // 'prov_type_list_id' => ['max:10'],
                // 'super_benefit_list_id' => ['max:10'],
                // 'super_benefit_list_id_2' => ['max:10'],
                // 'procedure_ucr_id' => ['max:10'],
                // 'procedure_xref_id' => ['max:10'],
            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' =>  'Max Age must be greater than Min Age',
                'max_rx_qty.gt' =>  'Max Qty must be greater than Min Qty',
                'max_rx_days.gt' => 'Max Day must be greater than Min Day',
                'max_ctl_days.gt' =>  'Max Ctl must be greater than Min Ctl',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } 
            // else {

            $plan_benifit = new PlanBenefitTable;
            // $plan_benifit->PLAN_ID = strtoupper($request->plan_id);
            // $plan_benifit->EFFECTIVE_DATE = date('Ymd', strtotime($request->effective_date));
            // $plan_benifit->PLAN_NAME = strtoupper($request->plan_name);
            // $plan_benifit->DEFAULT_DRUG_STATUS = strtoupper($request->default_drug_status);
            // $plan_benifit->DEFAULT_PRICE_SCHEDULE = strtoupper($request->default_price_schedule);
            // $plan_benifit->MAC_LIST = strtoupper($request->mac_list);
            // $plan_benifit->PHARMACY_EXCEPTIONS_FLAG = strtoupper($request->pharmacy_exceptions_flag);
            // $plan_benifit->PRESCRIBER_EXCEPTIONS_FLAG = strtoupper($request->prescriber_exceptions_flag);
            // $plan_benifit->save();
            // return $plan_benifit;

            $addData = DB::table('PLAN_BENEFIT_TABLE')
                ->insert([
                    'PLAN_ID' => $request->plan_id,
                    'EFFECTIVE_DATE' => date('Ymd', strtotime($request->effective_date)),
                    'PLAN_NAME' => $request->plan_name,
                    'DEFAULT_DRUG_STATUS' =>$request->default_drug_status,
                    'DEFAULT_PRICE_SCHEDULE' =>$request->default_price_schedule,
                    'MAC_LIST' => $request->mac_list,
                    'PHARMACY_EXCEPTIONS_FLAG' => $request->pharmacy_exceptions_flag,
                    'PRESCRIBER_EXCEPTIONS_FLAG' => $request->prescriber_exceptions_flag,
                    'DRUG_CATGY_EXCPT_FLAG' => $request->drug_catgy_excpt_flag,
                    'NDC_EXCEPTION_LIST' =>$request->ndc_exception_list,
                    'GPI_EXCEPTION_LIST' => $request->gpi_exception_list,
                    'THER_CLASS_EXCEPTION_LIST' =>($request->ther_class_exception_list),
                    'TERMINATION_DATE' => date('Ymd', strtotime($request->termination_date)),
                    'MIN_RX_QTY' =>($request->min_rx_qty),
                    'MAX_RX_QTY' => $request->max_rx_qty,
                    'MIN_RX_DAYS' =>($request->min_rx_days),
                    'MAX_RX_DAYS' =>($request->max_rx_days),
                    'MIN_CTL_DAYS' =>($request->min_ctl_days),
                    'MAX_CTL_DAYS' =>($request->max_ctl_days),
                    'MAX_REFILLS' => $request->max_refills,
                    'MAX_DAYS_PER_FILL' => ($request->max_days_per_fill),
                    'MAX_DOSE' => ($request->max_dose),
                    'MIN_AGE' => $request->min_age,
                    'MAX_AGE' => $request->max_age,
                    'MIN_PRICE' => $request->min_price,
                    'MAX_PRICE' => $request->max_price,
                    'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                    'MAX_PRICE_PATIENT' => $request->max_price_patient,
                    'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                    'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                    'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                    'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                    'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                    'SPONSOR_ID' => $request->sponsor_id,
                    'PLAN_CLASSIFICATION' => $request->plan_classification,
                    'DMR_PRICE_SCHEDULE' => $request->dmr_price_schedule,
                    'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                    'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                    'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                    'MIN_PRICE_OPT' => $request->min_price_opt,
                    'MAX_PRICE_OPT' => $request->max_price_opt,
                    'MIN_BRAND_COPAY_AMT' => $request->min_brand_copay_amt,
                    'MAX_BRAND_COPAY_AMT' => $request->max_brand_copay_amt,
                    'MAX_BRAND_COPAY_OPT' => $request->max_brand_copay_opt,
                    'MIN_GENERIC_COPAY_AMT' => $request->min_generic_copay_amt,
                    'MAX_GENERIC_COPAY_AMT' => $request->max_generic_copay_amt,
                    'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                    'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                    'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                    'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                    'SUPER_RX_NETWORK_ID' => $request->super_rx_network_id,
                    'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                    'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                    'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                    'USER_ID_CREATED' => $request->user_id_created,
                    'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                    'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                    'AGE_LIMIT_OPT' => $request->age_limit_opt,
                    'AGE_LIMIT_MMDD' => $request->age_limit_mmdd,
                    'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                    'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                    'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                    'PROCEDURE_EXCEPTION_LIST' => $request->procedure_exception_list,
                    'EXHAUSTED_BENEFIT_OPT' => $request->exhausted_benefit_opt,
                    'EXHAUSTED_BENEFIT_PLAN_ID' => $request->exhausted_benefit_plan_id,
                    'COVERAGE_START_DAYS' => $request->coverage_start_days,
                    'BENEFIT_DERIVATION_ID' => $request->benefit_derivation_id,
                    'PROV_TYPE_PROC_ASSOC_ID' => $request->prov_type_proc_assoc_id,
                    'SUPER_BENEFIT_LIST_ID' => $request->super_benefit_list_id,
                    'SUPER_BENEFIT_LIST_ID_2' => $request->super_benefit_list_id_2,
                    'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    'prov_type_list_id' => $request->prov_type_list_id,
                    'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                ]);
            // if(! $addData){
            //     return "failed";
            // }
            $add_extensions = DB::table('plan_table_extensions')
                ->insert([
                    'plan_id' => $request->plan_id,
                    // 'DEFAULT_DRUG_STATUS' => $request->default_drug_status,
                    // 'TERMINATION_DATE' => $request->termination_date,
                    // 'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                    'EFFECTIVE_DATE' => date('Ymd', strtotime($request->effective_date)),
                    'DATE_WRITTEN_TO_FIRST_FILL' => $request->date_written_to_first_fill,
                    'DATE_FILLED_TO_SUB_ONLINE' => $request->date_filled_to_sub_online,
                    'DATE_FILLED_TO_SUB_DMR' => $request->date_filled_to_sub_dmr,
                    'DATE_SUB_TO_FILLED_FUTURE' => $request->date_sub_to_filled_future,
                    'DAYS_FOR_REVERSALS' => $request->days_for_reversals,
                    'MISC_FLAG_3' => $request->misc_flag_3, //tax status
                    'MISC_FLAG_4' => $request->misc_flag_4, //mandatory u & c
                    'MISC_FLAG_1' => $request->misc_flag_1, //SYRINGES WITH ISSUING SAME DAY
                    'MISC_FLAG_5' => $request->misc_flag_5, //EXCLUDE SYSTEM NDC/GPI FORMULARY EDITS FOR OUT OF NETWORK CLAIM
                    'MISC_FLAG_6' => $request->misc_flag_6, //EXCLUDE PLAN NDC/GPI FORMULARY EDITS FOR OUT OF NETWORK CLAIM
                    'MISC_FLAG_7' => $request->misc_flag_7, //REJECT CLAIM FOR MISSING CARDHOLDER ID
                    // 'ER_LIMIT_1_MAX_DAYS_SUPPLY' => $request->er_limit_max_days_supply, //LIMIT1 (RX MAXIMUM DAYS SUPPLY
                    'ER_LIMIT_1_MINIMUM_USE' => $request->er_limit_1_minimum_use, //LIMIT1  MINIMUM USE PERCENTAGE) 
                    'ER_LIMIT_2_MAX_DAYS_SUPPLY' => $request->er_limit_2_max_days_supply, //LIMIT 2 - ABOVE LIMIT 1(RX MAXIMUM DAYS SUPPLY
                    'ER_LIMIT_2_MINIMUM_USE' => $request->er_limit_2_minimum_use, //LIMIT 2 - ABOVE LIMIT 1 MINIMUM USE PERCENTAGE)
                    'ER_LIMIT_X_MINIMUM_USE' => $request->er_limit_x_minimum_use, //ABOVE LIMIT2(MAXIMUM USE MAXIMUM
                    'ER_SEARCH_IND' => $request->er_search_ind, //ABOVE LIMIT2 SEARCH INDICATION)
                    'MO_ER_LIMIT_1_MAX_DAYS_SUPPLY' => $request->mo_er_limit_1_max_days_supply, //LIMIT1 (RX MAXIMUM DAYS SUPPLY
                    'MO_ER_LIMIT_1_MINIMUM_USE' => $request->mo_er_limit_1_minimum_use, //LIMIT1  MINIMUM USE PERCENTAGE) 
                    'MO_ER_LIMIT_2_MAX_DAYS_SUPPLY' => $request->mo_er_limit_2_max_days_supply, //LIMIT 2 - ABOVE LIMIT 1(RX MAXIMUM DAYS SUPPLY
                    'MO_ER_LIMIT_X_MINIMUM_USE' => $request->mo_er_limit_x_minimum_use,
                    // 'mo_er_limit_x_max_days_supply' => $request->mo_er_limit_x_max_days_supply,
                    'plan_notes' => $request->plan_notes,
                    'mo_er_limit_2_minimum_use' => $request->mo_er_limit_2_minimum_use,
                    'ER_BYPASS_HIST_DAYS_SUPPLY' => $request->er_bypass_hist_days_supply,
                    'MO_ER_BYPASS_HIST_DAYS_SUPPLY' => $request->mo_er_bypass_hist_days_supply,
                    'mo_er_search_ind' => $request->mo_er_search_ind,
                    // 'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                    'ER_LIMIT_1_MAX_DAYS_SUPPLY' => $request->er_limit_1_max_days_supply,
                ]);

            $addData = DB::table('PLAN_BENEFIT_TABLE')
                ->leftJoin('PLAN_TABLE_EXTENSIONS', 'PLAN_BENEFIT_TABLE.plan_id', '=', 'PLAN_TABLE_EXTENSIONS.plan_id')
                // ->where('PLAN_BENEFIT_TABLE.plan_id', $request->plan_id)
                ->where(DB::raw('UPPER(PLAN_BENEFIT_TABLE.plan_id)'), strtoupper($request->plan_id))

                ->first();


            $plan_benefit_table = DB::table('PLAN_BENEFIT_TABLE')
                ->where(DB::raw('UPPER(plan_id)'), strtoupper('plan_id'))
                ->first();

            $plan_table_ext = DB::table('PLAN_TABLE_EXTENSIONS')
                ->where(DB::raw('UPPER(plan_id)'), strtoupper('plan_id'))
                ->first();
            $record_snapshot_benefit = json_encode($addData);
            $save_audit = $this->auditMethod('IN', $record_snapshot_benefit, 'PLAN_BENEFIT_TABLE');

            $record_snapshot_ext = json_encode($plan_table_ext);
            $save_audit_ext = $this->auditMethod('IN', $record_snapshot_ext, 'PLAN_TABLE_EXTENSIONS');

            if ($addData) {
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $addData);
            }

            if ($addData) {
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $addData);
            }
            // }
        } else { 
            
            $validator = Validator::make($request->all(), [
                'plan_id' => ['required', 'max:15', Rule::unique('PLAN_BENEFIT_TABLE')->where(function ($q) use($request){
                    $q->whereNotNull('plan_id');
                    $q->where('plan_id','!=',$request->plan_id);
                })],
                'effective_date' => ['required', 'max:10'],
                'termination_date' => ['required', 'after:effective_date'],
                'plan_name' => ['max:35'],
                // 'default_drug_status' => ['max:2'],
                // 'default_price_schedule' => ['max:10'],
                // 'mac_list' => ['max:10'],
                // 'pharmacy_exceptions_flag' => ['max:1'],
                // 'eligibility_exceptions_flag' => ['max:1'],
                // 'prescriber_exceptions_flag' => ['max:1'],
                // 'drug_catgy_excpt_flag' => ['max:1'],
                // 'ndc_exception_list' => ['max:10'],
                // 'gpi_exception_list' => ['max:10'],
                // 'ther_class_exception_list' => ['max:10'],
                'min_rx_qty' => ['nullable'],
                'max_rx_qty' => ['nullable','gt:min_rx_qty'],
                'min_rx_days' => ['nullable'],
                'max_rx_days' => ['nullable','gt:min_rx_days'],
                'min_ctl_days' => ['nullable'],
                'max_ctl_days' => ['nullable','gt:min_ctl_days'],
                // 'max_refills' => ['max:6'],
                // 'max_days_per_fill' => ['max:6'],
                // 'max_dose' => ['max:6'],
                'min_age' => ['nullable'],
                'max_age' => ['nullable','gt:min_age'],
                // 'min_price' => ['max:12'],
                // 'max_price' => ['max:12'],
                // 'max_rxs_patient' => ['max:6'],
                // 'max_price_patiennt' => ['max:12'],
                // 'generic_copay_amt' => ['max:12'],
                // 'brand_copay_amt' => ['max:12'],
                // 'max_rxs_time_flag' => ['max:6'],
                // 'max_price_time_flag' => ['max:6'],
                // 'qty_dsup_compare_rule' => ['max:6'],
                // 'plan_classification' => ['max:1'],
                // 'dmr_price_schedule' => ['max:10'],
                // 'max_days_supply_opt' => ['max:1'],
                // 'retail_max_fills_opt' => ['max:1'],
                // 'mail_ord_max_fills_opt' => ['max:1'],
                // 'min_price_opt' => ['max:1'],
                // 'max_price_opt' => ['max:1'],
                // 'min_brand_copay_amt' => ['max:12'],
                // 'max_brand_copay_amt' => ['max:12'],
                // 'max_brand_copay_opt' => ['max:1'],
                // 'min_generic_copay_amt' => ['max:12'],
                // 'max_generic_copay_amt' => ['max:12'],
                // 'drug_catgy_exception_list' => ['max:10'],
                // 'starter_dose_days' => ['max:3'],
                // 'starter_dose_bypass_days' => ['max:3'],
                // 'drug_cov_start_days' => ['max:3'],
                // 'super_rx_network_id' => ['max:10'],
                // 'max_rx_qty_opt' => ['max:1'],
                // 'max_qty_over_time' => ['max:6'],
                // 'max_days_over_time' => ['max:6'],
                // 'starter_dose_maint_bypass_days' => ['max:3'],
                // 'max_qty_per_fill' => ['max:8'],
                // 'age_limit_opt' => ['max:1'],
                // 'age_limit_mmdd' => ['max:4'],
                // 'pricing_strategy_id' => ['max:10'],
                // 'accume_bene_strategy_id' => ['max:10'],
                // 'copay_strategy_id' => ['max:10'],
                // 'exhausted_benefit_opt' => ['max:10'],
                // 'exhausted_benefit_plan_id' => ['max:15'],
                // 'coverage_start_days' => ['max:6'],
                // 'benefit_derivation_id' => ['max:10'],
                // 'prov_type_proc_assoc_id' => ['max:10'],
                // 'prov_type_list_id' => ['max:10'],
                // 'super_benefit_list_id' => ['max:10'],
                // 'super_benefit_list_id_2' => ['max:10'],
                // 'procedure_ucr_id' => ['max:10'],
                // 'procedure_xref_id' => ['max:10'],
            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' =>  'Max Age must be greater than Min Age',
                'max_rx_qty.gt' =>  'Max Qty must be greater than Min Qty',
                'max_rx_days.gt' => 'Max Day must be greater than Min Day',
                'max_ctl_days.gt' =>  'Max Ctl must be greater than Min Ctl',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            }
                $updateData = DB::table('PLAN_BENEFIT_TABLE')
                    ->where('PLAN_ID', $request->plan_id)
                    ->update([
                        'EFFECTIVE_DATE' => date('Ymd', strtotime($request->effective_date)),
                        // 'effective_date' => $request->effective_date,
                        'PLAN_NAME' => $request->plan_name,
                        'DEFAULT_DRUG_STATUS' =>$request->default_drug_status,
                        'DEFAULT_PRICE_SCHEDULE' =>$request->default_price_schedule,
                        'MAC_LIST' =>$request->mac_list,
                        'PHARMACY_EXCEPTIONS_FLAG' => $request->pharmacy_exceptions_flag,
                        'PRESCRIBER_EXCEPTIONS_FLAG' =>$request->prescriber_exceptions_flag,
                        'DRUG_CATGY_EXCPT_FLAG' =>$request->drug_catgy_excpt_flag,
                        'NDC_EXCEPTION_LIST' =>$request->ndc_exception_list,
                        'GPI_EXCEPTION_LIST' =>$request->gpi_exception_list,
                        'THER_CLASS_EXCEPTION_LIST' =>$request->ther_class_exception_list,
                        'TERMINATION_DATE' => date('Ymd', strtotime($request->termination_date)),
                        'MIN_RX_QTY' => $request->min_rx_qty,
                        'MAX_RX_QTY' =>$request->max_rx_qty,
                        'MIN_RX_DAYS' =>$request->min_rx_days,
                        'MAX_RX_DAYS' =>$request->max_rx_days,
                        'MIN_CTL_DAYS' =>$request->min_ctl_days,
                        'MAX_CTL_DAYS' =>$request->max_ctl_days,
                        'MAX_REFILLS' => $request->max_refills,
                        'MAX_DAYS_PER_FILL' =>$request->max_days_per_fill,
                        'MAX_DOSE' =>$request->max_dose,
                        'MIN_AGE' => $request->min_age,
                        'MAX_AGE' => $request->max_age,
                        'MIN_PRICE' => $request->min_price,
                        'MAX_PRICE' => $request->max_price,
                        'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                        'MAX_PRICE_PATIENT' => $request->max_price_patient,
                        'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                        'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                        'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                        'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                        'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                        'SPONSOR_ID' => $request->sponsor_id,
                        'PLAN_CLASSIFICATION' => $request->plan_classification,
                        'DMR_PRICE_SCHEDULE' => $request->dmr_price_schedule,
                        'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                        'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                        'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                        'MIN_PRICE_OPT' => $request->min_price_opt,
                        'MAX_PRICE_OPT' => $request->max_price_opt,
                        'MIN_BRAND_COPAY_AMT' => $request->min_brand_copay_amt,
                        'MAX_BRAND_COPAY_AMT' => $request->max_brand_copay_amt,
                        'MAX_BRAND_COPAY_OPT' => $request->max_brand_copay_opt,
                        'MIN_GENERIC_COPAY_AMT' => $request->min_generic_copay_amt,
                        'MAX_GENERIC_COPAY_AMT' => $request->max_generic_copay_amt,
                        'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                        'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                        'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                        'SUPER_RX_NETWORK_ID' => $request->super_rx_network_id,
                        'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                        'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                        'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                        'USER_ID_CREATED' => $request->user_id_created,
                        'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                        'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                        'AGE_LIMIT_OPT' => $request->age_limit_opt,
                        'AGE_LIMIT_MMDD' => $request->age_limit_mmdd,
                        'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                        'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                        'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                        'PROCEDURE_EXCEPTION_LIST' => $request->procedure_exception_list,
                        'EXHAUSTED_BENEFIT_OPT' => $request->exhausted_benefit_opt,
                        'EXHAUSTED_BENEFIT_PLAN_ID' => $request->exhausted_benefit_plan_id,
                        'COVERAGE_START_DAYS' => $request->coverage_start_days,
                        'BENEFIT_DERIVATION_ID' => $request->benefit_derivation_id,
                        'PROV_TYPE_PROC_ASSOC_ID' => $request->prov_type_proc_assoc_id,
                        'SUPER_BENEFIT_LIST_ID' => $request->super_benefit_list_id,
                        'SUPER_BENEFIT_LIST_ID_2' => $request->super_benefit_list_id_2,
                        'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                        'PROCEDURE_XREF_ID' => $request->procedure_xref_id,
                        'prov_type_list_id' => $request->prov_type_list_id,
                        'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                    ]);

                $update_extensions = DB::table('plan_table_extensions')
                    ->where('PLAN_ID', $request->plan_id)
                    ->update([
                        'EFFECTIVE_DATE' => date('Ymd', strtotime($request->effective_date)),
                        'DATE_WRITTEN_TO_FIRST_FILL' => $request->date_written_to_first_fill,
                        'DATE_FILLED_TO_SUB_ONLINE' => $request->date_filled_to_sub_online,
                        'DATE_FILLED_TO_SUB_DMR' => $request->date_filled_to_sub_dmr,
                        'DATE_SUB_TO_FILLED_FUTURE' => $request->date_sub_to_filled_future,
                        'DAYS_FOR_REVERSALS' => $request->days_for_reversals,
                        'MISC_FLAG_3' => $request->misc_flag_3, //tax status
                        'MISC_FLAG_4' => $request->misc_flag_4, //mandatory u & c
                        'MISC_FLAG_1' => $request->misc_flag_1, //SYRINGES WITH ISSUING SAME DAY
                        'MISC_FLAG_5' => $request->misc_flag_5, //EXCLUDE SYSTEM NDC/GPI FORMULARY EDITS FOR OUT OF NETWORK CLAIM
                        'MISC_FLAG_6' => $request->misc_flag_6, //EXCLUDE PLAN NDC/GPI FORMULARY EDITS FOR OUT OF NETWORK CLAIM
                        'MISC_FLAG_7' => $request->misc_flag_7, //REJECT CLAIM FOR MISSING CARDHOLDER ID
                        'ER_LIMIT_1_MAX_DAYS_SUPPLY' => $request->er_limit_1_max_days_supply, //LIMIT1 (RX MAXIMUM DAYS SUPPLY
                        'ER_LIMIT_1_MINIMUM_USE' => $request->er_limit_1_minimum_use, //LIMIT1  MINIMUM USE PERCENTAGE) 
                        'ER_LIMIT_2_MAX_DAYS_SUPPLY' => $request->er_limit_2_max_days_supply, //LIMIT 2 - ABOVE LIMIT 1(RX MAXIMUM DAYS SUPPLY
                        'ER_LIMIT_2_MINIMUM_USE' => $request->er_limit_2_minimum_use, //LIMIT 2 - ABOVE LIMIT 1 MINIMUM USE PERCENTAGE)
                        'ER_LIMIT_X_MINIMUM_USE' => $request->er_limit_x_minimum_use, //ABOVE LIMIT2(MAXIMUM USE MAXIMUM
                        'ER_SEARCH_IND' => $request->er_search_ind, //ABOVE LIMIT2 SEARCH INDICATION)
                        'MO_ER_LIMIT_1_MAX_DAYS_SUPPLY' => $request->mo_er_limit_1_max_days_supply, //LIMIT1 (RX MAXIMUM DAYS SUPPLY
                        'MO_ER_LIMIT_1_MINIMUM_USE' => $request->mo_er_limit_1_minimum_use, //LIMIT1  MINIMUM USE PERCENTAGE) 
                        'MO_ER_LIMIT_2_MAX_DAYS_SUPPLY' => $request->mo_er_limit_2_max_days_supply, //LIMIT 2 - ABOVE LIMIT 1(RX MAXIMUM DAYS SUPPLY
                        'MO_ER_LIMIT_X_MINIMUM_USE' => $request->mo_er_limit_x_minimum_use,
                        // 'mo_er_limit_x_max_days_supply' => $request->mo_er_limit_x_max_days_supply
                        'plan_notes' => $request->plan_notes,
                        // 'prov_type_list_id' => $request->prov_type_list_id,
                        'mo_er_limit_2_minimum_use' => $request->mo_er_limit_2_minimum_use,
                        'ER_BYPASS_HIST_DAYS_SUPPLY' => $request->er_bypass_hist_days_supply,
                        'MO_ER_BYPASS_HIST_DAYS_SUPPLY' => $request->mo_er_bypass_hist_days_supply,
                        'mo_er_search_ind' => $request->mo_er_search_ind,
                    ]);
                $updateData = DB::table('PLAN_BENEFIT_TABLE')
                    ->join('PLAN_TABLE_EXTENSIONS', 'PLAN_BENEFIT_TABLE.plan_id', '=', 'PLAN_TABLE_EXTENSIONS.plan_id')
                    ->where('PLAN_BENEFIT_TABLE.plan_id', $request->plan_id)
                    ->first();


                $plan_benefit_table = DB::table('PLAN_BENEFIT_TABLE')
                    ->where(DB::raw('UPPER(plan_id)'), strtoupper('plan_id'))
                    ->first();

                $plan_table_ext = DB::table('PLAN_TABLE_EXTENSIONS')
                    ->where(DB::raw('UPPER(plan_id)'), strtoupper('plan_id'))
                    ->first();
                $record_snapshot_benefit = json_encode($updateData);
                $save_audit = $this->auditMethod('UP', $record_snapshot_benefit, 'PLAN_BENEFIT_TABLE');

                $record_snapshot_ext = json_encode($plan_table_ext);
                $save_audit_ext = $this->auditMethod('UP', $record_snapshot_ext, 'PLAN_TABLE_EXTENSIONS');

                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData);
                // }
            }
        
    }


    public function get(Request $request)
    {
        $planEdit = DB::table('PLAN_BENEFIT_TABLE')
        // ->select('PLAN_BENEFIT_TABLE.*',
        // 'PLAN_TABLE_EXTENSIONS.EFFECTIVE_DATE',
        // 'PLAN_TABLE_EXTENSIONS.PLAN_NOTES',
        // 'PLAN_TABLE_EXTENSIONS.DATE_WRITTEN_TO_FIRST_FILL',
        // 'PLAN_TABLE_EXTENSIONS.DATE_FILLED_TO_SUB_ONLINE',
        // 'PLAN_TABLE_EXTENSIONS.DATE_FILLED_TO_SUB_DMR',
        // 'PLAN_TABLE_EXTENSIONS.DATE_SUB_TO_FILLED_FUTURE',
        // 'PLAN_TABLE_EXTENSIONS.DAYS_FOR_REVERSALS',
        // 'PLAN_TABLE_EXTENSIONS.ER_LIMIT_1_MAX_DAYS_SUPPLY',
        // 'PLAN_TABLE_EXTENSIONS.ER_LIMIT_1_MINIMUM_USE',
        // 'PLAN_TABLE_EXTENSIONS.ER_LIMIT_2_MAX_DAYS_SUPPLY',
        // 'PLAN_TABLE_EXTENSIONS.ER_LIMIT_2_MINIMUM_USE',
        // 'PLAN_TABLE_EXTENSIONS.ER_LIMIT_X_MINIMUM_USE',
        // 'PLAN_TABLE_EXTENSIONS.MO_ER_LIMIT_1_MAX_DAYS_SUPPLY',
        // 'PLAN_TABLE_EXTENSIONS.MO_ER_LIMIT_1_MINIMUM_USE',
        // 'PLAN_TABLE_EXTENSIONS.MO_ER_LIMIT_2_MAX_DAYS_SUPPLY',
        // 'PLAN_TABLE_EXTENSIONS.MO_ER_LIMIT_2_MINIMUM_USE',
        // 'PLAN_TABLE_EXTENSIONS.MO_ER_LIMIT_X_MINIMUM_USE',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_1',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_2',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_3',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_4',

        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_5',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_6',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_7',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_8',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_9',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_10',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_11',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_12',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_13',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_14',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_15',
        // 'PLAN_TABLE_EXTENSIONS.MISC_FLAG_16',
        // 'PLAN_TABLE_EXTENSIONS.ER_SEARCH_IND',
        // 'PLAN_TABLE_EXTENSIONS.ER_BYPASS_HIST_DAYS_SUPPLY',
        // 'PLAN_TABLE_EXTENSIONS.MO_ER_SEARCH_IND',
        // 'PLAN_TABLE_EXTENSIONS.MO_ER_BYPASS_HIST_DAYS_SUPPLY',


        
        // )
            ->join('plan_table_extensions', 'plan_table_extensions.plan_id', '=', 'PLAN_BENEFIT_TABLE.plan_id')
            ->whereRaw('LOWER(PLAN_BENEFIT_TABLE.PLAN_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            // ->where('PLAN_BENEFIT_TABLE.PLAN_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PLAN_BENEFIT_TABLE.PLAN_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->paginate(100);

        $system_limits = DB::table('GLOBAL_PARAMS')->select(['sys_date_written_to_first_fill', 'sys_date_filled_to_sub_online', 'sys_date_filled_to_sub_dmr', 'sys_date_sub_to_filled_future', 'sys_days_for_reversals'])->first();

        foreach ($planEdit as $key => $plan) {
            // $plan->system_limits = $system_limits;

            $plan->sys_date_written_to_first_fill = $system_limits->sys_date_written_to_first_fill;
            $plan->sys_date_filled_to_sub_online = $system_limits->sys_date_filled_to_sub_online;
            $plan->sys_date_filled_to_sub_dmr = $system_limits->sys_date_filled_to_sub_dmr;
            $plan->sys_date_sub_to_filled_future = $system_limits->sys_date_sub_to_filled_future;
            $plan->sys_days_for_reversals = $system_limits->sys_days_for_reversals;
        }



        return $this->respondWithToken($this->token(), '', $planEdit);
    }

    public function getPlanEditData($planid)
    {
        $plandata = DB::table('PLAN_BENEFIT_TABLE')
            // ->leftJoin('PLAN_VALIDATION_LISTS', 'plan_benefit_table.plan_id', '=', 'plan_benefit_table.plan_id')
            // ->leftJoin('PLAN_RX_NETWORK_RULES', 'plan_benefit_table.plan_id', '=', 'PLAN_RX_NETWORK_RULES.plan_id')
            // ->leftJoin('PLAN_RX_NETWORKS', 'plan_benefit_table.plan_id', '=', 'PLAN_RX_NETWORKS.plan_id')
            ->where('plan_benefit_table.plan_id', $planid)
            ->first();
        return $this->respondWithToken($this->token(), '', $plandata);
    }

    public function getPlanClassification(Request $request)
    {
        $plan_classification = [
            ['pclass_value' => '', 'pclass_label' => 'Select'],
            ['pclass_value' => 'C', 'pclass_label' => 'Cash'],
            ['pclass_value' => 'M', 'pclass_label' => 'Medicaid'],
            ['pclass_value' => 'T', 'pclass_label' => 'Third Party'],
            ['pclass_value' => 'U', 'pclass_label' => 'Unclassified'],
            ['pclass_value' => 'W', 'pclass_label' => 'Workers Compensation'],
        ];

        return $this->respondWithToken($this->token(), '', $plan_classification);
    }

    public function getExpFlag(Request $request)
    {
        $exp_flag = [
            ['exp_flag_value' => '', 'exp_flag_label' => 'Select'],
            ['exp_flag_value' => 'N', 'exp_flag_label' => 'None (No Eligibility check)'],
            ['exp_flag_value' => 'V', 'exp_flag_label' => 'Validate Patient by PIN'],
            ['exp_flag_value' => 'M', 'exp_flag_label' => 'Check Eligibility By Member'],
            ['exp_flag_value' => 'X', 'exp_flag_label' => 'Check Eligibility By Member Date of Birth & Gender'],
            ['exp_flag_value' => 'Y', 'exp_flag_label' => 'Check Eligibility By Member Date of Birth'],
            ['exp_flag_value' => 'Z', 'exp_flag_label' => 'Check Eligibility By Member Gender'],
            ['exp_flag_value' => '1', 'exp_flag_label' => 'Check Eligibility By Member Birth Year'],
            ['exp_flag_value' => '2', 'exp_flag_label' => 'Check Eligibility By Member Birth Month and Year'],
        ];

        return $this->respondWithToken($this->token(), '', $exp_flag);
    }

    public function getPharmExpFlag(Request $request)
    {
        $pharm_exp_flags = [
            ['pharm_exp_flag_value' => '', 'pharm_exp_flag_label' => 'Select'],
            ['pharm_exp_flag_value' => 'N', 'pharm_exp_flag_label' => 'None'],
            ['pharm_exp_flag_value' => 'M', 'pharm_exp_flag_label' => 'Must Exist Within Provider Master'],
            ['pharm_exp_flag_value' => 'P', 'pharm_exp_flag_label' => 'Must Exist Within Provider Network'],
            ['pharm_exp_flag_value' => 'V', 'pharm_exp_flag_label' => 'Validate Provider In/Out of Networkt'],
            ['pharm_exp_flag_value' => 'F', 'pharm_exp_flag_label' => 'Validate Provider Format'],
        ];

        return $this->respondWithToken($this->token(), '', $pharm_exp_flags);
    }

    public function getPriscExpFlag(Request $request)
    {
        $prisc_exp_flags = [
            ['prisc_exp_flag_value' => '', 'prisc_exp_flag_label' => 'Not Specified'],
            ['prisc_exp_flag_value' => 'N', 'prisc_exp_flag_label' => 'No Prescriber check'],
            ['prisc_exp_flag_value' => 'D', 'prisc_exp_flag_label' => 'Validate DEA Code'],
            ['prisc_exp_flag_value' => 'P', 'prisc_exp_flag_label' => 'Primary Phisician Validation'],
            ['prisc_exp_flag_value' => 'E', 'prisc_exp_flag_label' => 'Must Exist in Physician Master'],
        ];
        return $this->respondWithToken($this->token(), '', $prisc_exp_flags);
    }

    public function getExhausted(Request $request)
    {
        $exhausted = [
            ['exhausted_value' => '', 'exhausted_label' => 'Select'],
            ['exhausted_value' => 'R', 'exhausted_label' => 'Reject the transaction'],
            ['exhausted_value' => 'N', 'exhausted_label' => 'New plan is specified'],
        ];

        return $this->respondWithToken($this->token(), '', $exhausted);
    }

    public function getTax(Request $request)
    {
        $taxs = [
            ['tax_value' => '', 'tax_label' => 'Not Specified'],
            ['tax_value' => '0', 'tax_label' => 'Taxable'],
            ['tax_value' => '1', 'tax_label' => 'Tax excempt'],
        ];

        return $this->respondWithToken($this->token(), '', $taxs);
    }

    public function getUCPlan(Request $request)
    {
        $uc_plans = [
            ['uc_plan_value' => '', 'uc_plan_label' => 'Not Specified'],
            ['uc_plan_value' => '1', 'uc_plan_label' => 'No'],
            ['uc_plan_value' => '2', 'uc_plan_label' => 'Yes'],
        ];

        return $this->respondWithToken($this->token(), '', $uc_plans);
    }

    public function getSearchIndication(Request $request)
    {
        $search_indiations = [
            ['sarch_indication_value' => '', 'search_indication_label' => 'select'],
            ['sarch_indication_value' => 'N', 'search_indication_label' => 'Name Portion of GPI'],
            ['sarch_indication_value' => 'F', 'search_indication_label' => 'Full GPI'],
        ];

        return $this->respondWithToken($this->token(), '', $search_indiations);
    }

    public function getFormulary(Request $request)
    {
        $formulary = [
            ['formulary_value' => '', 'formulary_label' => 'Select'],
            ['formulary_value' => 'FA', 'formulary_label' => 'Approved, Formularly'],
            ['formulary_value' => 'NF', 'formulary_label' => 'Approved, Non Formularly'],
            ['formulary_value' => 'CF', 'formulary_label' => 'Rejected'],
            ['formulary_value' => 'NR', 'formulary_label' => 'Rejected-No Rx Coverage'],
        ];

        return $this->respondWithToken($this->token(), '', $formulary);
    }

    public function getSuperProviderNetwork(Request  $request)
    {
        $super_provider_network = DB::table('SUPER_RX_NETWORKS')
            ->join('SUPER_RX_NETWORK_NAMES', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->get();
        return $this->respondWithToken($this->token(), '', $super_provider_network);
    }

    public function getSuperProviderNetwork_New(Request  $request)
    {
        $super_provider_network = DB::table('SUPER_RX_NETWORKS')
            ->join('SUPER_RX_NETWORK_NAMES', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->paginate(100);
        return $this->respondWithToken($this->token(), '', $super_provider_network);
    }

    public function getExhaustedBenefits(Request $request)
    {
        $exhausetd_benefit = [
            ['exhausted_id' => 'R', 'exhausted_name' => 'Reject The Application'],
            ['exhausted_id' => 'N', 'exhausted_name' => 'New Plan Is Specified'],
        ];

        return $this->respondWithToken($this->token(), '', $exhausetd_benefit);
    }

    public function getProcedureException(Request $request)
    {
        $procedure_list = DB::table('PROCEDURE_EXCEPTION_LISTS')
            ->join('PROCEDURE_EXCEPTION_NAMES', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST', '=', 'PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST')
            ->get();
        return $this->respondWithToken($this->token(), '', $procedure_list);
    }

    public function getProcedureException_New(Request $request)
    {
        $procedure_list = DB::table('PROCEDURE_EXCEPTION_LISTS')
            ->join('PROCEDURE_EXCEPTION_NAMES', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST', '=', 'PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST')
            ->paginate(100);
        return $this->respondWithToken($this->token(), '', $procedure_list);
    }



    public function planeditDelete(Request $request){
        if(isset($request->plan_id)) {
            $plan_benefit_delete = DB::table('PLAN_BENEFIT_TABLE')->where('PLAN_ID', $request->plan_id)->delete();
            $update_extensions = DB::table('plan_table_extensions') ->where('PLAN_ID', $request->plan_id)->delete();    

            if ($plan_benefit_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}