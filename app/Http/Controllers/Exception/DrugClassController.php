<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DrugClassController extends Controller
{


    public function search_old(Request $request)
    {
        if ($request->search_mode == 'category') {
            $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->join('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
                ->where(DB::raw('UPPER(PLAN_DRUG_CATGY_EXCEPTIONS.scategory)'), 'like', '%' . $request->search . '%')
                ->get();
            return $this->respondWithToken($this->token(), '', $ndc);
        } else if ($request->search_mode == 'type') {
            $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->join('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
                ->where(DB::raw('UPPER(PLAN_DRUG_CATGY_EXCEPTIONS.stype)'), 'like', '%' . $request->stype . '%')
                ->get();
            return $this->respondWithToken($this->token(), '', $ndc);
        } else if ($request->search_mode == 'description') {
            $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->join('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
                ->where(DB::raw('UPPER(DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST)'), 'like', '%' . $request->search . '%')
                ->orWhere(DB::raw('UPPER(DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_NAME)'), 'like', '%' . $request->search . '%')
                ->get();
            return $this->respondWithToken($this->token(), '', $ndc);
        } else {
            $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->join('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
                ->where(DB::raw('UPPER(DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST)'), 'like', '%' . $request->search . '%')
                ->orWhere(DB::raw('UPPER(DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_NAME)'), 'like', '%' . $request->search . '%')
                ->get();
            return $this->respondWithToken($this->token(), '', $ndc);
        }
    }

    public function drugClassDropDown(Request $request)
    {

        $drug_classes = DB::table('FE_SYSTEM_CATEGORIES')
            ->where('SCATEGORY', 'DRGCAT')
            ->get();
        return $this->respondWithToken($this->token(), '', $drug_classes);

    }

    public function search(Request $request)
    {
        $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
            ->select('DRUG_CATGY_EXCEPTION_LIST', 'DRUG_CATGY_EXCEPTION_NAME')
            ->where('DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . $request->search . '%')
            ->orWhere('DRUG_CATGY_EXCEPTION_NAME', 'like', '%' . $request->search . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNdc(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
            ->join('NDC_EXCEPTION_LISTS', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST')
            // ->where(DB::raw('UPPER(NDC_EXCEPTIONS.NDC_EXCEPTION_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
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
            ->where('DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . $ndcid . '%')
            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getTCItemDetails($ndcid)
    {
        $ndc = DB::table('TC_EXCEPTION_LISTS')
            ->select('TC_EXCEPTION_LISTS.*', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST as exception_list', 'TC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
            ->join('TC_EXCEPTIONS', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST', '=', 'TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')
            ->where('TC_EXCEPTION_LISTS.therapy_class', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCItemDetails($DRUG_CATGY_EXCEPTION_LIST,$scategory,$stype,$new_drug_status,$process_rule,$effective_date)
    {

        // $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
        //     ->select(
        //         'DRUG_CATGY_EXCEPTION_NAMES.*',
        //         'PLAN_DRUG_CATGY_EXCEPTIONS.*',
        //         'FE_SYSTEM_CATEGORIES.SDESCRIPTION',
        //         'MASTER1.LABEL_NAME as preferd_ndc_description',
        //         'MASTER2.LABEL_NAME as conversion_ndc_description'
        //     )
        //     ->leftjoin('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
        //     ->leftjoin('DRUG_MASTER AS MASTER1', 'MASTER1.NDC', '=', 'PLAN_DRUG_CATGY_EXCEPTIONS.PREFERRED_PRODUCT_NDC')
        //     ->leftjoin('DRUG_MASTER AS MASTER2', 'MASTER2.NDC', '=', 'PLAN_DRUG_CATGY_EXCEPTIONS.CONVERSION_PRODUCT_NDC')
        //     ->leftjoin('FE_SYSTEM_CATEGORIES', 'FE_SYSTEM_CATEGORIES.STYPE', '=', 'PLAN_DRUG_CATGY_EXCEPTIONS.SCATEGORY')

        //     // ->where('DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST',$DRUG_CATGY_EXCEPTION_LIST)
        //     ->where('PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST',$DRUG_CATGY_EXCEPTION_LIST)
        //     ->where('PLAN_DRUG_CATGY_EXCEPTIONS.SCATEGORY',$scategory)
        //     ->where('PLAN_DRUG_CATGY_EXCEPTIONS.STYPE',$stype)
        //     ->where('PLAN_DRUG_CATGY_EXCEPTIONS.NEW_DRUG_STATUS',$new_drug_status)
        //     ->where('PLAN_DRUG_CATGY_EXCEPTIONS.process_rule',$process_rule)
        //     ->where('PLAN_DRUG_CATGY_EXCEPTIONS.EFFECTIVE_DATE',$effective_date)
        //     ->where('PLAN_DRUG_CATGY_EXCEPTIONS.DIAGNOSIS_LIST',$diagnosis_list)

        //     ->first();


            $ndc = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
            // ->select(
            //     'DRUG_CATGY_EXCEPTION_NAMES.*',
            //     'PLAN_DRUG_CATGY_EXCEPTIONS.*',
            //     'FE_SYSTEM_CATEGORIES.SDESCRIPTION',
            //     'MASTER1.LABEL_NAME as preferd_ndc_description',
            //     'MASTER2.LABEL_NAME as conversion_ndc_description'
            // )
            // ->leftjoin('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
            // ->leftjoin('DRUG_MASTER AS MASTER1', 'MASTER1.NDC', '=', 'PLAN_DRUG_CATGY_EXCEPTIONS.PREFERRED_PRODUCT_NDC')
            // ->leftjoin('DRUG_MASTER AS MASTER2', 'MASTER2.NDC', '=', 'PLAN_DRUG_CATGY_EXCEPTIONS.CONVERSION_PRODUCT_NDC')
            // ->leftjoin('FE_SYSTEM_CATEGORIES', 'FE_SYSTEM_CATEGORIES.STYPE', '=', 'PLAN_DRUG_CATGY_EXCEPTIONS.SCATEGORY')

            // ->where('DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST',$DRUG_CATGY_EXCEPTION_LIST)
            ->where('PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST',$DRUG_CATGY_EXCEPTION_LIST)
            ->where('PLAN_DRUG_CATGY_EXCEPTIONS.SCATEGORY',$scategory)
            ->where('PLAN_DRUG_CATGY_EXCEPTIONS.STYPE',$stype)
            ->where('PLAN_DRUG_CATGY_EXCEPTIONS.NEW_DRUG_STATUS',$new_drug_status)
            ->where('PLAN_DRUG_CATGY_EXCEPTIONS.process_rule',$process_rule)
            ->where('PLAN_DRUG_CATGY_EXCEPTIONS.EFFECTIVE_DATE',$effective_date)
            // ->where('PLAN_DRUG_CATGY_EXCEPTIONS.DIAGNOSIS_LIST',$diagnosis_list)

            ->first();




        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function addcopy(Request $request)
    {


        $createddate = date('y-m-d');


        $exist = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
            ->where('DRUG_CATGY_EXCEPTION_LIST', $request->drug_catgy_exception_list)->first();


        if ($request->new) {


            if ($exist) {

                return $this->respondWithToken($this->token(), 'Drug Classification ID is Already Exists', $exist);


            } else {

                $drugcatgy = DB::table('DRUG_CATGY_EXCEPTION_NAMES')->insert(
                    [
                        'drug_catgy_exception_list' => $request->drug_catgy_exception_list,
                        'drug_catgy_exception_name' => $request->drug_catgy_exception_name,
                        'DATE_TIME_CREATED' => $createddate,
                        'USER_ID' => '',
                        // TODO add user id
                        'DATE_TIME_MODIFIED' => '',
                        // 'USER_ID_CREATED' => '',
                        'FORM_ID' => ''
                    ]
                );

                $plan = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')->insert(
                    [
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'PLAN_ID' => $request->plan_id,
                        'SCATEGORY' => $request->scategory,
                        'STYPE' => $request->stype,
                        'NEW_DRUG_STATUS' => $request->new_drug_status,
                        'PROCESS_RULE' => $request->process_rule,
                        'MAXIMUM_ALLOWABLE_COST' => $request->maximum_allowable_cost,
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'PHYSICIAN_SPECIALTY_LIST' => $request->physician_specialty_list,
                        'PHARMACY_LIST' => $request->pharmacy_list,
                        'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        'PREFERRED_PRODUCT_NDC' => $request->preferred_product_ndc,
                        'CONVERSION_PRODUCT_NDC' => $request->conversion_product_ndc,
                        'ALTERNATE_PRICE_SCHEDULE' => $request->alternate_price_schedule,
                        'ALTERNATE_COPAY_SCHED' => $request->alternate_copay_sched,
                        'MESSAGE' => $request->message,
                        'MESSAGE_STOP_DATE' => date('Ymd', strtotime($request->message_stop_date)),
                        'MIN_RX_QTY' => $request->min_rx_qty,
                        'MAX_RX_QTY' => $request->max_rx_qty,
                        'MIN_RX_DAYS' => $request->min_rx_days,
                        'MAX_RX_DAYS' => $request->max_rx_days,
                        'MIN_CTL_DAYS' => $request->min_ctl_days,
                        'MAX_CTL_DAYS' => $request->max_ctl_days,
                        'MAX_REFILLS' => $request->max_refills,
                        'MAX_DAYS_PER_FILL' => $request->max_days_per_fill,
                        'MAX_DOSE' => $request->max_dose,
                        'MIN_AGE' => $request->min_age,
                        'MAX_AGE' => $request->max_age,
                        'MIN_PRICE' => $request->min_price,
                        'MAX_PRICE' => $request->max_price,
                        'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                        'max_price_patient' => $request->max_price_patient,
                        'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                        'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                        'MAINT_DOSE_UNITS_DAY' => $request->maint_dose_units_day,
                        'ACUTE_DOSING_DAYS' => $request->acute_dosing_days,
                        'DENIAL_OVERRIDE' => $request->denial_override,
                        'MAINTENANCE_DRUG' => $request->maintenance_drug,
                        'SEX_RESTRICTION' => $request->sex_restriction,
                        'MERGE_DEFAULTS' => $request->merge_defaults,
                        'MAIL_ORDER_MIN_RX_DAYS' => $request->mail_order_min_rx_days,
                        'MAIL_ORDER_MAX_RX_DAYS' => $request->mail_order_max_rx_days,
                        'MAIL_ORDER_MAX_REFILLS' => $request->mail_order_max_refills,
                        'MODULE_EXIT' => $request->module_exit,
                        'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                        'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                        'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                        'COPAY_NETWORK_OVRD' => $request->copay_network_ovrd,
                        'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                        'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                        'MAIL_ORD_MAX_DAYS_SUPPLY_OPT' => $request->mail_ord_max_days_supply_opt,
                        'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                        'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                        'MIN_PRICE_OPT' => $request->min_price_opt,
                        'MAX_PRICE_OPT' => $request->macx_price_opt,
                        'VALID_RELATION_CODE' => $request->valid_relation_code,
                        'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                        'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                        'PKG_DETERMINE_ID' => $request->pkg_determine_id,
                        'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                        'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                        'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                        'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                        'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                        'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                        'BNG_SNGL_INC_EXC_IND' => $request->bng_sngl_inc_exc_ind,
                        'BNG_MULTI_INC_EXC_IND' => $request->bng_multi_inc_exc_ind,
                        'BGA_INC_EXC_IND' => $request->bga_inc_exc_ind,
                        'GEN_INC_EXC_IND' => $request->gen_inc_exc_ind,
                    ]
                );
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $drugcatgy);

            }


        } else
            $drugcatgy = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->where('drug_catgy_exception_list', $request->drug_catgy_exception_list)
                ->update(
                    [
                        'drug_catgy_exception_name' => $request->drug_catgy_exception_name,
                        'DATE_TIME_CREATED' => $createddate,
                        'USER_ID' => '',
                        // TODO add user id
                        'DATE_TIME_MODIFIED' => date('Ymd H:i:s'),
                        // 'USER_ID_CREATED' => '',
                        'FORM_ID' => ''
                    ]
                );
        $check_exist = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
            ->where('SCATEGORY', $request->scategory)
            ->where('STYPE', $request->stype)
            ->where('effective_date', date('Ymd', strtotime($request->effective_date)))
            ->where('termination_date', date('Ymd', strtotime($request->termination_date)))
            ->get()
            ->count();
        // dd($request->new_drug_status);
        if ($check_exist <= 0) {
            $plan = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
            ->insert(
                [
                    'max_price_patient' => $request->max_price_patient,
                    'effective_date' => date('Ymd', strtotime($request->effective_date)),
                    'termination_date' => date('Ymd', strtotime($request->termination_date)),
                    'PLAN_ID' => $request->plan_id,
                    'SCATEGORY' => $request->scategory,
                    'STYPE' => $request->stype,
                    'NEW_DRUG_STATUS' => $request->new_drug_status,
                    'PROCESS_RULE' => $request->process_rule,
                    'MAXIMUM_ALLOWABLE_COST' => $request->maximum_allowable_cost,
                    'PHYSICIAN_LIST' => $request->physician_list,
                    'PHYSICIAN_SPECIALTY_LIST' => $request->physician_specialty_list,
                    'PHARMACY_LIST' => $request->pharmacy_list,
                    'DIAGNOSIS_LIST' => $request->diagnosis_list,
                    'PREFERRED_PRODUCT_NDC' => $request->preferred_product_ndc,
                    'CONVERSION_PRODUCT_NDC' => $request->conversion_product_ndc,
                    'ALTERNATE_PRICE_SCHEDULE' => $request->alternate_price_schedule,
                    'ALTERNATE_COPAY_SCHED' => $request->alternate_copay_sched,
                    'MESSAGE' => $request->message,
                    'MESSAGE_STOP_DATE' => date('Ymd', strtotime($request->message_stop_date)),
                    'MIN_RX_QTY' => $request->min_rx_qty,
                    'MAX_RX_QTY' => $request->max_rx_qty,
                    'MIN_RX_DAYS' => $request->min_rx_days,
                    'MAX_RX_DAYS' => $request->max_rx_days,
                    'MIN_CTL_DAYS' => $request->min_ctl_days,
                    'MAX_CTL_DAYS' => $request->max_ctl_days,
                    'MAX_REFILLS' => $request->max_refills,
                    'MAX_DAYS_PER_FILL' => $request->max_days_per_fill,
                    'MAX_DOSE' => $request->max_dose,
                    'MIN_AGE' => $request->min_age,
                    'MAX_AGE' => $request->max_age,
                    'MIN_PRICE' => $request->min_price,
                    'MAX_PRICE' => $request->max_price,
                    'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                    'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                    'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                    'MAINT_DOSE_UNITS_DAY' => $request->maint_dose_units_day,
                    'ACUTE_DOSING_DAYS' => $request->acute_dosing_days,
                    'DENIAL_OVERRIDE' => $request->denial_override,
                    'MAINTENANCE_DRUG' => $request->maintenance_drug,
                    'SEX_RESTRICTION' => $request->sex_restriction,
                    'MERGE_DEFAULTS' => $request->merge_defaults,
                    'MAIL_ORDER_MIN_RX_DAYS' => $request->mail_order_min_rx_days,
                    'MAIL_ORDER_MAX_RX_DAYS' => $request->mail_order_max_rx_days,
                    'MAIL_ORDER_MAX_REFILLS' => $request->mail_order_max_refills,
                    'MODULE_EXIT' => $request->module_exit,
                    'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                    'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                    'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                    'COPAY_NETWORK_OVRD' => $request->copay_network_ovrd,
                    'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                    'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                    'MAIL_ORD_MAX_DAYS_SUPPLY_OPT' => $request->mail_ord_max_days_supply_opt,
                    'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                    'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                    'MIN_PRICE_OPT' => $request->min_price_opt,
                    'MAX_PRICE_OPT' => $request->macx_price_opt,
                    'VALID_RELATION_CODE' => $request->valid_relation_code,
                    'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                    'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                    'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                    'PKG_DETERMINE_ID' => $request->pkg_determine_id,
                    'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                    'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                    'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                    'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                    'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                    'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                    'BNG_SNGL_INC_EXC_IND' => $request->bng_sngl_inc_exc_ind,
                    'BNG_MULTI_INC_EXC_IND' => $request->bng_multi_inc_exc_ind,
                    'BGA_INC_EXC_IND' => $request->bga_inc_exc_ind,
                    'GEN_INC_EXC_IND' => $request->gen_inc_exc_ind,

                ]
            );
        } else {
            $plan = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                ->where('SCATEGORY', $request->scategory)
                ->where('STYPE', $request->stype)
                ->where('effective_date', $request->effective_date)
                ->where('termination_date', $request->termination_date)
                ->update(
                    [
                        'max_price_patient' => $request->max_price_patient,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'PLAN_ID' => $request->plan_id,
                        // 'SCATEGORY' => $request->scategory,
                        // 'STYPE' => $request->stype,
                        'NEW_DRUG_STATUS' => $request->new_drug_status,
                        'PROCESS_RULE' => $request->process_rule,
                        'MAXIMUM_ALLOWABLE_COST' => $request->maximum_allowable_cost,
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'PHYSICIAN_SPECIALTY_LIST' => $request->physician_specialty_list,
                        'PHARMACY_LIST' => $request->pharmacy_list,
                        'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        'PREFERRED_PRODUCT_NDC' => $request->preferred_product_ndc,
                        'CONVERSION_PRODUCT_NDC' => $request->conversion_product_ndc,
                        'ALTERNATE_PRICE_SCHEDULE' => $request->alternate_price_schedule,
                        'ALTERNATE_COPAY_SCHED' => $request->alternate_copay_sched,
                        'MESSAGE' => $request->message,
                        'MIN_RX_QTY' => $request->min_rx_qty,
                        'MAX_RX_QTY' => $request->max_rx_qty,
                        'MIN_RX_DAYS' => $request->min_rx_days,
                        'MAX_RX_DAYS' => $request->max_rx_days,
                        'MIN_CTL_DAYS' => $request->min_ctl_days,
                        'MAX_CTL_DAYS' => $request->max_ctl_days,
                        'MAX_REFILLS' => $request->max_refills,
                        'MAX_DAYS_PER_FILL' => $request->max_days_per_fill,
                        'MAX_DOSE' => $request->max_dose,
                        'MIN_AGE' => $request->min_age,
                        'MAX_AGE' => $request->max_age,
                        'MIN_PRICE' => $request->min_price,
                        'MAX_PRICE' => $request->max_price,
                        'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                        'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                        'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                        'MAINT_DOSE_UNITS_DAY' => $request->maint_dose_units_day,
                        'ACUTE_DOSING_DAYS' => $request->acute_dosing_days,
                        'DENIAL_OVERRIDE' => $request->denial_override,
                        'MAINTENANCE_DRUG' => $request->maintenance_drug,
                        'SEX_RESTRICTION' => $request->sex_restriction,
                        'MERGE_DEFAULTS' => $request->merge_defaults,
                        'MAIL_ORDER_MIN_RX_DAYS' => $request->mail_order_min_rx_days,
                        'MAIL_ORDER_MAX_RX_DAYS' => $request->mail_order_max_rx_days,
                        'MAIL_ORDER_MAX_REFILLS' => $request->mail_order_max_refills,
                        'MODULE_EXIT' => $request->module_exit,
                        'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                        'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                        'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                        'COPAY_NETWORK_OVRD' => $request->copay_network_ovrd,
                        'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                        'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                        'MAIL_ORD_MAX_DAYS_SUPPLY_OPT' => $request->mail_ord_max_days_supply_opt,
                        'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                        'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                        'MIN_PRICE_OPT' => $request->min_price_opt,
                        'MAX_PRICE_OPT' => $request->macx_price_opt,
                        'VALID_RELATION_CODE' => $request->valid_relation_code,
                        'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                        'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                        'PKG_DETERMINE_ID' => $request->pkg_determine_id,
                        'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                        'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                        'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                        'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                        'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                        'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                        'BNG_SNGL_INC_EXC_IND' => $request->bng_sngl_inc_exc_ind,
                        'BNG_MULTI_INC_EXC_IND' => $request->bng_multi_inc_exc_ind,
                        'BGA_INC_EXC_IND' => $request->bga_inc_exc_ind,
                        'GEN_INC_EXC_IND' => $request->gen_inc_exc_ind,
                        'MESSAGE_STOP_DATE' => $request->message_stop_date,
                    ]
                );

            $drugcatgy = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                ->join(
                    'DRUG_CATGY_EXCEPTION_NAMES',
                    DB::raw('UPPER(DRUG_CATGY_EXCEPTION_NAMES.drug_catgy_exception_list)'),
                    '=',
                    DB::raw('UPPER(PLAN_DRUG_CATGY_EXCEPTIONS.drug_catgy_exception_list)')
                )
                ->where(DB::raw('UPPER(PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST)'), $request->drug_catgy_exception_list)
                ->where('PLAN_DRUG_CATGY_EXCEPTIONS.SCATEGORY', $request->scategory)
                ->where('PLAN_DRUG_CATGY_EXCEPTIONS.STYPE', $request->stype)
                ->where('PLAN_DRUG_CATGY_EXCEPTIONS.effective_date', date('Ymd', strtotime($request->effective_date)))
                ->where('PLAN_DRUG_CATGY_EXCEPTIONS.termination_date', date('Ymd', strtotime($request->termination_date)))
                ->first();
        }

        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $drugcatgy);
    }

    public function add(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
        ->where('drug_catgy_exception_list',$request->ndc_exception_list)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'drug_catgy_exception_list' => ['required', 'max:10', Rule::unique('DRUG_CATGY_EXCEPTION_NAMES')->where(function ($q) {
                    $q->whereNotNull('drug_catgy_exception_list');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "drug_catgy_exception_name"=>['required','max:2'],
                "effective_date"=>['max:1'],
                'termination_date'=>['required|date|after_or_equal:effective_date','max:15','min:5'],
                'scategory'=>['max:10'],
                'sdescription'=>['max:10'],
                'stype'=>['max:10'],
                'new_drug_status'=>['max:10'],
                'process_rule'=>['max:11'],
                'module_exit'=>['max:11'],
                'preferred_product_ndc'=>['max:10'],
                'conversion_product_ndc'=>['max:10'],
                'message'=>['max:40'],
                'message_stop_date'=>['max:10'],
                'reject_only_msg_flag'=>['max:6'],
                'min_rx_qty'=>['max:6'],
                'max_rx_qty'=>['max:6'],
                'max_rxs_patient'=>['max:6'],
                'mail_order_min_rx_days'=>['max:6'],
                'mail_ord_max_days_supply_opt'=>['max:6'],
                'min_price'=>['max:6'],
                'max_price'=>['max:6'],
                'max_price_patient'=>['max:6'],
                'mail_ord_max_fills_opt'=>['max:6'],
                'min_rx_days'=>['max:6'],
                'max_days_supply_opt'=>['min:2','max:12'],
                'retail_max_fills_opt'=>['min:2','max:12'],
                'valid_relation_code'=>['max:6'],
                'min_ctl_days'=>['max:12','min:2'],
                'max_ctl_days'=>['max:12','min:2'],
                'max_rx_qty_opt'=>['max:12|min:2'],
                'valid_relation_code'=>['max:6'],
                'max_days_per_fill'=>['max:6'],
                'max_dose'=>['max:2'],
                'starter_dose_days'=>['max:1'],
                'sex_restriction'=>['max:1'],
                'drug_cov_start_days'=>['max:1'],
                'starter_dose_bypass_days'=>['numeric|max:6'],
                'alternate_price_schedule'=>['numeric|max:6'],
                'drug_cov_start_days'=>['numeric|max:6'],
                'starter_dose_bypass_days'=>['numeric|max:6'],
                'alternate_copay_sched'=>['max:1'],
                'brand_copay_amt'=>['max:1'],
                'min_age'=>['numeric|max:6'],
                'max_age'=>['numeric|max:6'],
                'maint_dose_units_day'=>['max:10'],
                'generic_copay_amt'=>['max:10'],
                'merge_defaults'=>['max:1'],
                'max_qty_over_time'=>['max:1'],
                'maximum_allowable_cost'=>['max:1'],
                'max_days_over_time'=>['max:1'],
               


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'NDC Exception Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('DRUG_CATGY_EXCEPTION_NAMES')->insert(
                    [
                        'drug_catgy_exception_list' => $request->drug_catgy_exception_list,
                        'drug_catgy_exception_name'=>$request->drug_catgy_exception_name,
                        
                    ]
                );
    
                $add = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                ->insert(
                    [
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'PLAN_ID' => $request->plan_id,
                        'SCATEGORY' => $request->scategory,
                        'STYPE' => $request->stype,
                        'NEW_DRUG_STATUS' => $request->new_drug_status,
                        'PROCESS_RULE' => $request->process_rule,
                        'MAXIMUM_ALLOWABLE_COST' => $request->maximum_allowable_cost,
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'PHYSICIAN_SPECIALTY_LIST' => $request->physician_specialty_list,
                        'PHARMACY_LIST' => $request->pharmacy_list,
                        'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        'PREFERRED_PRODUCT_NDC' => $request->preferred_product_ndc,
                        'CONVERSION_PRODUCT_NDC' => $request->conversion_product_ndc,
                        'ALTERNATE_PRICE_SCHEDULE' => $request->alternate_price_schedule,
                        'ALTERNATE_COPAY_SCHED' => $request->alternate_copay_sched,
                        'MESSAGE' => $request->message,
                        'MESSAGE_STOP_DATE' => date('Ymd', strtotime($request->message_stop_date)),
                        'MIN_RX_QTY' => $request->min_rx_qty,
                        'MAX_RX_QTY' => $request->max_rx_qty,
                        'MIN_RX_DAYS' => $request->min_rx_days,
                        'MAX_RX_DAYS' => $request->max_rx_days,
                        'MIN_CTL_DAYS' => $request->min_ctl_days,
                        'MAX_CTL_DAYS' => $request->max_ctl_days,
                        'MAX_REFILLS' => $request->max_refills,
                        'MAX_DAYS_PER_FILL' => $request->max_days_per_fill,
                        'MAX_DOSE' => $request->max_dose,
                        'MIN_AGE' => $request->min_age,
                        'MAX_AGE' => $request->max_age,
                        'MIN_PRICE' => $request->min_price,
                        'MAX_PRICE' => $request->max_price,
                        'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                        'max_price_patient' => $request->max_price_patient,
                        'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                        'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                        'MAINT_DOSE_UNITS_DAY' => $request->maint_dose_units_day,
                        'ACUTE_DOSING_DAYS' => $request->acute_dosing_days,
                        'DENIAL_OVERRIDE' => $request->denial_override,
                        'MAINTENANCE_DRUG' => $request->maintenance_drug,
                        'SEX_RESTRICTION' => $request->sex_restriction,
                        'MERGE_DEFAULTS' => $request->merge_defaults,
                        'MAIL_ORDER_MIN_RX_DAYS' => $request->mail_order_min_rx_days,
                        'MAIL_ORDER_MAX_RX_DAYS' => $request->mail_order_max_rx_days,
                        'MAIL_ORDER_MAX_REFILLS' => $request->mail_order_max_refills,
                        'MODULE_EXIT' => $request->module_exit,
                        'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                        'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                        'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                        'COPAY_NETWORK_OVRD' => $request->copay_network_ovrd,
                        'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                        'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                        'MAIL_ORD_MAX_DAYS_SUPPLY_OPT' => $request->mail_ord_max_days_supply_opt,
                        'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                        'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                        'MIN_PRICE_OPT' => $request->min_price_opt,
                        'MAX_PRICE_OPT' => $request->macx_price_opt,
                        'VALID_RELATION_CODE' => $request->valid_relation_code,
                        'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                        'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                        'PKG_DETERMINE_ID' => $request->pkg_determine_id,
                        'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                        'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                        'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                        'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                        'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                        'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                        'BNG_SNGL_INC_EXC_IND' => $request->bng_sngl_inc_exc_ind,
                        'BNG_MULTI_INC_EXC_IND' => $request->bng_multi_inc_exc_ind,
                        'BGA_INC_EXC_IND' => $request->bga_inc_exc_ind,
                        'GEN_INC_EXC_IND' => $request->gen_inc_exc_ind,
                    ]
                );

                    
    
                $add = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')->where('PLAN_ID', 'like', '%' . $request->plan_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                
                "drug_catgy_exception_list" => ['required','max:36'],
                "drug_catgy_exception_name"=>['required','max:2'],
                "effective_date"=>['max:1'],
                'termination_date'=>['required|date|after_or_equal:effective_date','max:15','min:5'],
                'scategory'=>['max:10'],
                'sdescription'=>['max:10'],
                'stype'=>['max:10'],
                'new_drug_status'=>['max:10'],
                'process_rule'=>['max:11'],
                'module_exit'=>['max:11'],
                'preferred_product_ndc'=>['max:10'],
                'conversion_product_ndc'=>['max:10'],
                'message'=>['max:40'],
                'message_stop_date'=>['max:10'],
                'reject_only_msg_flag'=>['max:6'],
                'min_rx_qty'=>['max:6'],
                'max_rx_qty'=>['max:6'],
                'max_rxs_patient'=>['max:6'],
                'mail_order_min_rx_days'=>['max:6'],
                'mail_ord_max_days_supply_opt'=>['max:6'],
                'min_price'=>['max:6'],
                'max_price'=>['max:6'],
                'max_price_patient'=>['max:6'],
                'mail_ord_max_fills_opt'=>['max:6'],
                'min_rx_days'=>['max:6'],
                'max_days_supply_opt'=>['min:2','max:12'],
                'retail_max_fills_opt'=>['min:2','max:12'],
                'valid_relation_code'=>['max:6'],
                'min_ctl_days'=>['max:12','min:2'],
                'max_ctl_days'=>['max:12','min:2'],
                'max_rx_qty_opt'=>['max:12|min:2'],
                'valid_relation_code'=>['max:6'],
                'max_days_per_fill'=>['max:6'],
                'max_dose'=>['max:2'],
                'starter_dose_days'=>['max:1'],
                'sex_restriction'=>['max:1'],
                'drug_cov_start_days'=>['max:1'],
                'starter_dose_bypass_days'=>['numeric|max:6'],
                'alternate_price_schedule'=>['numeric|max:6'],
                'drug_cov_start_days'=>['numeric|max:6'],
                'starter_dose_bypass_days'=>['numeric|max:6'],
                'alternate_copay_sched'=>['max:1'],
                'brand_copay_amt'=>['max:1'],
                'min_age'=>['numeric|max:6'],
                'max_age'=>['numeric|max:6'],
                'maint_dose_units_day'=>['max:10'],
                'generic_copay_amt'=>['max:10'],
                'merge_defaults'=>['max:1'],
                'max_qty_over_time'=>['max:1'],
                'maximum_allowable_cost'=>['max:1'],
                'max_days_over_time'=>['max:1'],
               

            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // dd($request->add_new);

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }
    
               
                $update_names = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->where('drug_catgy_exception_list', $request->drug_catgy_exception_list )
                ->update(
                    [
                        'drug_catgy_exception_name'=>$request->drug_catgy_exception_name,
                        
                    ]
                );

              
                    
    
                $checkGPI =  DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                ->where('SCATEGORY', $request->scategory)
                ->where('STYPE', $request->stype)
                ->where('effective_date', date('Ymd', strtotime($request->effective_date)))
                ->where('termination_date', date('Ymd', strtotime($request->termination_date)))
                ->get()
                ->count();
                    // dd($checkGPI);
                // if result >=1 then update NDC_EXCEPTION_LISTS table record
                //if result 0 then add NDC_EXCEPTION_LISTS record

    
                if ($checkGPI <= "0") {
                    $update = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                    ->insert(
                        [
                            'max_price_patient' => $request->max_price_patient,
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'termination_date' => date('Ymd', strtotime($request->termination_date)),
                            'PLAN_ID' => $request->plan_id,
                            'SCATEGORY' => $request->scategory,
                            'STYPE' => $request->stype,
                            'NEW_DRUG_STATUS' => $request->new_drug_status,
                            'PROCESS_RULE' => $request->process_rule,
                            'MAXIMUM_ALLOWABLE_COST' => $request->maximum_allowable_cost,
                            'PHYSICIAN_LIST' => $request->physician_list,
                            'PHYSICIAN_SPECIALTY_LIST' => $request->physician_specialty_list,
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'DIAGNOSIS_LIST' => $request->diagnosis_list,
                            'PREFERRED_PRODUCT_NDC' => $request->preferred_product_ndc,
                            'CONVERSION_PRODUCT_NDC' => $request->conversion_product_ndc,
                            'ALTERNATE_PRICE_SCHEDULE' => $request->alternate_price_schedule,
                            'ALTERNATE_COPAY_SCHED' => $request->alternate_copay_sched,
                            'MESSAGE' => $request->message,
                            'MESSAGE_STOP_DATE' => date('Ymd', strtotime($request->message_stop_date)),
                            'MIN_RX_QTY' => $request->min_rx_qty,
                            'MAX_RX_QTY' => $request->max_rx_qty,
                            'MIN_RX_DAYS' => $request->min_rx_days,
                            'MAX_RX_DAYS' => $request->max_rx_days,
                            'MIN_CTL_DAYS' => $request->min_ctl_days,
                            'MAX_CTL_DAYS' => $request->max_ctl_days,
                            'MAX_REFILLS' => $request->max_refills,
                            'MAX_DAYS_PER_FILL' => $request->max_days_per_fill,
                            'MAX_DOSE' => $request->max_dose,
                            'MIN_AGE' => $request->min_age,
                            'MAX_AGE' => $request->max_age,
                            'MIN_PRICE' => $request->min_price,
                            'MAX_PRICE' => $request->max_price,
                            'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                            'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                            'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                            'MAINT_DOSE_UNITS_DAY' => $request->maint_dose_units_day,
                            'ACUTE_DOSING_DAYS' => $request->acute_dosing_days,
                            'DENIAL_OVERRIDE' => $request->denial_override,
                            'MAINTENANCE_DRUG' => $request->maintenance_drug,
                            'SEX_RESTRICTION' => $request->sex_restriction,
                            'MERGE_DEFAULTS' => $request->merge_defaults,
                            'MAIL_ORDER_MIN_RX_DAYS' => $request->mail_order_min_rx_days,
                            'MAIL_ORDER_MAX_RX_DAYS' => $request->mail_order_max_rx_days,
                            'MAIL_ORDER_MAX_REFILLS' => $request->mail_order_max_refills,
                            'MODULE_EXIT' => $request->module_exit,
                            'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                            'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                            'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                            'COPAY_NETWORK_OVRD' => $request->copay_network_ovrd,
                            'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                            'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                            'MAIL_ORD_MAX_DAYS_SUPPLY_OPT' => $request->mail_ord_max_days_supply_opt,
                            'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                            'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                            'MIN_PRICE_OPT' => $request->min_price_opt,
                            'MAX_PRICE_OPT' => $request->macx_price_opt,
                            'VALID_RELATION_CODE' => $request->valid_relation_code,
                            'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                            'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                            'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                            'PKG_DETERMINE_ID' => $request->pkg_determine_id,
                            'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                            'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                            'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                            'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                            'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                            'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                            'BNG_SNGL_INC_EXC_IND' => $request->bng_sngl_inc_exc_ind,
                            'BNG_MULTI_INC_EXC_IND' => $request->bng_multi_inc_exc_ind,
                            'BGA_INC_EXC_IND' => $request->bga_inc_exc_ind,
                            'GEN_INC_EXC_IND' => $request->gen_inc_exc_ind,
        
                        ]);

                $update = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')->where('plan_id', 'like', '%' . $request->plan_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                } else {

 
                  

                //     $update = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS' )
                //     ->where('SCATEGORY', $request->scategory)
                //     ->where('STYPE', $request->stype)
                //     ->where('effective_date', $request->effective_date)
                //     ->where('termination_date', $request->termination_date)
                //    ->update(
                //     [
                //         'max_price_patient' => $request->max_price_patient,
                //         'effective_date' => date('Ymd', strtotime($request->effective_date)),
                //         'termination_date' => date('Ymd', strtotime($request->termination_date)),
                //         'PLAN_ID' => $request->plan_id,
                //         // 'SCATEGORY' => $request->scategory,
                //         // 'STYPE' => $request->stype,
                //         'NEW_DRUG_STATUS' => $request->new_drug_status,
                //         'PROCESS_RULE' => $request->process_rule,
                //         'MAXIMUM_ALLOWABLE_COST' => $request->maximum_allowable_cost,
                //         'PHYSICIAN_LIST' => $request->physician_list,
                //         'PHYSICIAN_SPECIALTY_LIST' => $request->physician_specialty_list,
                //         'PHARMACY_LIST' => $request->pharmacy_list,
                //         'DIAGNOSIS_LIST' => $request->diagnosis_list,
                //         'PREFERRED_PRODUCT_NDC' => $request->preferred_product_ndc,
                //         'CONVERSION_PRODUCT_NDC' => $request->conversion_product_ndc,
                //         'ALTERNATE_PRICE_SCHEDULE' => $request->alternate_price_schedule,
                //         'ALTERNATE_COPAY_SCHED' => $request->alternate_copay_sched,
                //         'MESSAGE' => $request->message,
                //         'MIN_RX_QTY' => $request->min_rx_qty,
                //         'MAX_RX_QTY' => $request->max_rx_qty,
                //         'MIN_RX_DAYS' => $request->min_rx_days,
                //         'MAX_RX_DAYS' => $request->max_rx_days,
                //         'MIN_CTL_DAYS' => $request->min_ctl_days,
                //         'MAX_CTL_DAYS' => $request->max_ctl_days,
                //         'MAX_REFILLS' => $request->max_refills,
                //         'MAX_DAYS_PER_FILL' => $request->max_days_per_fill,
                //         'MAX_DOSE' => $request->max_dose,
                //         'MIN_AGE' => $request->min_age,
                //         'MAX_AGE' => $request->max_age,
                //         'MIN_PRICE' => $request->min_price,
                //         'MAX_PRICE' => $request->max_price,
                //         'MAX_RXS_PATIENT' => $request->max_rxs_patient,
                //         'GENERIC_COPAY_AMT' => $request->generic_copay_amt,
                //         'BRAND_COPAY_AMT' => $request->brand_copay_amt,
                //         'MAINT_DOSE_UNITS_DAY' => $request->maint_dose_units_day,
                //         'ACUTE_DOSING_DAYS' => $request->acute_dosing_days,
                //         'DENIAL_OVERRIDE' => $request->denial_override,
                //         'MAINTENANCE_DRUG' => $request->maintenance_drug,
                //         'SEX_RESTRICTION' => $request->sex_restriction,
                //         'MERGE_DEFAULTS' => $request->merge_defaults,
                //         'MAIL_ORDER_MIN_RX_DAYS' => $request->mail_order_min_rx_days,
                //         'MAIL_ORDER_MAX_RX_DAYS' => $request->mail_order_max_rx_days,
                //         'MAIL_ORDER_MAX_REFILLS' => $request->mail_order_max_refills,
                //         'MODULE_EXIT' => $request->module_exit,
                //         'MAX_RXS_TIME_FLAG' => $request->max_rxs_time_flag,
                //         'MAX_PRICE_TIME_FLAG' => $request->max_price_time_flag,
                //         'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                //         'COPAY_NETWORK_OVRD' => $request->copay_network_ovrd,
                //         'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_list,
                //         'MAX_DAYS_SUPPLY_OPT' => $request->max_days_supply_opt,
                //         'MAIL_ORD_MAX_DAYS_SUPPLY_OPT' => $request->mail_ord_max_days_supply_opt,
                //         'RETAIL_MAX_FILLS_OPT' => $request->retail_max_fills_opt,
                //         'MAIL_ORD_MAX_FILLS_OPT' => $request->mail_ord_max_fills_opt,
                //         'MIN_PRICE_OPT' => $request->min_price_opt,
                //         'MAX_PRICE_OPT' => $request->macx_price_opt,
                //         'VALID_RELATION_CODE' => $request->valid_relation_code,
                //         'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                //         'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                //         'DRUG_COV_START_DAYS' => $request->drug_cov_start_days,
                //         'PKG_DETERMINE_ID' => $request->pkg_determine_id,
                //         'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                //         'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                //         'MAX_DAYS_OVER_TIME' => $request->max_days_over_time,
                //         'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                //         'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                //         'MAX_QTY_PER_FILL' => $request->max_qty_per_fill,
                //         'BNG_SNGL_INC_EXC_IND' => $request->bng_sngl_inc_exc_ind,
                //         'BNG_MULTI_INC_EXC_IND' => $request->bng_multi_inc_exc_ind,
                //         'BGA_INC_EXC_IND' => $request->bga_inc_exc_ind,
                //         'GEN_INC_EXC_IND' => $request->gen_inc_exc_ind,
                //         'MESSAGE_STOP_DATE' => $request->message_stop_date,
                //     ]
                //     );
                //     $update = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')->where('plan_id', 'like', '%' . $request->plan_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update_names);
                }
    
               

            }

           
        }
    }

    public function getDetailsList($id)
    {
        $data_list = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
            ->join('PLAN_DRUG_CATGY_EXCEPTIONS','DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST','=','PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST')
            // ->join('FE_SYSTEM_CATEGORIES', 'FE_SYSTEM_CATEGORIES.STYPE', '=', 'PLAN_DRUG_CATGY_EXCEPTIONS.SCATEGORY')
            ->where('DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST', $id)
            ->get();
        return $this->respondWithToken($this->token(), '', $data_list);
    }
    public function drugclassDelete(Request $request)
    {
        if (isset($request->drug_catgy_exception_list) && ($request->scategory)) {
            $all_exceptions_lists =  DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->where('DRUG_CATGY_EXCEPTION_LIST', $request->drug_catgy_exception_list)
                ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->plan_id)) {

            $exception_delete =  DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                ->where('PLAN_ID', $request->plan_id)
                ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}