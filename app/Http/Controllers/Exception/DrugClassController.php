<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugClassController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->where('DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('DRUG_CATGY_EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function DrugCategoryList(Request $request)
    {
        $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getTCList($ndcid)
    {
        $ndclist = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getTCItemDetails($ndcid)
    {
        $ndc = DB::table('TC_EXCEPTION_LISTS')
                    ->select('TC_EXCEPTION_LISTS.*', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST as exception_list', 'TC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->join('TC_EXCEPTIONS', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST', '=', 'TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')
                    ->where('TC_EXCEPTION_LISTS.therapy_class', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function getNDCItemDetails($ndcid){

        $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
        // ->select('DRUG_CATGY_EXCEPTION_NAMES.*', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_NAME')
        ->join('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
        ->where('PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')  ->get();
        return $this->respondWithToken($this->token(), '', $ndc);


    }

    public function add(Request $request)
    {
        
        $createddate = date('y-m-d');
        if ($request->new == 1)  {
            $drugcatgy = DB::table('DRUG_CATGY_EXCEPTION_NAMES')->insert(
                [
                    'drug_catgy_exception_list' => strtoupper($request->drug_catgy_exception_list),
                    'drug_catgy_exception_name' => $request->drug_catgy_exception_name,
                    'DATE_TIME_CREATED' => $createddate,
                    'USER_ID' => '', // TODO add user id
                    'DATE_TIME_MODIFIED' => '',
                    'USER_ID_CREATED' => '',
                    'FORM_ID' => ''
                ]
            );


            $plan=DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')->insert(
                [
                    'PLAN_ID'=>$request->plan_id,
                    'SCATEGORY'=>$request->scategory,
                    'STYPE'=>$request->stype,
                    'NEW_DRUG_STATUS'=>$request->new_drug_status,
                    'PROCESS_RULE'=>$request->process_rule,
                    'MAXIMUM_ALLOWABLE_COST'=>$request->maximum_allowable_cost,
                    'PHYSICIAN_LIST'=>$request->physician_list,
                    'PHYSICIAN_SPECIALTY_LIST'=>$request->physician_speciality_list,
                    'PHARMACY_LIST'=>$request->pharmacy_list,
                    'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                    'PREFERRED_PRODUCT_NDC'=>$request->prefered_product_ndc,
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
                    'MAX_RXS_PATIENT'=>$request->max_rxa_patient,
                    'GENERIC_COPAY_AMT'=>$request->generic_copay_amt,
                    'BRAND_COPAY_AMT'=>$request->brand_copay_amt,
                    'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                    'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                    'DENIAL_OVERRIDE'=>$request->denial_override,
                    'MAINTENANCE_DRUG'=>$request->maintenance_drug,
                    'SEX_RESTRICTION'=>$request->sex_restriction,
                    'MERGE_DEFAULTS'=>$request->merge_defaults,
                    'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                    'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                    'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                    'MODULE_EXIT'=>$request->module_exit,
                    'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                    'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                    'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                    'COPAY_NETWORK_OVRD'=>$request->copay_network_ovrd,
                    'DRUG_CATGY_EXCEPTION_LIST'=>$request->drug_catgy_exception_list,
                    'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                    'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                    'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                    'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                    'MIN_PRICE_OPT'=>$request->min_price_opt,
                    'MAX_PRICE_OPT'=>$request->macx_price_opt,
                    'VALID_RELATION_CODE'=>$request->valid_relation_code,
                    'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                    'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                    'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                    'PKG_DETERMINE_ID'=>$request->pkg_determine_id,
                    'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                    'TERMINATION_DATE'=>$request->termination_date,
                    'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                    'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                    'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                    'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                    'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                    
                    

                ]
            );


        

	return $this->respondWithToken($this->token(), 'Record added Successfully!', $drugcatgy);


        } else  if($request->new == 0){
            $benefitcode = DB::table('benefit_codes')
                                ->where('benefit_code', $request->benefit_code)
                                ->update(
                                    [
                                        'benefit_code' => strtoupper($request->benefit_code),
                                        'description' => $request->description,
                                        'DATE_TIME_CREATED' => $createddate,
                                        'USER_ID' => '', // TODO add user id
                                        'DATE_TIME_MODIFIED' => '',
                                        'USER_ID_CREATED' => '',
                                        'FORM_ID' => ''
                                    ]
                            );

           

            $benefitcode = DB::table('benefit_codes' ) ->where('benefit_code', 'like', '%' . $request->benefit_code. '%')->first();


        
        }


        return $this->respondWithToken($this->token(), 'Record updated Successfully! ', $benefitcode);
    }
}
