<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GPIExceptionController extends Controller
{

   

    public function add(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('GPI_EXCEPTIONS')
        ->where('gpi_exception_list',$request->gpi_exception_list)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'gpi_exception_list' => ['required', 'max:10', Rule::unique('GPI_EXCEPTION_LISTS')->where(function ($q) {
                    $q->whereNotNull('gpi_exception_list');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('GPI_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'gpi_exception_list' => ['required', 'max:10', Rule::unique('GPI_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('gpi_exception_list');
                // })],
                "exception_name" => ['required'],
                "effective_date" => ['required'],
                "termination_date" => ['required', 'max:10'],
                "generic_product_id" => ['required', 'max:14'],
                "new_drug_status" => ['max:1'],
                "process_rule"=>['max:1'],
                'preferred_product_ndc'=>['max:15','min:5'],
                'conversion_product_ndc'=>['max:10'],
                'message_stop_date'=>['max:10'],
                'min_rx_qty'=>['max:10'],
                'max_rx_qty'=>['max:10'],
                'max_rxs_patient'=>['max:11'],
                'mail_order_min_rx_days'=>['max:11'],
                'mail_ord_max_days_supply_opt'=>['max:10'],
                'min_price'=>['max:10'],
                'max_price'=>['max:40'],
                'max_price_patient'=>['max:10'],
                'mail_order_max_refills'=>['max:6'],
                'min_rx_days'=>['max:6'],
                'max_rx_days'=>['max:6'],
                'max_refills'=>['max:6'],
                'min_ctl_days'=>['max:6'],
                'max_ctl_days'=>['max:6'],
                'max_rx_qty_opt'=>['max:6'],
                'valid_relation_code'=>['max:6'],
                'sex_restriction'=>['max:6'],
                'max_days_per_fill'=>['max:6'],
                'max_dose'=>['max:6'],
                'starter_dose_days'=>['min:2','max:12'],
                'drug_cov_start_days'=>['min:2','max:12'],
                'starter_dose_bypass_days'=>['max:6'],
                'alternate_price_schedule'=>['max:12','min:2'],
                'acute_dosing_days'=>['max:12','min:2'],
                'starter_dose_maint_bypass_days'=>['max:12|min:2'],
                'alternate_copay_sched'=>['max:6'],
                'min_age'=>['max:6'],
                'max_age'=>['max:2'],
                'maint_dose_units_day'=>['max:1'],
                'brand_copay_amt'=>['max:1'],
                'merge_defaults'=>['max:1'],
                'max_qty_over_time'=>['numeric|max:6'],
                'generic_copay_amt'=>['numeric|max:6'],
                'max_days_over_time'=>['numeric|max:6'],
                'maximum_allowable_cost'=>['numeric|max:6'],
                'pharmacy_list'=>['max:1'],
                'physician_specialty_list'=>['numeric|max:6'],
                'physician_list'=>['numeric|max:6'],
                'diagnosis_list'=>['max:10'],
                'denial_override'=>['max:10'],
                'bng_sngl_inc_exc_ind'=>['max:1'],
                'bng_multi_inc_exc_ind'=>['max:1'],
                'bga_inc_exc_ind'=>['max:1'],
                'gen_inc_exc_ind'=>['max:1'],



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'GPI Exception Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('GPI_EXCEPTIONS')->insert(
                    [
                        'gpi_exception_list' => $request->gpi_exception_list,
                        'exception_name'=>$request->exception_name,
                        
                    ]
                );
    
                $add = DB::table('GPI_EXCEPTION_LISTS')
                    ->insert([


                        'GPI_EXCEPTION_LIST'=>$request->gpi_exception_list,
                        'GENERIC_PRODUCT_ID'=>$request->generic_product_id,
                        'NEW_DRUG_STATUS'=>$request->new_drug_status,
                        'PROCESS_RULE'=>$request->process_rule,
                        'MAXIMUM_ALLOWABLE_COST'=>$request->maximum_allowable_cost,
                        'PHYSICIAN_LIST'=>$request->physician_list,
                        'PHYSICIAN_SPECIALTY_LIST'=>$request->physician_specialty_list,
                        'PHARMACY_LIST'=>$request->pharmacy_list,
                        'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                        'PREFERRED_PRODUCT_NDC'=>$request->preferred_product_ndc,
                        'CONVERSION_PRODUCT_NDC'=>$request->conversion_product_ndc,
                        'ALTERNATE_PRICE_SCHEDULE'=>$request->alternate_price_schedule,
                        'ALTERNATE_COPAY_SCHED'=>$request->alternate_copay_sched,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                        'MIN_RX_QTY'=>$request->min_rx_qty,
                        'MAX_RX_QTY'=>$request->max_rx_qty,
                        'MIN_RX_DAYS'=>$request->min_rx_days,
                        'MAX_RX_DAYS'=>$request->max_rx_days,
                        'MIN_CTL_DAYS'=>$request->min_ctl_days,
                        'MAX_CTL_DAYS'=>$request->max_ctl_days,
                        'MAX_REFILLS'=>$request->max_refills,
                        'MAX_DAYS_PER_FILL'=>$request->max_days_per_fill,
                        'MAX_DOSE'=>$request->max_dose,
                        'MIN_AGE'=>$request->min_age,
                        'MAX_AGE'=>$request->max_age,
                        'MIN_PRICE'=>$request->min_price,
                        'MAX_PRICE'=>$request->max_price,
                        'MAX_RXS_PATIENT'=>$request->max_rxs_patient,
                        'MAX_PRICE_PATIENT'=>$request->max_price_patient,
                        'GENERIC_COPAY_AMT'=>$request->generic_copay_amt,
                        'BRAND_COPAY_AMT'=>$request->brand_copay_amt,
                        'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                        'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                        'DENIAL_OVERRIDE'=>$request->denial_override,
                        'MAINTENANCE_DRUG'=>$request->maintenance_drug,
                        'MERGE_DEFAULTS'=>$request->merge_defaults,
                        'SEX_RESTRICTION'=>$request->sex_restriction,
                        'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                        'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                        'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                        'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                        'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                        'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID'=>'',
                        'DATE_TIME_MODIFIED'=>$createddate,
                        'COPAY_NETWORK_OVRD'=>$request->copay_network_ovrd,
                        'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                        'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                        'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                        'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                        'MIN_PRICE_OPT'=>$request->min_price_opt,
                        'MAX_PRICE_OPT'=>$request->max_price_opt,
                        'VALID_RELATION_CODE'=>$request->valid_relation_code,
                        'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                        'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                        'PKG_DETERMINE_ID'=>$request->pkg_determine_id,
                        'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                        'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                        'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                        'USER_ID_CREATED'=>'',
                        'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                        'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                        'BNG_SNGL_INC_EXC_IND'=>$request->bng_sngl_inc_exc_ind,
                        'BNG_MULTI_INC_EXC_IND'=>$request->bng_multi_inc_exc_ind,
                        'BGA_INC_EXC_IND'=>$request->bga_inc_exc_ind,
                        'GEN_INC_EXC_IND'=>$request->gen_inc_exc_ind,
                        'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                        'DAYS_SUPPLY_OPT_MULTIPLIER'=>$request->days_supply_opt_multiplier,
                        'MODULE_EXIT'=>$request->module_exit,
    
                        
                           
                        
                    ]);
    
                $add = DB::table('GPI_EXCEPTION_LISTS')->where('GPI_EXCEPTION_LIST', 'like', '%' . $request->gpi_exception_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                'gpi_exception_list' => ['required', 'max:10'],
                "exception_name" => ['required'],
                "effective_date" => ['required'],
                "termination_date" => ['required', 'max:10'],
                "generic_product_id" => ['required', 'max:14'],
                "new_drug_status" => ['max:1'],
                "process_rule"=>['max:1'],
                'preferred_product_ndc'=>['max:15','min:5'],
                'conversion_product_ndc'=>['max:10'],
                'message_stop_date'=>['max:10'],
                'min_rx_qty'=>['max:10'],
                'max_rx_qty'=>['max:10'],
                'max_rxs_patient'=>['max:11'],
                'mail_order_min_rx_days'=>['max:11'],
                'mail_ord_max_days_supply_opt'=>['max:10'],
                'min_price'=>['max:10'],
                'max_price'=>['max:40'],
                'max_price_patient'=>['max:10'],
                'mail_order_max_refills'=>['max:6'],
                'min_rx_days'=>['max:6'],
                'max_rx_days'=>['max:6'],
                'max_refills'=>['max:6'],
                'min_ctl_days'=>['max:6'],
                'max_ctl_days'=>['max:6'],
                'max_rx_qty_opt'=>['max:6'],
                'valid_relation_code'=>['max:6'],
                'sex_restriction'=>['max:6'],
                'max_days_per_fill'=>['max:6'],
                'max_dose'=>['max:6'],
                'starter_dose_days'=>['min:2','max:12'],
                'drug_cov_start_days'=>['min:2','max:12'],
                'starter_dose_bypass_days'=>['max:6'],
                'alternate_price_schedule'=>['max:12','min:2'],
                'acute_dosing_days'=>['max:12','min:2'],
                'starter_dose_maint_bypass_days'=>['max:12|min:2'],
                'alternate_copay_sched'=>['max:6'],
                'min_age'=>['max:6'],
                'max_age'=>['max:2'],
                'maint_dose_units_day'=>['max:1'],
                'brand_copay_amt'=>['max:1'],
                'merge_defaults'=>['max:1'],
                'max_qty_over_time'=>['numeric|max:6'],
                'generic_copay_amt'=>['numeric|max:6'],
                'max_days_over_time'=>['numeric|max:6'],
                'maximum_allowable_cost'=>['numeric|max:6'],
                'pharmacy_list'=>['max:1'],
                'physician_specialty_list'=>['numeric|max:6'],
                'physician_list'=>['numeric|max:6'],
                'diagnosis_list'=>['max:10'],
                'denial_override'=>['max:10'],
                'bng_sngl_inc_exc_ind'=>['max:1'],
                'bng_multi_inc_exc_ind'=>['max:1'],
                'bga_inc_exc_ind'=>['max:1'],
                'gen_inc_exc_ind'=>['max:1'],



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }
    
                $update_names = DB::table('GPI_EXCEPTIONS')
                ->where('gpi_exception_list', $request->gpi_exception_list )
                ->first();
                    
    
                $checkGPI = DB::table('GPI_EXCEPTION_LISTS')
                    ->where('generic_product_id', $request->generic_product_id)
                    ->where('gpi_exception_list',$request->gpi_exception_list)
                    ->get()
                    ->count();
                    // dd($checkGPI);
                // if result >=1 then update NDC_EXCEPTION_LISTS table record
                //if result 0 then add NDC_EXCEPTION_LISTS record

    
                if ($checkGPI <= "0") {

                    $update = DB::table('GPI_EXCEPTION_LISTS')->insert([
                        'GPI_EXCEPTION_LIST'=>$request->gpi_exception_list,
                        'GENERIC_PRODUCT_ID'=>$request->generic_product_id,
                        'NEW_DRUG_STATUS'=>$request->new_drug_status,
                        'PROCESS_RULE'=>$request->process_rule,
                        'MAXIMUM_ALLOWABLE_COST'=>$request->maximum_allowable_cost,
                        'PHYSICIAN_LIST'=>$request->physician_list,
                        'PHYSICIAN_SPECIALTY_LIST'=>$request->physician_specialty_list,
                        'PHARMACY_LIST'=>$request->pharmacy_list,
                        'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                        'PREFERRED_PRODUCT_NDC'=>$request->preferred_product_ndc,
                        'CONVERSION_PRODUCT_NDC'=>$request->conversion_product_ndc,
                        'ALTERNATE_PRICE_SCHEDULE'=>$request->alternate_price_schedule,
                        'ALTERNATE_COPAY_SCHED'=>$request->alternate_copay_sched,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                        'MIN_RX_QTY'=>$request->min_rx_qty,
                        'MAX_RX_QTY'=>$request->max_rx_qty,
                        'MIN_RX_DAYS'=>$request->min_rx_days,
                        'MAX_RX_DAYS'=>$request->max_rx_days,
                        'MIN_CTL_DAYS'=>$request->min_ctl_days,
                        'MAX_CTL_DAYS'=>$request->max_ctl_days,
                        'MAX_REFILLS'=>$request->max_refills,
                        'MAX_DAYS_PER_FILL'=>$request->max_days_per_fill,
                        'MAX_DOSE'=>$request->max_dose,
                        'MIN_AGE'=>$request->min_age,
                        'MAX_AGE'=>$request->max_age,
                        'MIN_PRICE'=>$request->min_price,
                        'MAX_PRICE'=>$request->max_price,
                        'MAX_RXS_PATIENT'=>$request->max_rxs_patient,
                        'MAX_PRICE_PATIENT'=>$request->max_price_patient,
                        'GENERIC_COPAY_AMT'=>$request->generic_copay_amt,
                        'BRAND_COPAY_AMT'=>$request->brand_copay_amt,
                        'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                        'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                        'DENIAL_OVERRIDE'=>$request->denial_override,
                        'MAINTENANCE_DRUG'=>$request->maintenance_drug,
                        'MERGE_DEFAULTS'=>$request->merge_defaults,
                        'SEX_RESTRICTION'=>$request->sex_restriction,
                        'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                        'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                        'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                        'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                        'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                        'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID'=>'',
                        'DATE_TIME_MODIFIED'=>$createddate,
                        'COPAY_NETWORK_OVRD'=>$request->copay_network_ovrd,
                        'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                        'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                        'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                        'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                        'MIN_PRICE_OPT'=>$request->min_price_opt,
                        'MAX_PRICE_OPT'=>$request->max_price_opt,
                        'VALID_RELATION_CODE'=>$request->valid_relation_code,
                        'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                        'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                        'PKG_DETERMINE_ID'=>$request->pkg_determine_id,
                        'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                        'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                        'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                        'USER_ID_CREATED'=>'',
                        'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                        'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                        'BNG_SNGL_INC_EXC_IND'=>$request->bng_sngl_inc_exc_ind,
                        'BNG_MULTI_INC_EXC_IND'=>$request->bng_multi_inc_exc_ind,
                        'BGA_INC_EXC_IND'=>$request->bga_inc_exc_ind,
                        'GEN_INC_EXC_IND'=>$request->gen_inc_exc_ind,
                        'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                        'DAYS_SUPPLY_OPT_MULTIPLIER'=>$request->days_supply_opt_multiplier,
                        'MODULE_EXIT'=>$request->module_exit,
    
                        
                           
                        
                    ]);
                   

                $update = DB::table('GPI_EXCEPTION_LISTS')->where('gpi_exception_list', 'like', '%' . $request->ndc_exception_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                } else {
                    $update = DB::table('GPI_EXCEPTION_LISTS' )
                    ->where('generic_product_id',$request->generic_product_id)
                    ->where('gpi_exception_list',$request->gpi_exception_list)
                    ->where('effective_date',$request->effective_date)        
                    ->update(
                        [
                            'NEW_DRUG_STATUS'=>$request->new_drug_status,
                            'PROCESS_RULE'=>$request->process_rule,
                            'MAXIMUM_ALLOWABLE_COST'=>$request->maximum_allowable_cost,
                            'PHYSICIAN_LIST'=>$request->physician_list,
                            'PHYSICIAN_SPECIALTY_LIST'=>$request->physician_specialty_list,
                            'PHARMACY_LIST'=>$request->pharmacy_list,
                            'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                            'PREFERRED_PRODUCT_NDC'=>$request->preferred_product_ndc,
                            'CONVERSION_PRODUCT_NDC'=>$request->conversion_product_ndc,
                            'ALTERNATE_PRICE_SCHEDULE'=>$request->alternate_price_schedule,
                            'ALTERNATE_COPAY_SCHED'=>$request->alternate_copay_sched,
                            'MESSAGE'=>$request->message,
                            'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                            'MIN_RX_QTY'=>$request->min_rx_qty,
                            'MAX_RX_QTY'=>$request->max_rx_qty,
                            'MIN_RX_DAYS'=>$request->min_rx_days,
                            'MAX_RX_DAYS'=>$request->max_rx_days,
                            'MIN_CTL_DAYS'=>$request->min_ctl_days,
                            'MAX_CTL_DAYS'=>$request->max_ctl_days,
                            'MAX_REFILLS'=>$request->max_refills,
                            'MAX_DAYS_PER_FILL'=>$request->max_days_per_fill,
                            'MAX_DOSE'=>$request->max_dose,
                            'MIN_AGE'=>$request->min_age,
                            'MAX_AGE'=>$request->max_age,
                            'MIN_PRICE'=>$request->min_price,
                            'MAX_PRICE'=>$request->max_price,
                            'MAX_RXS_PATIENT'=>$request->max_rxs_patient,
                            'MAX_PRICE_PATIENT'=>$request->max_price_patient,
                            'GENERIC_COPAY_AMT'=>$request->generic_copay_amt,
                            'BRAND_COPAY_AMT'=>$request->brand_copay_amt,
                            'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                            'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                            'DENIAL_OVERRIDE'=>$request->denial_override,
                            'MAINTENANCE_DRUG'=>$request->maintenance_drug,
                            'MERGE_DEFAULTS'=>$request->merge_defaults,
                            'SEX_RESTRICTION'=>$request->sex_restriction,
                            'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                            'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                            'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                            'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                            'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                            'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                            'DATE_TIME_CREATED'=>$createddate,
                            'USER_ID'=>'',
                            'DATE_TIME_MODIFIED'=>$createddate,
                            'COPAY_NETWORK_OVRD'=>$request->copay_network_ovrd,
                            'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                            'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                            'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                            'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                            'MIN_PRICE_OPT'=>$request->min_price_opt,
                            'MAX_PRICE_OPT'=>$request->max_price_opt,
                            'VALID_RELATION_CODE'=>$request->valid_relation_code,
                            'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                            'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                            'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                            'PKG_DETERMINE_ID'=>$request->pkg_determine_id,
                            'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                            'EFFECTIVE_DATE'=>$request->effective_date,
                            'TERMINATION_DATE'=>$request->termination_date,
                            'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                            'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                            'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                            'USER_ID_CREATED'=>'',
                            'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                            'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                            'BNG_SNGL_INC_EXC_IND'=>$request->bng_sngl_inc_exc_ind,
                            'BNG_MULTI_INC_EXC_IND'=>$request->bng_multi_inc_exc_ind,
                            'BGA_INC_EXC_IND'=>$request->bga_inc_exc_ind,
                            'GEN_INC_EXC_IND'=>$request->gen_inc_exc_ind,
                            'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                            'DAYS_SUPPLY_OPT_MULTIPLIER'=>$request->days_supply_opt_multiplier,
                            'MODULE_EXIT'=>$request->module_exit,
        
                            ]
                        );
                    $update = DB::table('GPI_EXCEPTION_LISTS')->where('gpi_exception_list', 'like', '%' . $request->ndc_exception_list . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                }
    
               

            }

           
        }
    }


    public function GpiList(Request $request)
    {

        $benefitcode = DB::table('GPI_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(), 'Successfully Fetched Data', $benefitcode);
    }



    public function search(Request $request)
    {
        $ndc = DB::table('GPI_EXCEPTIONS')
            ->select('GPI_EXCEPTION_LIST', 'EXCEPTION_NAME')
            ->where('GPI_EXCEPTION_LIST', 'like', '%' . $request->search. '%')
            ->orWhere('EXCEPTION_NAME', 'like', '%' . $request->search. '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('GPI_EXCEPTION_LISTS')
        ->join('GPI_EXCEPTIONS', 'GPI_EXCEPTIONS.GPI_EXCEPTION_LIST', '=', 'GPI_EXCEPTION_LISTS.GPI_EXCEPTION_LIST')
        // ->select('gpi_exception_list', 'EXCEPTION_NAME')
                ->where('GPI_EXCEPTION_LISTS.GPI_EXCEPTION_LIST', 'like', '%' . $ndcid. '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();


        return $this->respondWithToken($this->token(), '', $ndclist);
    }


    public function getNDCItemDetails($ndcid,$ncdid2)
    {
        $ndc = DB::table('GPI_EXCEPTION_LISTS')
                    ->select('GPI_EXCEPTION_LISTS.*',
                    'DRUG_MASTER1.LABEL_NAME as preferd_ndc_description',
                    'DRUG_MASTER2.LABEL_NAME as conversion_ndc_description',
                    'DRUG_MASTER3.LABEL_NAME as gpi_exception_description',
                    'GPI_EXCEPTIONS.EXCEPTION_NAME'
                    )

                    ->leftjoin('GPI_EXCEPTIONS', 'GPI_EXCEPTIONS.GPI_EXCEPTION_LIST', '=', 'GPI_EXCEPTION_LISTS.GPI_EXCEPTION_LIST')
                    ->leftjoin('DRUG_MASTER as DRUG_MASTER1', 'DRUG_MASTER1.NDC', '=', 'GPI_EXCEPTION_LISTS.PREFERRED_PRODUCT_NDC')
                    ->leftjoin('DRUG_MASTER as DRUG_MASTER2', 'DRUG_MASTER2.NDC', '=', 'GPI_EXCEPTION_LISTS.CONVERSION_PRODUCT_NDC')
                    ->leftjoin('DRUG_MASTER as DRUG_MASTER3', 'DRUG_MASTER3.GENERIC_PRODUCT_ID', '=', 'GPI_EXCEPTION_LISTS.GENERIC_PRODUCT_ID')
                    ->where('GPI_EXCEPTION_LISTS.GPI_EXCEPTION_LIST',$ncdid2)

                    ->where('GPI_EXCEPTION_LISTS.generic_product_id',$ndcid)
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getGpiDropDown()
    {
        $data = DB::table('GPI_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(), '', $data);
    }
}
