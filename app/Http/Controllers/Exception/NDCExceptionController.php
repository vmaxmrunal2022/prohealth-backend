<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NDCExceptionController extends Controller
{

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );


        $recordcheck = DB::table('NDC_EXCEPTION_LISTS')
        ->where('ndc_exception_list', strtoupper($request->ndc_exception_list))
        ->where('ndc', strtoupper($request->ndc))
        ->where('effective_date', $request->effective_date)

        ->first();




        if ( $request->has('add_new') ) {


            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Ndc  Exception ID already exists in the system..!!!', $recordcheck);


            }

            else{

                $accum_benfit_stat_names = DB::table('NDC_EXCEPTIONS')->insert(
                    [
                        'ndc_exception_list' => strtoupper($request->ndc_exception_list),
                        'exception_name'=>$request->exception_name,
                        
                    ]
                );
    
                $insert = DB::table('NDC_EXCEPTION_LISTS')->insert(
                    [
                        'NDC_EXCEPTION_LIST' =>strtoupper($request->ndc_exception_list),
                        'NDC'=>strtoupper($request->ndc),
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
                return $this->respondWithToken( $this->token(), 'Record Added Successfully',$insert);

            }

           


        } else {

            $update = DB::table('NDC_EXCEPTION_LISTS' )
            ->where('ndc',strtoupper($request->ndc))
            ->where('ndc_exception_list',strtoupper($request->ndc_exception_list))
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
            ->where('ndc_exception_list', strtoupper($request->ndc_exception_list ))
            ->update(
                [
                    'exception_name'=>strtoupper($request->exception_name),


                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

        }


    }

    public function getAllNDCS(){

        $ndc = DB::table('DRUG_MASTER')
        ->select('NDC','LABEL_NAME')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function ndcList(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }




    public function search(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
                ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('NDC_EXCEPTION_LISTS')
        ->join('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
        ->where('NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
        ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }
    public function getNDC($ndcid, $name)
    {
        $ndclist = DB::table('NDC_EXCEPTION_LISTS')
                ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                ->Where('NDC_EXCEPTIONS.EXCEPTION_NAME', 'like', '%' . strtoupper($name) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid,$ndcid2)

    {


       
      
 
            $ifset = DB::table('NDC_EXCEPTION_LISTS')
        
                    ->select('NDC_EXCEPTION_LISTS.*', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST as exception_list', 'NDC_EXCEPTIONS.EXCEPTION_NAME as exception_name',
                    'DRUG_MASTER.LABEL_NAME as ndc_exception_description',
                    'NDC_EXCEPTIONS1.EXCEPTION_NAME as preferd_ndc_description',
                    'NDC_EXCEPTIONS2.EXCEPTION_NAME as conversion_ndc_description',
                    )
                    ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
                    ->leftjoin('NDC_EXCEPTIONS as NDC_EXCEPTIONS1', 'NDC_EXCEPTIONS1.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.PREFERRED_PRODUCT_NDC')
                    ->leftjoin('NDC_EXCEPTIONS as NDC_EXCEPTIONS2', 'NDC_EXCEPTIONS2.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.CONVERSION_PRODUCT_NDC')
                    ->leftjoin('DRUG_MASTER', 'DRUG_MASTER.NDC', '=', 'NDC_EXCEPTION_LISTS.NDC')


                    ->where('NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST',strtoupper($ndcid))
                    ->where('NDC_EXCEPTION_LISTS.NDC',strtoupper($ndcid2))
                    ->first();

             return $this->respondWithToken($this->token(), '', $ifset);


        

       


       
    }

    public function getNdcDropDown(){
        $data = DB::table('NDC_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(),'',$data);
    }
}
