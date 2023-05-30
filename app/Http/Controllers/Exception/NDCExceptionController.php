<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class NDCExceptionController extends Controller
{

    public function addcopy( Request $request ) {

        $createddate = date( 'y-m-d' );


        $ndc_exceptions_lists = DB::table('NDC_EXCEPTION_LISTS')
        ->where('ndc_exception_list',$request->ndc_exception_list)
        ->first();

        $ndc_exception=DB::table('NDC_EXCEPTIONS')
        ->where('ndc_exception_list',$request->ndc_exception_list)
        ->first();

        if ( $request->has('add_new') ) {

            $validator = Validator::make($request->all(), [
                'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                    $q->whereNotNull('NDC_EXCEPTION_LIST');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('ndc_exception_list');
                })],

                "exception_name" => ['max:36'],
                "NEW_DRUG_STATUS"=>['max:2'],
                "PROCESS_RULE"=>['max:1'],
                'MAXIMUM_ALLOWABLE_COST'=>['max:15','min:5'],
                'PHYSICIAN_LIST'=>['max:10'],
                'PHYSICIAN_SPECIALTY_LIST'=>['max:10'],
                'PHARMACY_LIST'=>['max:10'],
                'DIAGNOSIS_LIST'=>['max:10'],
                'PREFERRED_PRODUCT_NDC'=>['max:11'],
                'CONVERSION_PRODUCT_NDC'=>['max:11'],
                'ALTERNATE_PRICE_SCHEDULE'=>['max:10'],
                'ALTERNATE_COPAY_SCHED'=>['max:10'],
                'MESSAGE'=>['max:40'],
                'MESSAGE_STOP_DATE'=>['max:10'],
                'MIN_RX_QTY'=>['max:6'],
                'MAX_RX_QTY'=>['max:6'],
                'MIN_RX_DAYS'=>['max:6'],
                'MAX_RX_DAYS'=>['max:6'],
                'MIN_CTL_DAYS'=>['max:6'],
                'MAX_CTL_DAYS'=>['max:6'],
                'MAX_REFILLS'=>['max:6'],
                'MAX_DAYS_PER_FILL'=>['max:6'],
                'MAX_DOSE'=>['max:6'],
                'MIN_AGE'=>['max:6'],
                'MAX_AGE'=>['max:6'],
                'MIN_PRICE'=>['min:2','max:12'],
                'MAX_PRICE'=>['min:2','max:12'],
                'MAX_RXS_PATIENT'=>['max:6'],
                'MAX_PRICE_PATIENT'=>['max:12','min:2'],
                'GENERIC_COPAY_AMT'=>['max:12','min:2'],
                'BRAND_COPAY_AMT'=>['max:12|min:2'],
                'MAINT_DOSE_UNITS_DAY'=>['max:6'],
                'ACUTE_DOSING_DAYS'=>['max:6'],
                'DENIAL_OVERRIDE'=>['max:2'],
                'MAINTENANCE_DRUG'=>['max:1'],
                'GENERIC_INDICATOR'=>['max:1'],
                'MERGE_DEFAULTS'=>['max:1'],
                'SEX_RESTRICTION'=>['numeric|max:6'],
                'MAIL_ORDER_MIN_RX_DAYS'=>['numeric|max:6'],
                'MAIL_ORDER_MAX_RX_DAYS'=>['numeric|max:6'],
                'MAIL_ORDER_MAX_REFILLS'=>['numeric|max:6'],
                'MODULE_EXIT'=>['max:1'],
                'MAX_RXS_TIME_FLAG'=>['numeric|max:6'],
                'MAX_PRICE_TIME_FLAG'=>['numeric|max:6'],
                'USER_ID'=>['max:10'],
                'COPAY_NETWORK_OVRD'=>['max:10'],
                'MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                'RETAIL_MAX_FILLS_OPT'=>['max:1'],
                'MAIL_ORD_MAX_FILLS_OPT'=>['max:1'],
                'MIN_PRICE_OPT'=>['max:1'],
                'MAX_PRICE_OPT'=>['max:1'],
                'VALID_RELATION_CODE'=>['max:1'],
                'STARTER_DOSE_DAYS'=>['max:3'],
                'STARTER_DOSE_BYPASS_DAYS'=>['max:3'],
                'DRUG_COV_START_DAYS'=>['max:3'],
                'PKG_DETERMINE_ID'=>['max:10'],
                'MAX_RX_QTY_OPT'=>['max:1'],
                'TERMINATION_DATE'=>['max:10'],
                'MAX_QTY_OVER_TIME'=>['max:6'],
                'MAX_DAYS_OVER_TIME'=>['max:6'],
                'REJECT_ONLY_MSG_FLAG'=>['max:1'],
                'USER_ID_CREATED'=>['max:10'],
                'STARTER_DOSE_MAINT_BYPASS_DAYS'=>['numeric|max:3'],
                'MAX_QTY_PER_FILL'=>['numeric|min:3|max:8'],
                'BNG_SNGL_INC_EXC_IND'=>['max:1'],
                'BNG_MULTI_INC_EXC_IND'=>['max:1'],
                'BGA_INC_EXC_IND'=>['max:1'],
                'GEN_INC_EXC_IND'=>['max:1'],
                'RX_QTY_OPT_MULTIPLIER'=>['numeric|max:6'],
                'DAYS_SUPPLY_OPT_MULTIPLIER'=>['numeric|max:6'],



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                 if(!$ndc_exception && !$ndc_exceptions_lists){


                    $ndc_exception_names = DB::table('NDC_EXCEPTIONS')->insert(
                        [
                            'ndc_exception_list' => $request->ndc_exception_list,
                            'exception_name'=>$request->exception_name,
                            
                        ]
                    );

                    
                    $ndc_exceptions = DB::table('NDC_EXCEPTION_LISTS')->insert(
                        [
                            'NDC_EXCEPTION_LIST' =>$request->ndc_exception_list,
                            'NDC'=>$request->ndc,
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
                            'GENERIC_INDICATOR'=>$request->generic_indicator,
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

                    return $this->respondWithToken( $this->token(), 'Record Added Successfully',$ndc_exceptions);

                  

                }

                if($ndc_exceptions_lists &&  $ndc_exception){

                    return $this->respondWithToken($this->token(), 'Ndc  Exception ID Already Exists', $ndc_exceptions_lists);

                }else if(!$ndc_exceptions_lists ) {

                   

                    $ndc_exceptions = DB::table('NDC_EXCEPTION_LISTS')->insert(
                        [
                            'NDC_EXCEPTION_LIST' =>$request->ndc_exception_list,
                            'NDC'=>$request->ndc,
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
                            'GENERIC_INDICATOR'=>$request->generic_indicator,
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
                  
                  
                    return $this->respondWithToken( $this->token(), 'Record Added Successfully',$ndc_exceptions);

                }
                
                

               

            }

           


        } else {

            $validator = Validator::make($request->all(), [
                'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                    $q->whereNotNull('ndc_exception_list');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                    $q->whereNotNull('effective_date');
                })],

                'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('ndc_exception_list');
                })],

                "exception_name" => ['max:36'],
                "NEW_DRUG_STATUS"=>['max:2'],
                "PROCESS_RULE"=>['max:1'],
                'MAXIMUM_ALLOWABLE_COST'=>['max:15','min:5'],
                'PHYSICIAN_LIST'=>['max:10'],
                'PHYSICIAN_SPECIALTY_LIST'=>['max:10'],
                'PHARMACY_LIST'=>['max:10'],
                'DIAGNOSIS_LIST'=>['max:10'],
                'PREFERRED_PRODUCT_NDC'=>['max:11'],
                'CONVERSION_PRODUCT_NDC'=>['max:11'],
                'ALTERNATE_PRICE_SCHEDULE'=>['max:10'],
                'ALTERNATE_COPAY_SCHED'=>['max:10'],
                'MESSAGE'=>['max:40'],
                'MESSAGE_STOP_DATE'=>['max:10'],
                'MIN_RX_QTY'=>['max:6'],
                'MAX_RX_QTY'=>['max:6'],
                'MIN_RX_DAYS'=>['max:6'],
                'MAX_RX_DAYS'=>['max:6'],
                'MIN_CTL_DAYS'=>['max:6'],
                'MAX_CTL_DAYS'=>['max:6'],
                'MAX_REFILLS'=>['max:6'],
                'MAX_DAYS_PER_FILL'=>['max:6'],
                'MAX_DOSE'=>['max:6'],
                'MIN_AGE'=>['max:6'],
                'MAX_AGE'=>['max:6'],
                'MIN_PRICE'=>['min:2','max:12'],
                'MAX_PRICE'=>['min:2','max:12'],
                'MAX_RXS_PATIENT'=>['max:6'],
                'MAX_PRICE_PATIENT'=>['max:12','min:2'],
                'GENERIC_COPAY_AMT'=>['max:12','min:2'],
                'BRAND_COPAY_AMT'=>['max:12|min:2'],
                'MAINT_DOSE_UNITS_DAY'=>['max:6'],
                'ACUTE_DOSING_DAYS'=>['max:6'],
                'DENIAL_OVERRIDE'=>['max:2'],
                'MAINTENANCE_DRUG'=>['max:1'],
                'GENERIC_INDICATOR'=>['max:1'],
                'MERGE_DEFAULTS'=>['max:1'],
                'SEX_RESTRICTION'=>['numeric|max:6'],
                'MAIL_ORDER_MIN_RX_DAYS'=>['numeric|max:6'],
                'MAIL_ORDER_MAX_RX_DAYS'=>['numeric|max:6'],
                'MAIL_ORDER_MAX_REFILLS'=>['numeric|max:6'],
                'MODULE_EXIT'=>['max:1'],
                'MAX_RXS_TIME_FLAG'=>['numeric|max:6'],
                'MAX_PRICE_TIME_FLAG'=>['numeric|max:6'],
                'USER_ID'=>['max:10'],
                'COPAY_NETWORK_OVRD'=>['max:10'],
                'MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                'RETAIL_MAX_FILLS_OPT'=>['max:1'],
                'MAIL_ORD_MAX_FILLS_OPT'=>['max:1'],
                'MIN_PRICE_OPT'=>['max:1'],
                'MAX_PRICE_OPT'=>['max:1'],
                'VALID_RELATION_CODE'=>['max:1'],
                'STARTER_DOSE_DAYS'=>['max:3'],
                'STARTER_DOSE_BYPASS_DAYS'=>['max:3'],
                'DRUG_COV_START_DAYS'=>['max:3'],
                'PKG_DETERMINE_ID'=>['max:10'],
                'MAX_RX_QTY_OPT'=>['max:1'],
                'TERMINATION_DATE'=>['max:10'],
                'MAX_QTY_OVER_TIME'=>['max:6'],
                'MAX_DAYS_OVER_TIME'=>['max:6'],
                'REJECT_ONLY_MSG_FLAG'=>['max:1'],
                'USER_ID_CREATED'=>['max:10'],
                'STARTER_DOSE_MAINT_BYPASS_DAYS'=>['numeric|max:3'],
                'MAX_QTY_PER_FILL'=>['numeric|min:3|max:8'],
                'BNG_SNGL_INC_EXC_IND'=>['max:1'],
                'BNG_MULTI_INC_EXC_IND'=>['max:1'],
                'BGA_INC_EXC_IND'=>['max:1'],
                'GEN_INC_EXC_IND'=>['max:1'],
                'RX_QTY_OPT_MULTIPLIER'=>['numeric|max:6'],
                'DAYS_SUPPLY_OPT_MULTIPLIER'=>['numeric|max:6'],



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                $update = DB::table('NDC_EXCEPTION_LISTS' )
                ->where('ndc',$request->ndc)
                ->where('ndc_exception_list',$request->ndc_exception_list)
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
                        'GENERIC_INDICATOR'=>$request->generic_indicator,
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
    
    
    
                $accum_benfit_stat = DB::table('NDC_EXCEPTIONS' )
                ->where('ndc_exception_list', $request->ndc_exception_list )
                ->update(
                    [
                        'exception_name'=>$request->exception_name,
    
    
                    ]
                );
    
                return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

            }

           

        }


    }

    public function add(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('NDC_EXCEPTIONS')
        ->where('ndc_exception_list',$request->ndc_exception_list)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
              
            
                'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('ndc_exception_list');
                })],
                'ndc' => ['required', 'max:11'],
                "exception_name" => ['required','max:36'],
                "effective_date" => ['required'],
                "termination_date" => ['required','date','after:effective_date'],

                'min_rx_qty'=>['nullable'],
                'max_rx_qty'=>['nullable','gt:min_rx_qty'],

                'mail_order_min_rx_days'=>['nullable'],
                'mail_ord_max_days_supply_opt'=>['nullable','gt:mail_order_min_rx_days'],

                'days_supply_opt_multiplier' =>['nullable'],
                'max_days_supply_opt' =>['nullable','gt:days_supply_opt_multiplier'],

                'min_ctl_days'=>['nullable'],
                'max_ctl_days'=>['nullable','gt:min_ctl_days'],

                'mail_order_min_rx_days'=>['nullable'],
                'mail_ord_max_days_supply_opt'=>['nullable','gt:mail_order_min_rx_days'],

                'min_age'=>['nullable'],
                'max_age'=>['nullable','gt:min_age'],
                // "NEW_DRUG_STATUS"=>['max:2'],
                // "PROCESS_RULE"=>['max:1'],
                // 'MAXIMUM_ALLOWABLE_COST'=>['max:15','min:5'],
                // 'PHYSICIAN_LIST'=>['max:10'],
                // 'PHYSICIAN_SPECIALTY_LIST'=>['max:10'],
                // 'PHARMACY_LIST'=>['max:10'],
                // 'DIAGNOSIS_LIST'=>['max:10'],
                // 'PREFERRED_PRODUCT_NDC'=>['max:11'],
                // 'CONVERSION_PRODUCT_NDC'=>['max:11'],
                // 'ALTERNATE_PRICE_SCHEDULE'=>['max:10'],
                // 'ALTERNATE_COPAY_SCHED'=>['max:10'],
                // 'MESSAGE'=>['max:40'],
                // 'MESSAGE_STOP_DATE'=>['max:10'],
                // 'MIN_RX_QTY'=>['max:6'],
                // 'MAX_RX_QTY'=>['max:6'],
                // 'MIN_RX_DAYS'=>['max:6'],
                // 'MAX_RX_DAYS'=>['max:6'],
                // 'MIN_CTL_DAYS'=>['max:6'],
                // 'MAX_CTL_DAYS'=>['max:6'],
                // 'MAX_REFILLS'=>['max:6'],
                // 'MAX_DAYS_PER_FILL'=>['max:6'],
                // 'MAX_DOSE'=>['max:6'],
                // 'MIN_AGE'=>['max:6'],
                // 'MAX_AGE'=>['max:6'],
                // 'MIN_PRICE'=>['min:2','max:12'],
                // 'MAX_PRICE'=>['min:2','max:12'],
                // 'MAX_RXS_PATIENT'=>['max:6'],
                // 'MAX_PRICE_PATIENT'=>['max:12','min:2'],
                // 'GENERIC_COPAY_AMT'=>['max:12','min:2'],
                // 'BRAND_COPAY_AMT'=>['max:12|min:2'],
                // 'MAINT_DOSE_UNITS_DAY'=>['max:6'],
                // 'ACUTE_DOSING_DAYS'=>['max:6'],
                // 'DENIAL_OVERRIDE'=>['max:2'],
                // 'MAINTENANCE_DRUG'=>['max:1'],
                // 'GENERIC_INDICATOR'=>['max:1'],
                // 'MERGE_DEFAULTS'=>['max:1'],
                // 'SEX_RESTRICTION'=>['numeric|max:6'],
                // 'MAIL_ORDER_MIN_RX_DAYS'=>['numeric|max:6'],
                // 'MAIL_ORDER_MAX_RX_DAYS'=>['numeric|max:6'],
                // 'MAIL_ORDER_MAX_REFILLS'=>['numeric|max:6'],
                // 'MODULE_EXIT'=>['max:1'],
                // 'MAX_RXS_TIME_FLAG'=>['numeric|max:6'],
                // 'MAX_PRICE_TIME_FLAG'=>['numeric|max:6'],
                // 'USER_ID'=>['max:10'],
                // 'COPAY_NETWORK_OVRD'=>['max:10'],
                // 'MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                // 'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                // 'RETAIL_MAX_FILLS_OPT'=>['max:1'],
                // 'MAIL_ORD_MAX_FILLS_OPT'=>['max:1'],
                // 'MIN_PRICE_OPT'=>['max:1'],
                // 'MAX_PRICE_OPT'=>['max:1'],
                // 'VALID_RELATION_CODE'=>['max:1'],
                // 'STARTER_DOSE_DAYS'=>['max:3'],
                // 'STARTER_DOSE_BYPASS_DAYS'=>['max:3'],
                // 'DRUG_COV_START_DAYS'=>['max:3'],
                // 'PKG_DETERMINE_ID'=>['max:10'],
                // 'MAX_RX_QTY_OPT'=>['max:1'],
                // 'TERMINATION_DATE'=>['max:10'],
                // 'MAX_QTY_OVER_TIME'=>['max:6'],
                // 'MAX_DAYS_OVER_TIME'=>['max:6'],
                // 'REJECT_ONLY_MSG_FLAG'=>['max:1'],
                // 'USER_ID_CREATED'=>['max:10'],
                // 'STARTER_DOSE_MAINT_BYPASS_DAYS'=>['numeric|max:3'],
                // 'MAX_QTY_PER_FILL'=>['numeric|min:3|max:8'],
                // 'BNG_SNGL_INC_EXC_IND'=>['max:1'],
                // 'BNG_MULTI_INC_EXC_IND'=>['max:1'],
                // 'BGA_INC_EXC_IND'=>['max:1'],
                // 'GEN_INC_EXC_IND'=>['max:1'],
                // 'RX_QTY_OPT_MULTIPLIER'=>['numeric|max:6'],
                // 'DAYS_SUPPLY_OPT_MULTIPLIER'=>['numeric|max:6'],



            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' =>  'Max Age must be greater than Min Age',
                'max_rx_qty.gt' => 'Max Quantity must be greater than Min Quantity',
                'max_ctl_days.gt' => 'Max Ctl must be greater than Min Ctl',
                'mail_ord_max_days_supply_opt.gt' => 'Max day Mail Service must be greater than Min day Mail Service',
                'max_days_supply_opt' => 'Max day  must be greater than Min day ',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                // if ($validation->count() > 0) {
                //     return $this->respondWithToken($this->token(), 'NDC Exception Already Exists', $validation, true, 200, 1);
                // }

                $effectiveDate=$request->effective_date;
                $terminationDate=$request->termination_date;
                $overlapExists = DB::table('NDC_EXCEPTION_LISTS')
                ->where('NDC_EXCEPTION_LIST', $request->ndc_exception_list)
                ->where(function ($query) use ($effectiveDate, $terminationDate) {
                    $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                        ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                        ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                            $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                                ->where('TERMINATION_DATE', '>=', $terminationDate);
                        });
                })
                ->exists();
                if ($overlapExists) {
                    // return redirect()->back()->withErrors(['overlap' => 'Date overlap detected.']);
                    return $this->respondWithToken($this->token(), 'For same Therapy Class,dates cannot overlap', $validation, true, 200, 1);
                }


                $add_names = DB::table('NDC_EXCEPTIONS')->insert(
                    [
                        'ndc_exception_list' => $request->ndc_exception_list,
                        'exception_name'=>$request->exception_name,
                        
                    ]
                );
    
                $add = DB::table('NDC_EXCEPTION_LISTS')
                    ->insert([
    
                        
                            'NDC_EXCEPTION_LIST' =>$request->ndc_exception_list,
                            'NDC'=>$request->ndc,
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
                            'GENERIC_INDICATOR'=>$request->generic_indicator,
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
    
                $add = DB::table('NDC_EXCEPTION_LISTS')->where('NDC_EXCEPTION_LIST', 'like', '%' . $request->ndc_exception_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } elseif($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                'ndc_exception_list' => ['required', 'max:10'],
                'ndc' => ['required', 'max:11'],
                'effective_date' => ['required', 'max:10'],        
                "exception_name" => ['required','max:36'],
                "termination_date" => ['required','date','after:effective_date'],

                'min_rx_qty'=>['nullable'],
                'max_rx_qty'=>['nullable','gt:min_rx_qty'],

                'mail_order_min_rx_days'=>['nullable'],
                'mail_ord_max_days_supply_opt'=>['nullable','gt:mail_order_min_rx_days'],

                'days_supply_opt_multiplier' =>['nullable'],
                'max_days_supply_opt' =>['nullable','gt:days_supply_opt_multiplier'],

                'min_ctl_days'=>['nullable'],
                'max_ctl_days'=>['nullable','gt:min_ctl_days'],

                'mail_order_min_rx_days'=>['nullable'],
                'mail_ord_max_days_supply_opt'=>['nullable','gt:mail_order_min_rx_days'],

                'min_age'=>['nullable'],
                'max_age'=>['nullable','gt:min_age'],
                // "NEW_DRUG_STATUS"=>['max:2'],
                // "PROCESS_RULE"=>['max:1'],
                // 'MAXIMUM_ALLOWABLE_COST'=>['max:15','min:5'],
                // 'PHYSICIAN_LIST'=>['max:10'],
                // 'PHYSICIAN_SPECIALTY_LIST'=>['max:10'],
                // 'PHARMACY_LIST'=>['max:10'],
                // 'DIAGNOSIS_LIST'=>['max:10'],
                // 'PREFERRED_PRODUCT_NDC'=>['max:11'],
                // 'CONVERSION_PRODUCT_NDC'=>['max:11'],
                // 'ALTERNATE_PRICE_SCHEDULE'=>['max:10'],
                // 'ALTERNATE_COPAY_SCHED'=>['max:10'],
                // 'MESSAGE'=>['max:40'],
                // 'MESSAGE_STOP_DATE'=>['max:10'],
                // 'MIN_RX_QTY'=>['max:6'],
                // 'MAX_RX_QTY'=>['max:6'],
                // 'MIN_RX_DAYS'=>['max:6'],
                // 'MAX_RX_DAYS'=>['max:6'],
                // 'MIN_CTL_DAYS'=>['max:6'],
                // 'MAX_CTL_DAYS'=>['max:6'],
                // 'MAX_REFILLS'=>['max:6'],
                // 'MAX_DAYS_PER_FILL'=>['max:6'],
                // 'MAX_DOSE'=>['max:6'],
                // 'MIN_AGE'=>['max:6'],
                // 'MAX_AGE'=>['max:6'],
                // 'MIN_PRICE'=>['min:2','max:12'],
                // 'MAX_PRICE'=>['min:2','max:12'],
                // 'MAX_RXS_PATIENT'=>['max:6'],
                // 'MAX_PRICE_PATIENT'=>['max:12','min:2'],
                // 'GENERIC_COPAY_AMT'=>['max:12','min:2'],
                // 'BRAND_COPAY_AMT'=>['max:12|min:2'],
                // 'MAINT_DOSE_UNITS_DAY'=>['max:6'],
                // 'ACUTE_DOSING_DAYS'=>['max:6'],
                // 'DENIAL_OVERRIDE'=>['max:2'],
                // 'MAINTENANCE_DRUG'=>['max:1'],
                // 'GENERIC_INDICATOR'=>['max:1'],
                // 'MERGE_DEFAULTS'=>['max:1'],
                // 'SEX_RESTRICTION'=>['numeric|max:6'],
                // 'MAIL_ORDER_MIN_RX_DAYS'=>['numeric|max:6'],
                // 'MAIL_ORDER_MAX_RX_DAYS'=>['numeric|max:6'],
                // 'MAIL_ORDER_MAX_REFILLS'=>['numeric|max:6'],
                // 'MODULE_EXIT'=>['max:1'],
                // 'MAX_RXS_TIME_FLAG'=>['numeric|max:6'],
                // 'MAX_PRICE_TIME_FLAG'=>['numeric|max:6'],
                // 'USER_ID'=>['max:10'],
                // 'COPAY_NETWORK_OVRD'=>['max:10'],
                // 'MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                // 'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>['max:1'],
                // 'RETAIL_MAX_FILLS_OPT'=>['max:1'],
                // 'MAIL_ORD_MAX_FILLS_OPT'=>['max:1'],
                // 'MIN_PRICE_OPT'=>['max:1'],
                // 'MAX_PRICE_OPT'=>['max:1'],
                // 'VALID_RELATION_CODE'=>['max:1'],
                // 'STARTER_DOSE_DAYS'=>['max:3'],
                // 'STARTER_DOSE_BYPASS_DAYS'=>['max:3'],
                // 'DRUG_COV_START_DAYS'=>['max:3'],
                // 'PKG_DETERMINE_ID'=>['max:10'],
                // 'MAX_RX_QTY_OPT'=>['max:1'],
                // 'TERMINATION_DATE'=>['max:10'],
                // 'MAX_QTY_OVER_TIME'=>['max:6'],
                // 'MAX_DAYS_OVER_TIME'=>['max:6'],
                // 'REJECT_ONLY_MSG_FLAG'=>['max:1'],
                // 'USER_ID_CREATED'=>['max:10'],
                // 'STARTER_DOSE_MAINT_BYPASS_DAYS'=>['numeric|max:3'],
                // 'MAX_QTY_PER_FILL'=>['numeric|min:3|max:8'],
                // 'BNG_SNGL_INC_EXC_IND'=>['max:1'],
                // 'BNG_MULTI_INC_EXC_IND'=>['max:1'],
                // 'BGA_INC_EXC_IND'=>['max:1'],
                // 'GEN_INC_EXC_IND'=>['max:1'],
                // 'RX_QTY_OPT_MULTIPLIER'=>['numeric|max:6'],
                // 'DAYS_SUPPLY_OPT_MULTIPLIER'=>['numeric|max:6'],



            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' =>  'Max Age must be greater than Min Age',
                'max_rx_qty.gt' => 'Max Quantity must be greater than Min Quantity',
                'max_ctl_days.gt' => 'Max Ctl must be greater than Min Ctl',
                'mail_ord_max_days_supply_opt.gt' => 'Max day Mail Service must be greater than Min day Mail Service',
                'max_days_supply_opt' => 'Max day  must be greater than Min day ',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }

               


                if($request->update_new == 0){

                    $effectiveDate=$request->effective_date;
                    $terminationDate=$request->termination_date;
                    $overlapExists = DB::table('NDC_EXCEPTION_LISTS')
                    ->where('NDC_EXCEPTION_LIST', $request->ndc_exception_list)
                    ->where('ndc',$request->ndc)
                    ->where('effective_date','!=',$request->effective_date)
                    ->where(function ($query) use ($effectiveDate, $terminationDate) {
                        $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                            ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                            ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                                $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                                    ->where('TERMINATION_DATE', '>=', $terminationDate);
                            });
                    })
                    ->exists();
                    if ($overlapExists) {
                        return $this->respondWithToken($this->token(), [['For same NDC Class,dates cannot overlap']], '', 'false', 200, 1);
                    }

                    $add_names = DB::table('NDC_EXCEPTIONS')
                    ->where('ndc_exception_list',$request->ndc_exception_list)
                    ->update(
                        [
                            'exception_name'=>$request->exception_name,
                            
                        ]
                    );


                    $update = DB::table('NDC_EXCEPTION_LISTS' )
                        ->where('ndc',$request->ndc)
                        ->where('ndc_exception_list',$request->ndc_exception_list)
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
                                'GENERIC_INDICATOR'=>$request->generic_indicator,
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
                        $update = DB::table('NDC_EXCEPTION_LISTS')->where('ndc_exception_list', 'like', '%' . $request->ndc_exception_list . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);


                }
                elseif($request->update_new == 1){
                    $checkGPI = DB::table('NDC_EXCEPTION_LISTS')
                    ->where('ndc', $request->ndc)
                    ->where('ndc_exception_list',$request->ndc_exception_list)
                    ->where('effective_date',$request->effective_date)
                    ->get();

                    if(count($checkGPI) >= 1){
                        return $this->respondWithToken($this->token(), [["For same NDC ,dates cannot overlap"]], '', 'false');
                    }else{

                        $effectiveDate=$request->effective_date;
                        $terminationDate=$request->termination_date;
                        $overlapExists = DB::table('NDC_EXCEPTION_LISTS')
                        ->where('NDC_EXCEPTION_LIST', $request->ndc_exception_list)
                        ->where('ndc',$request->ndc)
                        // ->where('effective_date','!=',$request->effective_date)
                        ->where(function ($query) use ($effectiveDate, $terminationDate) {
                            $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                                ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                                ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                                    $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                                        ->where('TERMINATION_DATE', '>=', $terminationDate);
                                });
                        })
                        ->exists();
                        if ($overlapExists) {
                            return $this->respondWithToken($this->token(), [['For same NDC ,dates cannot overlap']], '', 'false', 200, 1);
                        }

                        $update = DB::table('NDC_EXCEPTION_LISTS')
                            ->insert([
                                'NDC_EXCEPTION_LIST' =>$request->ndc_exception_list,
                                'NDC'=>$request->ndc,
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
                                'GENERIC_INDICATOR'=>$request->generic_indicator,
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
        
                        $add_names = DB::table('NDC_EXCEPTIONS')
                        ->where('ndc_exception_list',$request->ndc_exception_list)
                        ->update(
                            [
                                'exception_name'=>$request->exception_name,
                                
                            ]);
    
                        $update = DB::table('NDC_EXCEPTION_LISTS')->where('ndc_exception_list', 'like', '%' . $request->ndc_exception_list . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }

                }

    
                // $update_names = DB::table('NDC_EXCEPTIONS')
                // ->where('ndc_exception_list', $request->ndc_exception_list )
                // ->first();
                    
    
                // $checkGPI = DB::table('NDC_EXCEPTION_LISTS')
                //     ->where('ndc', $request->ndc)
                //     ->where('ndc_exception_list',$request->ndc_exception_list)
                //     ->get()
                //     ->count();


                // $effective_date_check = DB::table('NDC_EXCEPTION_LISTS')
                //     ->where('ndc', $request->ndc)
                //     ->where('ndc_exception_list',$request->ndc_exception_list)
                //     ->where('effective_date',$request->effective_date)
                //     ->get()
                //     ->count();
                //     // dd($effective_date);
                // // if result >=1 then update NDC_EXCEPTION_LISTS table record
                // //if result 0 then add NDC_EXCEPTION_LISTS record


                // if($effective_date_check == 1){

                //     $add_names = DB::table('NDC_EXCEPTIONS')
                //     ->where('ndc_exception_list',$request->ndc_exception_list)
                //     ->update(
                //         [
                //             'exception_name'=>$request->exception_name,
                            
                //         ]
                //     );


                //     $update = DB::table('NDC_EXCEPTION_LISTS' )
                //         ->where('ndc',$request->ndc)
                //         ->where('ndc_exception_list',$request->ndc_exception_list)
                //         ->where('effective_date',$request->effective_date)        
                //         ->update(
                //             [
                //                 'NEW_DRUG_STATUS'=>$request->new_drug_status,
                //                 'PROCESS_RULE'=>$request->process_rule,
                //                 'MAXIMUM_ALLOWABLE_COST'=>$request->maximum_allowable_cost,
                //                 'PHYSICIAN_LIST'=>$request->physician_list,
                //                 'PHYSICIAN_SPECIALTY_LIST'=>$request->physician_specialty_list,
                //                 'PHARMACY_LIST'=>$request->pharmacy_list,
                //                 'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                //                 'PREFERRED_PRODUCT_NDC'=>$request->preferred_product_ndc,
                //                 'CONVERSION_PRODUCT_NDC'=>$request->conversion_product_ndc,
                //                 'ALTERNATE_PRICE_SCHEDULE'=>$request->alternate_price_schedule,
                //                 'ALTERNATE_COPAY_SCHED'=>$request->alternate_copay_sched,
                //                 'MESSAGE'=>$request->message,
                //                 'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                //                 'MIN_RX_QTY'=>$request->min_rx_qty,
                //                 'MAX_RX_QTY'=>$request->max_rx_qty,
                //                 'MIN_RX_DAYS'=>$request->min_rx_days,
                //                 'MAX_RX_DAYS'=>$request->max_rx_days,
                //                 'MIN_CTL_DAYS'=>$request->min_ctl_days,
                //                 'MAX_CTL_DAYS'=>$request->max_ctl_days,
                //                 'MAX_REFILLS'=>$request->max_refills,
                //                 'MAX_DAYS_PER_FILL'=>$request->max_days_per_fill,
                //                 'MAX_DOSE'=>$request->max_dose,
                //                 'MIN_AGE'=>$request->min_age,
                //                 'MAX_AGE'=>$request->max_age,
                //                 'MIN_PRICE'=>$request->min_price,
                //                 'MAX_PRICE'=>$request->max_price,
                //                 'MAX_RXS_PATIENT'=>$request->max_rxs_patient,
                //                 'MAX_PRICE_PATIENT'=>$request->max_price_patient,
                //                 'GENERIC_COPAY_AMT'=>$request->generic_copay_amt,
                //                 'BRAND_COPAY_AMT'=>$request->brand_copay_amt,
                //                 'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                //                 'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                //                 'DENIAL_OVERRIDE'=>$request->denial_override,
                //                 'MAINTENANCE_DRUG'=>$request->maintenance_drug,
                //                 'GENERIC_INDICATOR'=>$request->generic_indicator,
                //                 'MERGE_DEFAULTS'=>$request->merge_defaults,
                //                 'SEX_RESTRICTION'=>$request->sex_restriction,
                //                 'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                //                 'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                //                 'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                //                 'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                //                 'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                //                 'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                //                 'DATE_TIME_CREATED'=>$createddate,
                //                 'USER_ID'=>'',
                //                 'DATE_TIME_MODIFIED'=>$createddate,
                //                 'COPAY_NETWORK_OVRD'=>$request->copay_network_ovrd,
                //                 'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                //                 'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                //                 'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                //                 'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                //                 'MIN_PRICE_OPT'=>$request->min_price_opt,
                //                 'MAX_PRICE_OPT'=>$request->max_price_opt,
                //                 'VALID_RELATION_CODE'=>$request->valid_relation_code,
                //                 'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                //                 'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                //                 'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                //                 'PKG_DETERMINE_ID'=>$request->pkg_determine_id,
                //                 'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                //                 'EFFECTIVE_DATE'=>$request->effective_date,
                //                 'TERMINATION_DATE'=>$request->termination_date,
                //                 'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                //                 'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                //                 'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                //                 'USER_ID_CREATED'=>'',
                //                 'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                //                 'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                //                 'BNG_SNGL_INC_EXC_IND'=>$request->bng_sngl_inc_exc_ind,
                //                 'BNG_MULTI_INC_EXC_IND'=>$request->bng_multi_inc_exc_ind,
                //                 'BGA_INC_EXC_IND'=>$request->bga_inc_exc_ind,
                //                 'GEN_INC_EXC_IND'=>$request->gen_inc_exc_ind,
                //                 'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                //                 'DAYS_SUPPLY_OPT_MULTIPLIER'=>$request->days_supply_opt_multiplier,
                //                 'MODULE_EXIT'=>$request->module_exit,
            
                //             ]
                //         );
                //         $update = DB::table('NDC_EXCEPTION_LISTS')->where('ndc_exception_list', 'like', '%' . $request->ndc_exception_list . '%')->first();
                //         return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);

                   


                // }else if($checkGPI == 1)
                // {

                //     return $this->respondWithToken($this->token(), 'Record already  exists',$checkGPI);


                // }
                // else{
                //     if ($checkGPI <= "0") {
                //         $update = DB::table('NDC_EXCEPTION_LISTS')
                //         ->insert([
                //             'NDC_EXCEPTION_LIST' =>$request->ndc_exception_list,
                //             'NDC'=>$request->ndc,
                //             'NEW_DRUG_STATUS'=>$request->new_drug_status,
                //             'PROCESS_RULE'=>$request->process_rule,
                //             'MAXIMUM_ALLOWABLE_COST'=>$request->maximum_allowable_cost,
                //             'PHYSICIAN_LIST'=>$request->physician_list,
                //             'PHYSICIAN_SPECIALTY_LIST'=>$request->physician_specialty_list,
                //             'PHARMACY_LIST'=>$request->pharmacy_list,
                //             'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                //             'PREFERRED_PRODUCT_NDC'=>$request->preferred_product_ndc,
                //             'CONVERSION_PRODUCT_NDC'=>$request->conversion_product_ndc,
                //             'ALTERNATE_PRICE_SCHEDULE'=>$request->alternate_price_schedule,
                //             'ALTERNATE_COPAY_SCHED'=>$request->alternate_copay_sched,
                //             'MESSAGE'=>$request->message,
                //             'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                //             'MIN_RX_QTY'=>$request->min_rx_qty,
                //             'MAX_RX_QTY'=>$request->max_rx_qty,
                //             'MIN_RX_DAYS'=>$request->min_rx_days,
                //             'MAX_RX_DAYS'=>$request->max_rx_days,
                //             'MIN_CTL_DAYS'=>$request->min_ctl_days,
                //             'MAX_CTL_DAYS'=>$request->max_ctl_days,
                //             'MAX_REFILLS'=>$request->max_refills,
                //             'MAX_DAYS_PER_FILL'=>$request->max_days_per_fill,
                //             'MAX_DOSE'=>$request->max_dose,
                //             'MIN_AGE'=>$request->min_age,
                //             'MAX_AGE'=>$request->max_age,
                //             'MIN_PRICE'=>$request->min_price,
                //             'MAX_PRICE'=>$request->max_price,
                //             'MAX_RXS_PATIENT'=>$request->max_rxs_patient,
                //             'MAX_PRICE_PATIENT'=>$request->max_price_patient,
                //             'GENERIC_COPAY_AMT'=>$request->generic_copay_amt,
                //             'BRAND_COPAY_AMT'=>$request->brand_copay_amt,
                //             'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                //             'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                //             'DENIAL_OVERRIDE'=>$request->denial_override,
                //             'MAINTENANCE_DRUG'=>$request->maintenance_drug,
                //             'GENERIC_INDICATOR'=>$request->generic_indicator,
                //             'MERGE_DEFAULTS'=>$request->merge_defaults,
                //             'SEX_RESTRICTION'=>$request->sex_restriction,
                //             'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                //             'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                //             'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                //             'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                //             'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                //             'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                //             'DATE_TIME_CREATED'=>$createddate,
                //             'USER_ID'=>'',
                //             'DATE_TIME_MODIFIED'=>$createddate,
                //             'COPAY_NETWORK_OVRD'=>$request->copay_network_ovrd,
                //             'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                //             'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                //             'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                //             'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                //             'MIN_PRICE_OPT'=>$request->min_price_opt,
                //             'MAX_PRICE_OPT'=>$request->max_price_opt,
                //             'VALID_RELATION_CODE'=>$request->valid_relation_code,
                //             'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                //             'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                //             'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                //             'PKG_DETERMINE_ID'=>$request->pkg_determine_id,
                //             'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                //             'EFFECTIVE_DATE'=>$request->effective_date,
                //             'TERMINATION_DATE'=>$request->termination_date,
                //             'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                //             'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                //             'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                //             'USER_ID_CREATED'=>'',
                //             'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                //             'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                //             'BNG_SNGL_INC_EXC_IND'=>$request->bng_sngl_inc_exc_ind,
                //             'BNG_MULTI_INC_EXC_IND'=>$request->bng_multi_inc_exc_ind,
                //             'BGA_INC_EXC_IND'=>$request->bga_inc_exc_ind,
                //             'GEN_INC_EXC_IND'=>$request->gen_inc_exc_ind,
                //             'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                //             'DAYS_SUPPLY_OPT_MULTIPLIER'=>$request->days_supply_opt_multiplier,
                //             'MODULE_EXIT'=>$request->module_exit,
                        
                        
                //     ]);
    
                //     $add_names = DB::table('NDC_EXCEPTIONS')
                //     ->where('ndc_exception_list',$request->ndc_exception_list)
                //     ->update(
                //         [
                //             'exception_name'=>$request->exception_name,
                            
                //         ]
                //     );
    
                //     $update = DB::table('NDC_EXCEPTION_LISTS')->where('ndc_exception_list', 'like', '%' . $request->ndc_exception_list . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
    
                //     } 

                // }
               

            }

           
        }
    }

    public function getAllNDCS(){

        $ndc = DB::table('DRUG_MASTER')
        ->select('NDC','LABEL_NAME')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function ndcdelete(Request $request){
        
        // return $request->all();

        if(isset($request->ndc_exception_list) && isset($request->ndc) && isset($request->effective_date)){

            $all_exceptions_lists=  DB::table('NDC_EXCEPTION_LISTS')
                                        ->where('ndc_exception_list',$request->ndc_exception_list)
                                        ->where('ndc',$request->ndc)
                                        ->where('effective_date',$request->effective_date)
                                        ->delete();
            if($all_exceptions_lists){
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            }else{
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }

        }

        elseif(isset($request->ndc_exception_list)){

        
            $exception_delete=  DB::table('NDC_EXCEPTIONS')
                                    ->where('ndc_exception_list',$request->ndc_exception_list)
                                    ->delete();

            $all_exceptions_lists=  DB::table('NDC_EXCEPTION_LISTS')
                                        ->where('ndc_exception_list',$request->ndc_exception_list)
                                        ->delete();

            if($exception_delete){
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            }else{
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }

    


        }
    }


    public function ndcList(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }




    public function search(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else{

            $ndc = DB::table('NDC_EXCEPTIONS')
            ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
            // ->where('NDC_EXCEPTION_LIST', 'like', '%' .$request->search. '%')
            ->whereRaw('LOWER(NDC_EXCEPTION_LIST) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orWhere('EXCEPTION_NAME', 'like', '%' . $request->search. '%')
            ->get();

            if($ndc->count()<1){
                return $this->respondWithToken($this->token(), 'No Data Found', $ndc);

            }else{
                return $this->respondWithToken($this->token(), 'Data Fetched Successfully',$ndc);


            }


        }


       
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('NDC_EXCEPTION_LISTS')
        ->join('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
        ->where('NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST', 'like', '%' .$ndcid. '%')
        ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }
    public function getNDC($ndcid, $name)
    {
        $ndclist = DB::table('NDC_EXCEPTION_LISTS')
                ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST', 'like', '%' .$ndcid. '%')
                ->Where('NDC_EXCEPTIONS.EXCEPTION_NAME', 'like', '%' .$name. '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails(Request $request)

    {


    
            $ifset = DB::table('NDC_EXCEPTION_LISTS')
        
                    ->select('NDC_EXCEPTION_LISTS.*', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST as exception_list',
                    'NDC_EXCEPTIONS.EXCEPTION_NAME',
                    'DRUG_MASTER.LABEL_NAME as ndc_exception_description',
                    'DRUG_MASTER1.LABEL_NAME as preferd_ndc_description',
                    'DRUG_MASTER2.LABEL_NAME as conversion_ndc_description',
                    )
                    ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
                    ->leftjoin('DRUG_MASTER as DRUG_MASTER1', 'DRUG_MASTER1.NDC', '=', 'NDC_EXCEPTION_LISTS.PREFERRED_PRODUCT_NDC')
                    ->leftjoin('DRUG_MASTER as DRUG_MASTER2', 'DRUG_MASTER2.NDC', '=', 'NDC_EXCEPTION_LISTS.CONVERSION_PRODUCT_NDC')
                    ->leftjoin('DRUG_MASTER', 'DRUG_MASTER.NDC', '=', 'NDC_EXCEPTION_LISTS.NDC')
                    ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')


                    ->where('NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST',$request->ndc_exception_list)
                    ->where('NDC_EXCEPTION_LISTS.NDC',$request->ndc)
                    ->where('NDC_EXCEPTION_LISTS.EFFECTIVE_DATE',$request->effective_date)
                    ->first();

             return $this->respondWithToken($this->token(), '', $ifset);


        

       


       
    }

    public function getNdcDropDown(){
        $data = DB::table('NDC_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(),'',$data);
    }
}
