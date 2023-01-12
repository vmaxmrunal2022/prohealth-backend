<?php

namespace App\Http\Controllers\plan_design;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanEditController extends Controller
{
    public function get(Request $request)
    {
        $planEdit = DB::table('PLAN_BENEFIT_TABLE')
                    ->where('PLAN_ID', 'like', '%'. strtoupper($request->search) .'%')
                    ->orWhere('PLAN_NAME', 'like', '%'. strtoupper($request->search) .'%')
                    ->get();
        return $this->respondWithToken($this->token(), '', $planEdit);
    }

    public function getPlanEditData(Request $request)
    {
        $plandata = DB::table('plan_benefit_table')
                    ->leftJoin('PLAN_VALIDATION_LISTS', 'plan_benefit_table.plan_id', '=', 'plan_benefit_table.plan_id')
                    // ->leftJoin('PLAN_RX_NETWORK_RULES', 'plan_benefit_table.plan_id', '=', 'PLAN_RX_NETWORK_RULES.plan_id')
                    // ->leftJoin('PLAN_RX_NETWORKS', 'plan_benefit_table.plan_id', '=', 'PLAN_RX_NETWORKS.plan_id')
                    ->where('plan_benefit_table.plan_id', 'like', '%'.$request->search.'%')
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
