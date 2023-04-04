<?php

namespace App\Http\Controllers\plan_design;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanEditController extends Controller
{

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
        dd($request->plan_id);

        $getData = DB::table('PLAN_BENEFIT_TABLE')
            ->where('PLAN_ID', strtoupper($request->plan_id))
            // ->Where('LABEL_NAME',strtoupper($request->label_name))
            // ->Where('GENERIC_NAME',strtoupper($request->generic_name))
            // ->Where('PACKAGE_SIZE',strtoupper($request->package_size))
            ->first();

        if ($request->add_new == 1) {

            if ($getData) {

                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getData);
            } else {


                $addData = DB::table('PLAN_BENEFIT_TABLE')
                    ->insert([
                        'PLAN_ID' => strtoupper($request->plan_id),
                        'EFFECTIVE_DATE' => strtotime($request->effective_date),
                        'PLAN_NAME' => strtoupper($request->plan_name),
                        'DEFAULT_DRUG_STATUS' => strtoupper($request->default_drug_status),
                        'DEFAULT_PRICE_SCHEDULE' => strtoupper($request->default_price_schedule),
                        'MAC_LIST' => strtoupper($request->mac_list),
                        'PHARMACY_EXCEPTIONS_FLAG' => strtoupper($request->pharmacy_exceptions_flag),
                        'PRESCRIBER_EXCEPTIONS_FLAG' => strtoupper($request->prescriber_exceptions_flag),
                        'DRUG_CATGY_EXCPT_FLAG' => strtoupper($request->drug_catgy_excpt_flag),
                        'NDC_EXCEPTION_LIST' => strtoupper($request->ndc_exception_list),
                        'GPI_EXCEPTION_LIST' => strtoupper($request->gpi_exception_list),
                        'THER_CLASS_EXCEPTION_LIST' => strtoupper($request->ther_class_exception_list),
                        'TERMINATION_DATE' => strtoupper($request->terminate_date),
                        'MIN_RX_QTY' => strtoupper($request->min_rx_qty),
                        'MAX_RX_QTY' => strtoupper($request->max_rx_qty),
                        'MIN_RX_DAYS' => strtoupper($request->min_rx_days),
                        'MAX_RX_DAYS' => strtoupper($request->max_rx_days),
                        'MIN_CTL_DAYS' => strtoupper($request->min_ctl_days),
                        'MAX_CTL_DAYS' => strtoupper($request->max_ctl_days),
                        'MAX_REFILLS' => $request->max_refills,
                        'MAX_DAYS_PER_FILL' => strtoupper($request->max_days_per_fill),
                        'MAX_DOSE' => strtoupper($request->max_dose),
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
                        'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_test,
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
                        // 'PROCEDURE_XREF_ID'=>$request->procedure_xref_id,

                    ]);



                if ($addData) {
                    return $this->respondWithToken($this->token(), 'Added Successfully!!!', $addData);
                }
            }
        } else if ($request->add_new == 0) { {
                $updateData = DB::table('PLAN_BENEFIT_TABLE')
                    ->where('PLAN_ID', $request->plan_id)
                    ->update([

                        'EFFECTIVE_DATE' => strtotime($request->effective_date),
                        'PLAN_NAME' => strtoupper($request->plan_name),
                        'DEFAULT_DRUG_STATUS' => strtoupper($request->default_drug_status),
                        'DEFAULT_PRICE_SCHEDULE' => strtoupper($request->default_price_schedule),
                        'MAC_LIST' => strtoupper($request->mac_list),
                        'PHARMACY_EXCEPTIONS_FLAG' => strtoupper($request->pharmacy_exceptions_flag),
                        'PRESCRIBER_EXCEPTIONS_FLAG' => strtoupper($request->prescriber_exceptions_flag),
                        'DRUG_CATGY_EXCPT_FLAG' => strtoupper($request->drug_catgy_excpt_flag),
                        'NDC_EXCEPTION_LIST' => strtoupper($request->ndc_exception_list),
                        'GPI_EXCEPTION_LIST' => strtoupper($request->gpi_exception_list),
                        'THER_CLASS_EXCEPTION_LIST' => strtoupper($request->ther_class_exception_list),
                        'TERMINATION_DATE' => strtoupper($request->terminate_date),
                        'MIN_RX_QTY' => strtoupper($request->min_rx_qty),
                        'MAX_RX_QTY' => strtoupper($request->max_rx_qty),
                        'MIN_RX_DAYS' => strtoupper($request->min_rx_days),
                        'MAX_RX_DAYS' => strtoupper($request->max_rx_days),
                        'MIN_CTL_DAYS' => strtoupper($request->min_ctl_days),
                        'MAX_CTL_DAYS' => strtoupper($request->max_ctl_days),
                        'MAX_REFILLS' => $request->max_refills,
                        'MAX_DAYS_PER_FILL' => strtoupper($request->max_days_per_fill),
                        'MAX_DOSE' => strtoupper($request->max_dose),
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
                        'DRUG_CATGY_EXCEPTION_LIST' => $request->drug_catgy_exception_test,
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

                    ]);





                if ($updateData) {
                    return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateData);
                }
            }
        }
    }


    public function get(Request $request)
    {
        // dd('test');
        $planEdit = DB::table('PLAN_BENEFIT_TABLE')
            ->where('PLAN_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PLAN_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();
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
}
