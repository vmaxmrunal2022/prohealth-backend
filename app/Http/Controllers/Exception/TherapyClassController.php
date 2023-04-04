<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TherapyClassController extends Controller
{


    public function TherapyClassList(Request $request){

        $ndc = DB::table('TC_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );

        $recordcheck = DB::table('TC_EXCEPTIONS')
        ->where('ther_class_exception_list', strtoupper($request->ther_class_exception_list))
        ->first();


        if ( $request->has( 'new' ) ) {


            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Therapy class   Exception ID already exists in the system..!!!', $recordcheck);


            }

            else{

                $accum_benfit_stat_names = DB::table('TC_EXCEPTIONS')->insert(
                    [
                        'ther_class_exception_list' => strtoupper($request->ther_class_exception_list ),
                        'exception_name'=>$request->exception_name,
                        
    
                    ]
                );
    
                $insert = DB::table('TC_EXCEPTION_LISTS')->insert(
                    [
                        'THER_CLASS_EXCEPTION_LIST' => $request->ther_class_exception_list,
                        'THERAPY_CLASS'=>$request->therapy_class,
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
                        'MODULE_EXIT'=>$request->module_exit,
                        
    
    
            
                     
                    ]
                );
                return $this->respondWithToken( $this->token(), 'Record Added Successfully',$insert);
    
    

            }

            
           
        } else {


           

            $update = DB::table('TC_EXCEPTION_LISTS' )
            ->where('THER_CLASS_EXCEPTION_LIST',strtoupper($request->ther_class_exception_list))
            ->where('therapy_class', strtoupper($request->therapy_class ))

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
                    'MODULE_EXIT'=>$request->module_exit,

                    
                ]
            );



            $accum_benfit_stat = DB::table('TC_EXCEPTIONS' )
            ->where('ther_class_exception_list', $request->ther_class_exception_list )
            ->update(
                [
                    'ther_class_exception_list' => strtoupper( $request->ther_class_exception_list ),
                    'exception_name'=>$request->exception_name,
                   
                  

                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

        }


    }

    public function exceptionswithDesc(Request $request){

        $ndc = DB::table('FE_THERAPY_CLASS')
        ->get();
        return $this->respondWithToken($this->token(), '', $ndc);

    }
    public function search(Request $request)
    {
        $ndc = DB::table('TC_EXCEPTIONS')
                ->select('THER_CLASS_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('THER_CLASS_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getTCList($ndcid)
    {
        $ndclist = DB::table('TC_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->join('TC_EXCEPTIONS','TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST','=','TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')
                ->where('TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getTCItemDetails($ndcid,$ncdid2)
    {
        $ndc = DB::table('TC_EXCEPTION_LISTS')
        ->select('TC_EXCEPTION_LISTS.*', 'TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST as exception_list', 'TC_EXCEPTIONS.EXCEPTION_NAME as exception_name',
        'FE_THERAPY_CLASS.THERAPEUTIC_CLASS_DESC as tc_exception_description',
        'DRUG_MASTER1.LABEL_NAME as prefered_ndc_exception',
        'DRUG_MASTER2.LABEL_NAME as conversion_ndc_exception',
        )
        ->leftjoin('FE_THERAPY_CLASS', 'FE_THERAPY_CLASS.CLASS_CODE', '=', 'TC_EXCEPTION_LISTS.THERAPY_CLASS')
        ->leftjoin('DRUG_MASTER as DRUG_MASTER1','DRUG_MASTER1.NDC','=','TC_EXCEPTION_LISTS.PREFERRED_PRODUCT_NDC')
        ->leftjoin('DRUG_MASTER as DRUG_MASTER2','DRUG_MASTER2.NDC','=','TC_EXCEPTION_LISTS.CONVERSION_PRODUCT_NDC')
        ->leftjoin('TC_EXCEPTIONS','TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST','=','TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')

        ->where('TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST',strtoupper($ndcid))
        ->where('TC_EXCEPTION_LISTS.THERAPY_CLASS',strtoupper($ncdid2))
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
