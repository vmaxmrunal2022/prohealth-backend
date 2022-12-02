<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use DB;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function get(Request $request)
    {
        // $member = DB::table('MEMBER')
        //     ->where('CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
        //     ->orWhere('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')
        //     ->orWhere('MEMBER_LAST_NAME', 'like', '%' . strtoupper($request->search) . '%')
        //     ->orWhere('MEMBER_FIRST_NAME', 'like', '%' . strtoupper($request->search) . '%')
        //     ->orWhere('DATE_OF_BIRTH', 'like', '%' . strtoupper($request->search) . '%')
        //     // TODO -> data count is 66470rows and throwing error to process
        //     ->limit(100)
        //     ->get();

        $member = DB::table('MEMBER')
            ->select('MEMBER.CUSTOMER_ID', 'MEMBER.MEMBER_ID', 'MEMBER.MEMBER_FIRST_NAME', 'MEMBER.MEMBER_LAST_NAME', 'MEMBER.EFFECTIVE_DATE_OVERRIDE',
                      'MEMBER.ELIG_VALIDATION_ID', 'MEMBER.ELIGIBILITY_OVRD', 'MEMBER.ELIG_LOCK_DATE', 'MEMBER.LOAD_PROCESS_DATE', 'MEMBER.PRIM_COVERAGE_INS_CARRIER', 'MEMBER.ADDRESS_1', 'MEMBER.ADDRESS_2', 'MEMBER.CITY', 'MEMBER.COUNTRY', 'MEMBER.DATE_OF_BIRTH', 'MEMBER.RELATIONSHIP', 'MEMBER.ANNIV_DATE', 'MEMBER.PATIENT_PIN_NUMBER', 'MEMBER.ALT_MEMBER_ID', 'MEMBER.SEX_OF_PATIENT', 'MEMBER.COPAY_SCHED_OVR_FLAG', 'MEMBER.COPAY_SCHED_OVR',
                      'MEMBER.ACCUM_BENE_OVR_FLAG', 'MEMBER.ACCUM_BENE_PLAN_OVR', 'MEMBER.ACCUM_BENE_EFF_DATE_1', 'MEMBER.ACCUM_BENE_TERM_DATE_1', 'MEMBER.ACCUM_BENE_EFF_DATE_2', 'MEMBER.ACCUM_BENE_TERM_DATE_2', 'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_1', 'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_2', 'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_3', 'MEMBER.ACCUM_ADJMNT_PLAN_PAID_AMT', 'MEMBER.ACCUM_ADJMNT_PLAN_PAID_AMT_2', 'MEMBER.ACCUM_ADJMNT_MBR_PAID_AMT', 'MEMBER.ACCUM_ADJMNT_MBR_PAID_AMT_2', 'MEMBER.PRIMARY_PRESCRIBER', 'MEMBER.RX_NETWORK_ID', 'MEMBER.MISC_GROUPING_1', 'MEMBER.MISC_GROUPING_2',
                      'MEMBER.USER_DEFINED_CODE_1', 'MEMBER.USER_DEFINED_CODE_2', 'MEMBER.MISC_ID',

                      'CUSTOMER.CUSTOMER_ID as cust_cust_id', 'CUSTOMER.CUSTOMER_NAME', 'CUSTOMER.EFFECTIVE_DATE as cust_eff_date', 'CUSTOMER.TERMINATION_DATE as cust_term_date',

                      'CLIENT.CUSTOMER_ID as client_cust_id', 'CLIENT.CLIENT_ID', 'CLIENT.CLIENT_NAME', 'CLIENT.EFFECTIVE_DATE as client_eff_date', 'CLIENT.TERMINATION_DATE as client_term_date',

                      'CLIENT_GROUP.CUSTOMER_ID as client_group_cust_id', 'CLIENT_GROUP.GROUP_NAME', 'CLIENT_GROUP.EFFECTIVE_DATE as client_group_eff_date', 'CLIENT_GROUP.GROUP_TERMINATION_DATE as client_group_term_date', 'CLIENT_GROUP.CLIENT_GROUP_ID',
                      'MEMBER_COVERAGE.CUSTOMER_ID as mem_cov_cust_id', 'MEMBER_COVERAGE.EFFECTIVE_DATE', 'MEMBER_COVERAGE.TERMINATION_DATE', 'MEMBER_COVERAGE.PLAN_ID', 
                      'MEMBER_COVERAGE.COPAY_STRATEGY_ID', 'MEMBER_COVERAGE.ACCUM_BENEFIT_STRATEGY_ID', 'MEMBER_COVERAGE.PRICING_STRATEGY_ID', 
                      )
            ->join('CUSTOMER', 'MEMBER.CUSTOMER_ID', '=', 'CUSTOMER.CUSTOMER_ID')
            ->join('CLIENT', 'MEMBER.CUSTOMER_ID', '=', 'CLIENT.CUSTOMER_ID')
            ->join('CLIENT_GROUP', 'MEMBER.CUSTOMER_ID', '=', 'CLIENT_GROUP.CUSTOMER_ID')
            ->join('MEMBER_COVERAGE', 'MEMBER.CUSTOMER_ID', '=', 'MEMBER_COVERAGE.CUSTOMER_ID')
            ->where('MEMBER.CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('MEMBER.CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('MEMBER.MEMBER_LAST_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('MEMBER.MEMBER_FIRST_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('MEMBER.DATE_OF_BIRTH', 'like', '%' . strtoupper($request->search) . '%')
            // TODO -> data count is 66470rows and throwing error to process
            ->limit(100)
            ->get();

        return $this->respondWithToken($this->token(), '', $member);
    }

    public function getCoverageHistory(Request $request)
    {
        // $coverageHistory = DB::table('MEMBER')
        //                    ->join('MEMBER_COVERAGE', 'MEMBER.MEMBER_ID', '=', 'MEMBER_COVERAGE.MEMBER_ID')
        //                    ->where('MEMBER_COVERAGE.MEMBER_ID', 'like', '%'. strtoupper($request->search) .'%')
        //                    ->limit(100)
        //                    ->get();

        $coverageHistory = DB::table('MEMBER_COVERAGE')
                           ->where('MEMBER_ID', 'like', '%'. strtoupper($request->search) .'%')
                           ->limit(100)
                           ->get();

        return $this->respondWithToken($this->token(), '', $coverageHistory);
    }

    public function getHealthCondition(Request $request)
    {
        $healthCondition = DB::table('member_diagnosis')
                           ->join('DIAGNOSIS_CODES','member_diagnosis.diagnosis_id', '=', 'DIAGNOSIS_CODES.diagnosis_id')
                           ->where('member_diagnosis.member_id', 'like', '%'. strtoupper($request->search) .'%')
                           ->get();

        return $this->respondWithToken($this->token(), '', $healthCondition);
    }

    public function getDiagnosisHistory(Request $request)
    {
        $diagnosisHistor = DB::table('MEMBER_DIAGNOSIS_HISTORY')
                           ->where('diagnosis_id', 'like', '%'. strtoupper($request->search) .'%')
                           ->get();

        return $this->respondWithToken($this->token(), '', $diagnosisHistor);
    }

    public function getPriorAuthorization(Request $request)
    {
        $priorAuthorization = DB::table('PRIOR_AUTHORIZATIONS')
                              ->where('member_id', 'like', '%'. strtoupper($request->member_id) .'%')
                              ->where('client_id', 'like', '%'. strtoupper($request->client_id) .'%')
                              ->where('client_group_id', 'like', '%'. strtoupper($request->client_group_id) .'%')
                              ->get();

        return $this->respondWithToken($this->token(), '', $priorAuthorization);
    }

    public function getLogChangeData(Request $request)
    {
        $logChange = DB::table('MEMBER_CHANGE_LOG')
                              ->where('member_id', 'like', '%'. strtoupper($request->member_id) .'%')
                              ->where('client_id', 'like', '%'. strtoupper($request->client_id) .'%')
                              ->where('client_group_id', 'like', '%'. strtoupper($request->client_group_id) .'%')
                              ->where('customer_id', 'like', '%'. strtoupper($request->customer_id) .'%')
                              ->get();

        return $this->respondWithToken($this->token(), '', $logChange);
    }
}
