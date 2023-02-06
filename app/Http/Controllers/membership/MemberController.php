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
            ->select(
                'MEMBER.CUSTOMER_ID',
                'MEMBER.MEMBER_ID',
                'MEMBER.MEMBER_FIRST_NAME',
                'MEMBER.MEMBER_LAST_NAME',
                'MEMBER.EFFECTIVE_DATE_OVERRIDE',
                'MEMBER.ELIG_VALIDATION_ID',
                'MEMBER.ELIGIBILITY_OVRD',
                'MEMBER.ELIG_LOCK_DATE',
                'MEMBER.LOAD_PROCESS_DATE',
                'MEMBER.PRIM_COVERAGE_INS_CARRIER',
                'MEMBER.ADDRESS_1',
                'MEMBER.ADDRESS_2',
                'MEMBER.CITY',
                'MEMBER.COUNTRY',
                'MEMBER.DATE_OF_BIRTH',
                'MEMBER.RELATIONSHIP',
                'MEMBER.ANNIV_DATE',
                'MEMBER.PATIENT_PIN_NUMBER',
                'MEMBER.ALT_MEMBER_ID',
                'MEMBER.SEX_OF_PATIENT',
                'MEMBER.COPAY_SCHED_OVR_FLAG',
                'MEMBER.COPAY_SCHED_OVR',
                'MEMBER.ACCUM_BENE_OVR_FLAG',
                'MEMBER.ACCUM_BENE_PLAN_OVR',
                'MEMBER.ACCUM_BENE_EFF_DATE_1',
                'MEMBER.ACCUM_BENE_TERM_DATE_1',
                'MEMBER.ACCUM_BENE_EFF_DATE_2',
                'MEMBER.ACCUM_BENE_TERM_DATE_2',
                'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_1',
                'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_2',
                'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_3',
                'MEMBER.ACCUM_ADJMNT_PLAN_PAID_AMT',
                'MEMBER.ACCUM_ADJMNT_PLAN_PAID_AMT_2',
                'MEMBER.ACCUM_ADJMNT_MBR_PAID_AMT',
                'MEMBER.ACCUM_ADJMNT_MBR_PAID_AMT_2',
                'MEMBER.PRIMARY_PRESCRIBER',
                'MEMBER.RX_NETWORK_ID',
                'MEMBER.MISC_GROUPING_1',
                'MEMBER.MISC_GROUPING_2',
                'MEMBER.USER_DEFINED_CODE_1',
                'MEMBER.USER_DEFINED_CODE_2',
                'MEMBER.MISC_ID',

                'CUSTOMER.CUSTOMER_ID as cust_cust_id',
                'CUSTOMER.CUSTOMER_NAME',
                'CUSTOMER.EFFECTIVE_DATE as cust_eff_date',
                'CUSTOMER.TERMINATION_DATE as cust_term_date',

                'CLIENT.CUSTOMER_ID as client_cust_id',
                'CLIENT.CLIENT_ID',
                'CLIENT.CLIENT_NAME',
                'CLIENT.EFFECTIVE_DATE as client_eff_date',
                'CLIENT.TERMINATION_DATE as client_term_date',

                'CLIENT_GROUP.CUSTOMER_ID as client_group_cust_id',
                'CLIENT_GROUP.GROUP_NAME',
                'CLIENT_GROUP.EFFECTIVE_DATE as client_group_eff_date',
                'CLIENT_GROUP.GROUP_TERMINATION_DATE as client_group_term_date',
                'CLIENT_GROUP.CLIENT_GROUP_ID',
                'MEMBER_COVERAGE.CUSTOMER_ID as mem_cov_cust_id',
                'MEMBER_COVERAGE.EFFECTIVE_DATE',
                'MEMBER_COVERAGE.TERMINATION_DATE',
                'MEMBER_COVERAGE.PLAN_ID',
                'MEMBER_COVERAGE.COPAY_STRATEGY_ID',
                'MEMBER_COVERAGE.ACCUM_BENEFIT_STRATEGY_ID',
                'MEMBER_COVERAGE.PRICING_STRATEGY_ID',
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
            ->where('MEMBER_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->limit(100)
            ->get();

        return $this->respondWithToken($this->token(), '', $coverageHistory);
    }

    public function getHealthCondition(Request $request)
    {
        $healthCondition = DB::table('member_diagnosis')
            ->join('DIAGNOSIS_CODES', 'member_diagnosis.diagnosis_id', '=', 'DIAGNOSIS_CODES.diagnosis_id')
            ->where('member_diagnosis.member_id', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $healthCondition);
    }

    public function getDiagnosisHistory(Request $request)
    {
        $diagnosisHistor = DB::table('MEMBER_DIAGNOSIS_HISTORY')
            ->where('diagnosis_id', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $diagnosisHistor);
    }

    public function getPriorAuthorization(Request $request)
    {
        $priorAuthorization = DB::table('PRIOR_AUTHORIZATIONS')
            ->where('member_id', 'like', '%' . strtoupper($request->member_id) . '%')
            ->where('client_id', 'like', '%' . strtoupper($request->client_id) . '%')
            ->where('client_group_id', 'like', '%' . strtoupper($request->client_group_id) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $priorAuthorization);
    }

    public function getLogChangeData(Request $request)
    {
        $logChange = DB::table('MEMBER_CHANGE_LOG')
            ->where('member_id', 'like', '%' . strtoupper($request->member_id) . '%')
            ->where('client_id', 'like', '%' . strtoupper($request->client_id) . '%')
            ->where('client_group_id', 'like', '%' . strtoupper($request->client_group_id) . '%')
            ->where('customer_id', 'like', '%' . strtoupper($request->customer_id) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $logChange);
    }



    //Eligibility
    public function getEligibility(Request $request)
    {
        $eligibility = [
            ['eligibility_id' => '1', 'eligibility_name' => 'Member only (Individual)'],
            ['eligibility_id' => '2', 'eligibility_name' => 'Member spouse'],
            ['eligibility_id' => '3', 'eligibility_name' => 'Member children only'],
            ['eligibility_id' => '4', 'eligibility_name' => 'Member family'],
            ['eligibility_id' => '5', 'eligibility_name' => 'Dependent children'],
            ['eligibility_id' => '6', 'eligibility_name' => 'Disabled parents'],
            ['eligibility_id' => '7', 'eligibility_name' => 'Spouse only'],
            ['eligibility_id' => '8', 'eligibility_name' => 'Spouse & childern'],
        ];
        return $this->respondWithToken($this->token(), '', $eligibility);
    }

    //Member Status
    public function getMemberStatus(Request $request)
    {
        $memberStatus = [
            ['mem_status_id' => 'A', 'mem_status_name' => 'Active', 'mem_dec' => 'Determined by effective dates'],
            ['mem_status_id' => 'P', 'mem_status_name' => 'Pending', 'mem_dec' => 'Determined by effective dates'],
            ['mem_status_id' => 'T', 'mem_status_name' => 'Termed', 'mem_dec' => 'Determined by effective dates'],
            ['mem_status_id' => 'O', 'mem_status_name' => 'Other Coverge', 'mem_dec' => 'Other coverge indicated'],
            ['mem_status_id' => 'X', 'mem_status_name' => 'Max Benifit Met', 'mem_dec' => 'Major medical met - 100% copay'],
            ['mem_status_id' => 'I', 'mem_status_name' => 'Inetrn', 'mem_dec' => 'Temporary Eligibility'],
        ];
        return $this->respondWithToken($this->token(), '', $memberStatus);
    }

    //Realtionship
    public function getMemberRelationship(Request $request)
    {
        $memRelationship  = [
            ['relationship_id' => '1', 'relationship_name' => 'Cardholder'],
            ['relationship_id' => '2', 'relationship_name' => 'Spouse'],
            ['relationship_id' => '3', 'relationship_name' => 'Child'],
            ['relationship_id' => '4', 'relationship_name' => 'Other'],
            ['relationship_id' => '5', 'relationship_name' => 'Student'],
            ['relationship_id' => '6', 'relationship_name' => 'Disable'],
            ['relationship_id' => '7', 'relationship_name' => 'Adult Dependent'],
            ['relationship_id' => '8', 'relationship_name' => 'Sigificant Other'],
        ];

        return $this->respondWithToken($this->token(), '', $memRelationship);
    }

    // Copay Schedule Override
    public function getCopayScheduleOverride(Request $request)
    {
        $copayScheduleOverride = [
            ['copay_schedule_ovrr_id' => 'D', 'copay_schedule_ovrr_name' => 'Override default copay schedule'],
            ['copay_schedule_ovrr_id' => 'A', 'copay_schedule_ovrr_name' => 'Override all copay schedules except accumulated benefits'],
        ];

        return $this->respondWithToken($this->token(), '', $copayScheduleOverride);
    }

    //Accumulated benifit overrides
    public function getAccumulatedBenifitOverride(Request $request)
    {
        $accumulatedBenifitOvrr = [
            ['acc_beni_ovrr_id' => 'N', 'acc_beni_ovrr_id' => 'Override the plan and provide no accumulated benefits for this member'],
            ['acc_beni_ovrr_id' => 'C', 'acc_beni_ovrr_id' => 'Change the accumulated benifits to the accumulated benifit plan overrides'],
            ['acc_beni_ovrr_id' => 'A', 'acc_beni_ovrr_id' => ''],
            ['acc_beni_ovrr_id' => 'P', 'acc_beni_ovrr_id' => ''],
        ];

        return $this->respondWithToken($this->token(), '', $accumulatedBenifitOvrr);
    }

    public function getCopayStrategyId(Request $request)
    {
        $copay_strategy_id = DB::table('COPAY_STRATEGY')
            ->get();
        return $this->respondWithToken($this->token(), '', $copay_strategy_id);
    }

    public function getAccumulatedBenifitStrategy(Request $request)
    {
        $acc_beni_strategy = DB::table('ACCUM_BENEFIT_STRATEGY')
            ->get();

        return $this->respondWithToken($this->token(), '', $acc_beni_strategy);
    }

    public function getPricingStrategy(Request $request)
    {
        $pricing_strategy = DB::table('PRICING_STRATEGY')
            ->get();

        return $this->respondWithToken($this->token(), '', $pricing_strategy);
    }

    public function getViewLimitations(Request $request)
    {
        $view_limitations = [
            ['limit_id' => '1', 'limit_name' => 'All Claims'],
            ['limit_id' => '2', 'limit_name' => 'Paid Claims'],
            ['limit_id' => '3', 'limit_name' => 'Rejected Claims'],
            ['limit_id' => '4', 'limit_name' => 'Reversed Claim'],
        ];

        return $this->respondWithToken($this->token(), '', $view_limitations);
    }

    //Coverage Information Table
    public function getCoverageInformationTable(Request $request)
    {
        $coverageInformationTable = DB::table('MEMBER_COVERAGE')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->get();
        return $this->respondWithToken($this->token(), '', $coverageInformationTable);
    }

    // Health Condition -> Diagnosis
    public function getDiagnosisTable(Request $request)
    {
        $diagnosisTable = DB::table('MEMBER_DIAGNOSIS')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->get();
        return $this->respondWithToken($this->token(), '', $diagnosisTable);
    }

    // Health Condition -> Diagnosis Details
    public function getDiagnosisDetailsTable(Request $request)
    {
        $diagnosisDetailsTable = DB::table('MEMBER_DIAGNOSIS_HISTORY')
            ->where('diagnosis_id', $request->diagnosis_id)
            ->get();
        return $this->respondWithToken($this->token(), '', $diagnosisDetailsTable);
    }

    //Claim History Table
    public function getClaimHistoryTable(Request $request)
    {
        $claim_history_table = DB::table('RX_TRANSACTION_LOG')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->get();

        return $this->respondWithToken($this->token(), '', $claim_history_table);
        // return $claim_history_table;
    }

    //Prior Auth Table
    public function getPriorAuthTable(Request $request)
    {
        $prior_auth = DB::table('PRIOR_AUTHORIZATIONS')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->get();

        return $this->respondWithToken($this->token(), '', $prior_auth);
    }

    //Provider Search Table
    public function getProviderSearch(Request $request)
    {
    }

    //Change Log Table 
    public function getChangeLogTable(Request $request)
    {
        $change_log = DB::table('MEMBER_CHANGE_LOG')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->get();

        return $this->respondWithToken($this->token(), '', $change_log);
    }

    public function submitMemberForm(Request $request)
    {
        if ($request->add_new) {
            $add_member = DB::table('member')
                ->insert([
                    'customer_id' => $request->customer_id,
                    'client_id' => $request->client_id,
                    'client_group_id' => $request->client_group_id,
                    'member_id' => $request->member_id,
                    'eligibility_ovrd' => $request->eligibility_ovrd,
                    'status' => $request->status,
                    'elig_lock_date' => $request->elig_lock_date,
                    'load_process_date' => $request->load_process_date,
                    'prim_coverage_ins_carrier' => $request->prim_coverage_ins_carrier,
                    'member_first_name' => $request->member_first_name,
                    'member_last_name' => $request->member_last_name,
                    'address_1' => $request->address_1,
                    'address_2' => $request->address_2,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                    'date_of_birth' => $request->date_of_birth,
                    'relationship' => $request->relationship,
                    'anniv_date' => $request->anniv_date,
                    'patient_id_number' => $request->patient_id_number,
                    'alt_member_id' => $request->alt_member_id,
                    'sex_of_patient' => $request->sex_of_patient,
                ]);
        }
    }
}
