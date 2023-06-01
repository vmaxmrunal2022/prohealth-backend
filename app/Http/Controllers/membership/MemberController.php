<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
// use DB;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{

    use AuditTrait;
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
                //     'MEMBER_COVERAGE.CUSTOMER_ID as mem_cov_cust_id',
                //     'MEMBER_COVERAGE.EFFECTIVE_DATE',
                //     'MEMBER_COVERAGE.TERMINATION_DATE',
                //     'MEMBER_COVERAGE.PLAN_ID',
                //     'MEMBER_COVERAGE.COPAY_STRATEGY_ID',
                //     'MEMBER_COVERAGE.ACCUM_BENEFIT_STRATEGY_ID',
                //     'MEMBER_COVERAGE.PRICING_STRATEGY_ID',
            )
            ->join('CUSTOMER', 'MEMBER.CUSTOMER_ID', '=', 'CUSTOMER.CUSTOMER_ID')
            ->join('CLIENT', 'CLIENT.CLIENT_ID', '=', 'MEMBER.CLIENT_ID')
            ->join('CLIENT_GROUP', 'MEMBER.CLIENT_GROUP_ID', '=', 'CLIENT_GROUP.CLIENT_GROUP_ID')
            // ->join('MEMBER_COVERAGE', 'MEMBER.CUSTOMER_ID', '=', 'MEMBER_COVERAGE.CUSTOMER_ID')
            // ->where('MEMBER.CUSTOMER_ID', 'like', '%' . $request->search. '%')
            ->orWhere('MEMBER.MEMBER_ID', 'like', '%' . $request->search . '%')
            // ->orWhere('MEMBER.CLIENT_ID', 'like', '%' . $request->search. '%')
            // ->orWhere('MEMBER.MEMBER_LAST_NAME', 'like', '%' . $request->search . '%')
            // ->orWhere('MEMBER.MEMBER_FIRST_NAME', 'like', '%' .$request->search. '%')
            // ->orWhere('MEMBER.DATE_OF_BIRTH', 'like', '%' . $request->search. '%')
            // TODO -> data count is 66470rows and throwing error to process
            ->limit(100)
            ->get();

        return $this->respondWithToken($this->token(), '', $member);
    }
    // public function get1(Request $request)
    // {

    //     $member = DB::table('MEMBER')
    //         ->select(
    //             'MEMBER.CUSTOMER_ID',
    //             'MEMBER.MEMBER_ID',
    //             'MEMBER.MEMBER_FIRST_NAME',
    //             'MEMBER.MEMBER_LAST_NAME',
    //             'MEMBER.EFFECTIVE_DATE_OVERRIDE',
    //             'MEMBER.ELIG_VALIDATION_ID',
    //             'MEMBER.ELIGIBILITY_OVRD',
    //             'MEMBER.ELIG_LOCK_DATE',
    //             'MEMBER.LOAD_PROCESS_DATE',
    //             'MEMBER.PRIM_COVERAGE_INS_CARRIER',
    //             'MEMBER.ADDRESS_1',
    //             'MEMBER.ADDRESS_2',
    //             'MEMBER.CITY',
    //             'MEMBER.COUNTRY',
    //             'MEMBER.DATE_OF_BIRTH',
    //             'MEMBER.RELATIONSHIP',
    //             'MEMBER.ANNIV_DATE',
    //             'MEMBER.PATIENT_PIN_NUMBER',
    //             'MEMBER.ALT_MEMBER_ID',
    //             'MEMBER.SEX_OF_PATIENT',
    //             'MEMBER.COPAY_SCHED_OVR_FLAG',
    //             'MEMBER.COPAY_SCHED_OVR',
    //             'MEMBER.ACCUM_BENE_OVR_FLAG',
    //             'MEMBER.ACCUM_BENE_PLAN_OVR',
    //             'MEMBER.ACCUM_BENE_EFF_DATE_1',
    //             'MEMBER.ACCUM_BENE_TERM_DATE_1',
    //             'MEMBER.ACCUM_BENE_EFF_DATE_2',
    //             'MEMBER.ACCUM_BENE_TERM_DATE_2',
    //             'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_1',
    //             'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_2',
    //             'MEMBER.ACCUM_ADJMNT_MBR_PAID_MOP_3',
    //             'MEMBER.ACCUM_ADJMNT_PLAN_PAID_AMT',
    //             'MEMBER.ACCUM_ADJMNT_PLAN_PAID_AMT_2',
    //             'MEMBER.ACCUM_ADJMNT_MBR_PAID_AMT',
    //             'MEMBER.ACCUM_ADJMNT_MBR_PAID_AMT_2',
    //             'MEMBER.PRIMARY_PRESCRIBER',
    //             'MEMBER.RX_NETWORK_ID',
    //             'MEMBER.MISC_GROUPING_1',
    //             'MEMBER.MISC_GROUPING_2',
    //             'MEMBER.USER_DEFINED_CODE_1',
    //             'MEMBER.USER_DEFINED_CODE_2',
    //             'MEMBER.MISC_ID',

    //             'CUSTOMER.CUSTOMER_ID as cust_cust_id',
    //             'CUSTOMER.CUSTOMER_NAME',
    //             'CUSTOMER.EFFECTIVE_DATE as cust_eff_date',
    //             'CUSTOMER.TERMINATION_DATE as cust_term_date',

    //             'CLIENT.CUSTOMER_ID as client_cust_id',
    //             'CLIENT.CLIENT_ID',
    //             'CLIENT.CLIENT_NAME',
    //             'CLIENT.EFFECTIVE_DATE as client_eff_date',
    //             'CLIENT.TERMINATION_DATE as client_term_date',

    //             'CLIENT_GROUP.CUSTOMER_ID as client_group_cust_id',
    //             'CLIENT_GROUP.GROUP_NAME',
    //             'CLIENT_GROUP.EFFECTIVE_DATE as client_group_eff_date',
    //             'CLIENT_GROUP.GROUP_TERMINATION_DATE as client_group_term_date',
    //             'CLIENT_GROUP.CLIENT_GROUP_ID',
    //             'MEMBER_COVERAGE.CUSTOMER_ID as mem_cov_cust_id',
    //             'MEMBER_COVERAGE.EFFECTIVE_DATE',
    //             'MEMBER_COVERAGE.TERMINATION_DATE',
    //             'MEMBER_COVERAGE.PLAN_ID',
    //             'MEMBER_COVERAGE.COPAY_STRATEGY_ID',
    //             'MEMBER_COVERAGE.ACCUM_BENEFIT_STRATEGY_ID',
    //             'MEMBER_COVERAGE.PRICING_STRATEGY_ID',
    //         )
    //         ->join('CUSTOMER', 'MEMBER.CUSTOMER_ID', '=', 'CUSTOMER.CUSTOMER_ID')
    //         ->join('CLIENT', 'MEMBER.CUSTOMER_ID', '=', 'CLIENT.CUSTOMER_ID')
    //         ->join('CLIENT_GROUP', 'MEMBER.CUSTOMER_ID', '=', 'CLIENT_GROUP.CUSTOMER_ID')
    //         ->join('MEMBER_COVERAGE', 'MEMBER.CUSTOMER_ID', '=', 'MEMBER_COVERAGE.CUSTOMER_ID')
    //         ->where('MEMBER.CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
    //         ->orWhere('MEMBER.CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')
    //         ->orWhere('MEMBER.MEMBER_LAST_NAME', 'like', '%' . strtoupper($request->search) . '%')
    //         ->orWhere('MEMBER.MEMBER_FIRST_NAME', 'like', '%' . strtoupper($request->search) . '%')
    //         ->orWhere('MEMBER.DATE_OF_BIRTH', 'like', '%' . strtoupper($request->search) . '%')
    //         // TODO -> data count is 66470rows and throwing error to process
    //         // ->limit(100)
    //         ->get();

    //     return $this->respondWithToken($this->token(), '', $member);
    // }




    public function getCoverageHistory(Request $request)
    {
        $coverage_history = DB::table('MEMBER_HIST')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->where('member_id', $request->member_id)
            ->get();


            // dd(count($coverage_history));

            return $this->respondWithToken($this->token(), '', $coverage_history);

        
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


    public function getMembersDropDownList()
    {

        $member_data_list = DB::table('MEMBER')
            ->select('member_id')->get();

        // dd($member_data_list);

        return $this->respondWithToken($this->token(), '', $member_data_list);
    }

    //Member
    public function getMember(Request $request)
    {
        $memberIds = DB::table('member')
            ->where('member_id', 'like', '%' . $request->search . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $memberIds);
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

    //Relationship
    public function getMemberRelationship(Request $request)
    {
        $memRelationship = [
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
            ['acc_beni_ovrr_id' => 'N', 'name' => 'Override the plan and provide no accumulated benefits for this member'],
            ['acc_beni_ovrr_id' => 'C', 'name' => 'Change the accumulated benifits to the accumulated benifit plan override'],
            ['acc_beni_ovrr_id' => 'A', 'name' => 'Adjust the amount applied towards the member`s limit with a specified amount'],
            ['acc_beni_ovrr_id' => 'P', 'name' => 'No Override'],
        ];

        return $this->respondWithToken($this->token(), '', $accumulatedBenifitOvrr);
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

    public function getCoverageInformationTable(Request $request)
    {
        $coverageInformationTable = DB::table('MEMBER_COVERAGE')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->where('member_id', $request->member_id)
            ->get();

        if ($coverageInformationTable) {
            return $this->respondWithToken($this->token(), '', $coverageInformationTable);

        } else {

            return $this->respondWithToken($this->token(), 'No Data Found');

        }
    }

    public function getDiagnosisTable(Request $request)
    {
        $diagnosisTable = DB::table('MEMBER_DIAGNOSIS')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->get();
        return $this->respondWithToken($this->token(), '', $diagnosisTable);
    }

    public function getDiagnosisDetailsTable(Request $request)
    {
        $diagnosisDetailsTable = DB::table('MEMBER_DIAGNOSIS')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->where('member_id', $request->member_id)->get();
        return $this->respondWithToken($this->token(), '', $diagnosisDetailsTable);
    }


    public function getPriorAuthTable(Request $request)
    {
        $prior_auth = DB::table('PRIOR_AUTHORIZATIONS')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->get();

        return $this->respondWithToken($this->token(), '', $prior_auth);
    }

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
        // $coverage_effective_date = strtotime('Ydm', $request->coverage_effective_date);
        // $coverage_termination_date = strtotime('Ydm', $request->coverage_termination_date);


        if ($request->add_new == 1) {


            $validator = Validator::make($request->all(), [


                'member_id' => [
                    'required',
                    'max:10', Rule::unique('MEMBER')->where(function ($q) {
                        $q->whereNotNull('member_id');
                    })
                ],



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                //Member Tab and Override Tab

                $createddate = date('y-m-d');

                $add_member = DB::table('member')
                    ->insert([
                        //member tab
                        'CUSTOMER_ID' => $request->customer_id,
                        'CLIENT_ID' => $request->client_id,
                        'CLIENT_GROUP_ID' => $request->client_group_id,
                        'MEMBER_ID' => $request->member_id,
                        'PERSON_CODE' => $request->person_code,
                        'STATUS' => $request->status,
                        'EFFECTIVE_DATE_OVERRIDE' => $request->effective_date_override,
                        'MEMBER_LAST_NAME' => $request->member_last_name,
                        'MEMBER_FIRST_NAME' => $request->member_first_name,
                        'ADDRESS_1' => $request->address_1,
                        'ADDRESS_2' => $request->address_2,

                        'CITY' => $request->city,
                        'STATE' => $request->state,
                        'ZIP_CODE' => $request->zipcode,
                        'PHONE' => $request->phone,
                        'ANNIV_DATE' => $request->anniv_date,
                        'DATE_OF_BIRTH' => $request->date_of_birth,
                        'SEX_OF_PATIENT' => $request->sex_of_patient,
                        'PATIENT_PIN_NUMBER' => $request->patient_pin_number,
                        'RELATIONSHIP' => $request->relationship,
                        'HEALTH_COND_1' => $request->health_cond_1,
                        'HEALTH_COND_2' => $request->health_cond_2,
                        'HEALTH_COND_3' => $request->health_cond_3,
                        'HEALTH_COND_4' => $request->health_cond_4,
                        'HEALTH_COND_5' => $request->health_cond_5,
                        'HEALTH_COND_6' => $request->health_cond_6,


                        'ALLERGY_COND_1' => $request->allergy_cond_1,
                        'ALLERGY_COND_2' => $request->allergy_cond_2,
                        'ALLERGY_COND_3' => $request->allergy_cond_3,
                        'ALLERGY_COND_4' => $request->allergy_cond_4,
                        'ALLERGY_COND_5' => $request->allergy_cond_5,
                        'ALLERGY_COND_6' => $request->allergy_cond_6,


                        'ACCUM_BENEFIT_PTD' => $request->accum_benefit_ptd,
                        'ACCUM_DEDUCTIBLE_PTD' => $request->accum_deductible_ptd,
                        'REMAIN_BENEFIT_PTD' => $request->remain_benefit_ptd,
                        'ACCUM_BENEFIT_YTD' => $request->accum_benefit_ytd,
                        'ACCUM_DEDUCTIBLE_YTD' => $request->accum_deductible_ytd,
                        'REMAIN_BENEFIT_YTD' => $request->remain_benefit_ytd,
                        'ELIGIBILITY_OVRD' => $request->eligibility_ovrd,
                        'PLAN_OPTION' => $request->plan_option,
                        'PRIMARY_PRESCRIBER' => $request->primary_prescriber,
                        'DATE_TIME_CREATED' => $createddate,
                        'ALT_MEMBER_ID' => $request->alt_member_id,
                        'MISC_DATA' => $request->misc_data,
                        'PRIM_COVERAGE_INS_CARRIER' => $request->prim_coverage_ins_carrier,
                        'ELIG_LOCK_DATE' => $request->elig_lock_date,
                        'LOAD_PROCESS_DATE' => $request->load_process_date,



                        //Overrides
                        'COPAY_SCHED_OVR_FLAG' => $request->copay_sched_ovr_flag,
                        'COPAY_SCHED_OVR' => $request->copay_sched_ovr,
                        'ACCUM_BENE_OVR_FLAG' => $request->accum_bene_ovr_flag,
                        'ACCUM_BENE_PLAN_OVR' => $request->accum_bene_plan_ovr,
                        'ACCUM_ADJMNT_MBR_PAID_AMT' => $request->accum_adjmnt_mbr_paid_amt,

                        'WORK_COMP_REF_NUM' => $request->work_comp_ref_num,
                        'INJURY_DATE' => $request->injury_date,
                        'PHARMACY_NABP' => $request->pharmacy_nabp,
                        'RX_NETWORK_ID' => $request->rx_network_id,

                        'ACCUM_BENE_EFF_DATE_1' => $request->accum_bene_eff_date_1,
                        'ACCUM_BENE_EFF_DATE_2' => $request->accum_bene_eff_date_2,
                        'ACCUM_BENE_EFF_DATE_3' => $request->accum_bene_eff_date_3,
                        'ACCUM_BENE_TERM_DATE_1' => $request->ACCUM_BENE_TERM_DATE_1,
                        'ACCUM_BENE_TERM_DATE_2' => $request->ACCUM_BENE_TERM_DATE_2,
                        'ACCUM_BENE_TERM_DATE_3' => $request->ACCUM_BENE_TERM_DATE_3,


                        'ACCUM_ADJMNT_MBR_PAID_AMT_2' => $request->accum_adjmnt_mbr_paid_amt_2,
                        'ACCUM_ADJMNT_PLAN_PAID_AMT_2' => $request->accum_adjmnt_plan_paid_amt_2,
                        'ACCUM_ADJMNT_MBR_PAID_AMT_3' => $request->accum_adjmnt_mbr_paid_amt_3,
                        'ACCUM_ADJMNT_PLAN_PAID_AMT_3' => $request->accum_adjmnt_plan_paid_amt_3,

                        'ELIG_VALIDATION_ID' => $request->elig_validation_id,
                        'ACCUM_ADJMNT_MBR_PAID_MOP_1' => $request->accum_adjmnt_mbr_paid_mop_1,
                        'ACCUM_ADJMNT_MBR_PAID_MOP_2' => $request->accum_adjmnt_mbr_paid_mop_2,
                        'ACCUM_ADJMNT_MBR_PAID_MOP_3' => $request->accum_adjmnt_mbr_paid_mop_3,

                        'MISC_GROUPING_1' => $request->misc_grouping_1,
                        'MISC_GROUPING_2' => $request->misc_grouping_2,
                        'misc_id' => $request->misc_id,
                        'USER_DEFINED_CODE_1' => $request->user_defined_code_1,
                        'USER_DEFINED_CODE_2' => $request->user_defined_code_2,
                        'COUNTRY' => $request->country,
                        // 'COUNTRY_CODE'=>$request->country_code,

                    ]);

                $member = DB::table('member')
                    ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
                    ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
                    ->where(DB::raw('UPPER(client_group_id)'), strtoupper($request->client_group_id))
                    ->where(DB::raw('UPPER(member_id)'), strtoupper($request->member_id))
                    ->first();
                $record_snapshot_member = json_encode($member);
                $save_member_audit = $this->auditMethod('IN', $record_snapshot_member, 'MEMBER');
                // //Coverage Tab

                $coverage_list_array = json_decode(json_encode($request->coverage_form, true));

                if (!empty($request->coverage_form)) {
                    $coverage_list = $coverage_list_array[0];
                    foreach ($coverage_list_array as $key => $coverage_list) {

                        $add_member_coverage = DB::table('MEMBER_COVERAGE')
                            ->insert([
                                'customer_id' => $coverage_list->customer_id,
                                'client_id' => $coverage_list->client_id,
                                'client_group_id' => $coverage_list->client_group_id,
                                'member_id' => $coverage_list->member_id,
                                'EFFECTIVE_DATE' => $coverage_list->effective_date,
                                'TERMINATION_DATE' => $coverage_list->termination_date,
                                'plan_id' => $coverage_list->plan_id,
                                'COPAY_STRATEGY_ID' => $coverage_list->copay_strategy_id,
                                'ACCUM_BENEFIT_STRATEGY_ID' => $coverage_list->accum_benefit_strategy_id,
                                'PRICING_STRATEGY_ID' => $coverage_list->pricing_strategy_id,
                            ]);

                        $member_coverage = DB::table('MEMBER_COVERAGE')
                            ->where('customer_id', $coverage_list->customer_id)
                            ->where('client_id', $coverage_list->client_id)
                            ->where('client_group_id', $coverage_list->client_group_id)
                            ->where('member_id', $coverage_list->member_id)
                            ->where('EFFECTIVE_DATE', $coverage_list->effective_date)
                            ->where('TERMINATION_DATE', $coverage_list->termination_date)
                            ->where('plan_id', $coverage_list->plan_id)
                            ->where('COPAY_STRATEGY_ID', $coverage_list->copay_strategy_id)
                            ->where('ACCUM_BENEFIT_STRATEGY_ID', $coverage_list->accum_benefit_strategy_id)
                            ->where('PRICING_STRATEGY_ID', $coverage_list->pricing_strategy_id)
                            ->first();
                        $record_snap_mem_coverage = json_encode($member_coverage);
                        $save_audit_mem_cov = $this->auditMethod('IN', $record_snap_mem_coverage, 'MEMBER_COVERAGE');
                    }
                }

                //coverage-history

                $coverage_Hist_array = json_decode(json_encode($request->coverage_form, true));

                if (!empty($request->coverage_form)) {
                    $coverage_history = $coverage_Hist_array[0];


                    foreach ($coverage_Hist_array as $key => $coverage_history) {

                        $add_hist = DB::table('MEMBER_HIST')
                            ->insert([
                                'CUSTOMER_ID' => $coverage_history->customer_id,
                                'CLIENT_ID' => $coverage_history->client_id,
                                'CLIENT_GROUP_ID' => $coverage_history->client_group_id,
                                'MEMBER_ID' => $coverage_history->member_id,
                                "DATE_TIME_MODIFIED" => date('Y-m-d H:i:s'),

                                // 'PERSON_CODE'=>$coverage_history->person_code,
                                // 'FROM_EFFECTIVE_DATE'=>$coverage_history->effective_date,
                                // 'FROM_TERMINATION_DATE'=>$coverage_history->termination_date,
                                // 'FROM_PLAN_ID'=>$coverage_history->from_plan_id,
                                'TO_EFFECTIVE_DATE' => $coverage_history->effective_date,
                                'TO_TERMINATION_DATE' => $coverage_history->termination_date,
                                // 'TO_PLAN_ID'=>$coverage_history->to_plan_id,
                                'CHG_TYPE_IND' => 'A',
                                // 'FROM_COVERAGE_STRATEGY_ID'=>$coverage_history->from_coverage_strategy_id,
                                // 'FROM_DRUG_COV_STRATEGY_ID'=>$coverage_history->from_drug_cov_strategy,

                                // 'FROM_PREF_MAINT_DRUG_STRAT_ID'=>$coverage_history->from_pref_maint_drug_strat_id,
                                // 'FROM_PRICING_STRATEGY_ID'=>$coverage_history->from_pricing_strategy_id,
                                // 'FROM_COPAY_STRATEGY_ID'=>$coverage_history->from_copay_strategy_id,

                                // 'FROM_ACCUM_BENEFIT_STRAT_ID'=>$coverage_history->from_accum_benefit_strat_id,
                                // 'TO_COVERAGE_STRATEGY_ID'=>$coverage_history->to_coverage_strategy_id,
                                // 'TO_DRUG_COV_STRATEGY_ID'=>$coverage_history->to_drug_cov_strategy_id,
                                // 'TO_PREF_MAINT_DRUG_STRAT_ID'=>$coverage_history->to_pref_maint_drug_strat_id,
                                // 'TO_PRICING_STRATEGY_ID'=>$coverage_history->to_pricing_strategy_id,

                                // 'TO_COPAY_STRATEGY_ID'=>$coverage_history->to_copay_strategy_id,

                            ]);
                        $mem_hist = DB::table('MEMBER_HIST')
                            ->where('CUSTOMER_ID', $coverage_history->customer_id)
                            ->where('CLIENT_ID', $coverage_history->client_id)
                            ->where('CLIENT_GROUP_ID', $coverage_history->client_group_id)
                            ->where('MEMBER_ID', $coverage_history->member_id)
                            ->first();

                        $record_snap_mem_hist = json_encode($mem_hist);
                        $save_audit_mem_hist = $this->auditMethod('IN', $record_snap_mem_hist, 'MEMBER_HIST');
                    }
                }



                //diagnosis-helath conditions add


                //Diagnosis Tab

                $diagnosis_list_array = json_decode(json_encode($request->diagnosis_form, true));

                if (!empty($request->diagnosis_form)) {
                    $diagnosis_list = $diagnosis_list_array[0];


                    foreach ($diagnosis_list_array as $key => $diagnosis_list) {


                        $add_diagnosis = DB::table('MEMBER_DIAGNOSIS')
                            ->insert([
                                'customer_id' => $diagnosis_list->customer_id,
                                'client_id' => $diagnosis_list->client_id,
                                'client_group_id' => $diagnosis_list->client_group_id,
                                'member_id' => $diagnosis_list->member_id,
                                'DIAGNOSIS_ID' => $diagnosis_list->diagnosis_id,
                                "person_code" => "0",
                                'EFFECTIVE_DATE' => $diagnosis_list->effective_date,
                                'TERMINATION_DATE' => $diagnosis_list->termination_date,

                            ]);

                        $member_diag = DB::table('MEMBER_DIAGNOSIS')
                            ->where('customer_id', $diagnosis_list->customer_id)
                            ->where('client_id', $diagnosis_list->client_id)
                            ->where('client_group_id', $diagnosis_list->client_group_id)
                            ->where('member_id', $diagnosis_list->member_id)
                            ->where('DIAGNOSIS_ID', $diagnosis_list->diagnosis_id)
                            ->first();

                        $record_snap_mem_diag = json_encode($member_diag);
                        $save_audit_mem_diag = $this->auditMethod('IN', $member_diag, 'MEMBER_DIAGNOSIS');


                        $add_diagnosis_history = DB::table('MEMBER_DIAGNOSIS_HISTORY')
                            ->insert([
                                'CUSTOMER_ID' => $diagnosis_list->customer_id,
                                'CLIENT_ID' => $diagnosis_list->client_id,
                                'CLIENT_GROUP_ID' => $diagnosis_list->client_group_id,
                                'MEMBER_ID' => $diagnosis_list->member_id,
                                'DIAGNOSIS_ID' => $diagnosis_list->diagnosis_id,
                                "PERSON_CODE" => "000",
                                "CHG_TYPE_IND" => "A",
                                "BATCH_SEQUENCE_NUMBER" => '0',
                                'FROM_EFFECTIVE_DATE' => $diagnosis_list->effective_date,
                                'FROM_TERMINATION_DATE' => $diagnosis_list->termination_date,
                                "TO_EFFECTIVE_DATE" => $diagnosis_list->effective_date,
                                "TO_TERMINATION_DATE" => $diagnosis_list->termination_date,
                                "USER_ID_CREATED" => "",

                            ]);
                        $diagnosis_history = DB::table('MEMBER_DIAGNOSIS_HISTORY')
                            ->where('CUSTOMER_ID', $diagnosis_list->customer_id)
                            ->where('CLIENT_ID', $diagnosis_list->client_id)
                            ->where('CLIENT_GROUP_ID', $diagnosis_list->client_group_id)
                            ->where('MEMBER_ID', $diagnosis_list->member_id)
                            ->where('FROM_EFFECTIVE_DATE', $diagnosis_list->effective_date)
                            ->first();
                        $record_snap = json_encode($diagnosis_history);
                        $save_audit_diag_hist = $this->auditMethod('IN', $record_snap, 'MEMBER_DIAGNOSIS_HISTORY');
                    }
                }
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add_member);
            }
        } else if ($request->add_new == 0) {

            //Member Tab Update
            $update_member = DB::table('member')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->where('member_id', $request->member_id)
                ->update([
                    //member tab
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
                    'patient_pin_number' => $request->patient_pin_number,
                    'alt_member_id' => $request->alt_member_id,
                    'sex_of_patient' => $request->sex_of_patient,

                    //Overrides
                    'copay_sched_ovr_flag' => $request->copay_sched_ovr_flag,
                    'copay_sched_ovr' => $request->copay_sched_ovr,
                    'accum_bene_ovr_flag' => $request->accum_bene_ovr_flag,
                    'accum_bene_plan_ovr' => $request->accum_bene_plan_ovr,
                    'ACCUM_BENE_EFF_DATE_1' => $request->accum_bene_eff_date_1,
                    'ACCUM_BENE_EFF_DATE_2' => $request->accum_bene_eff_date_2,
                    'ACCUM_BENE_EFF_DATE_3' => $request->accum_bene_eff_date_3,
                    'ACCUM_BENE_TERM_DATE_1' => $request->ACCUM_BENE_TERM_DATE_1,
                    'ACCUM_BENE_TERM_DATE_2' => $request->ACCUM_BENE_TERM_DATE_2,
                    'ACCUM_BENE_TERM_DATE_3' => $request->ACCUM_BENE_TERM_DATE_3,
                    // 'provider_id' => $request->provider_id,
                    'PRIMARY_PRESCRIBER' => $request->prescriber_id,
                    'MISC_GROUPING_1' => $request->misc_grouping_1,
                    'MISC_GROUPING_2' => $request->misc_grouping_2,
                    'misc_id' => $request->misc_id,
                    'USER_DEFINED_CODE_1' => $request->user_defined_code_1,
                    'USER_DEFINED_CODE_2' => $request->user_defined_code_2,
                ]);

            //Bellow code is for adding member in audit

            $member = DB::table('member')
                ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
                ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
                ->where(DB::raw('UPPER(client_group_id)'), strtoupper($request->client_group_id))
                ->where(DB::raw('UPPER(member_id)'), strtoupper($request->member_id))
                ->first();
            $record_snapshot_member = json_encode($member);
            $save_member_audit = $this->auditMethod('UP', $record_snapshot_member, 'MEMBER');

            //Coverage Tab
            $delete_member_coverage = DB::table('MEMBER_COVERAGE')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->where('member_id', $request->member_id)->delete();
            $coverage_list_array = json_decode(json_encode($request->coverage_form, true));
            if (!empty($request->coverage_form)) {
                $coverage_list = $coverage_list_array[0];
                foreach ($coverage_list_array as $key => $coverage_list) {
                    $add_member_coverage = DB::table('MEMBER_COVERAGE')
                        ->insert([
                            'customer_id' => $coverage_list->customer_id,
                            'client_id' => $coverage_list->client_id,
                            'client_group_id' => $coverage_list->client_group_id,
                            'member_id' => $coverage_list->member_id,
                            'EFFECTIVE_DATE' => $coverage_list->effective_date,
                            'TERMINATION_DATE' => $coverage_list->termination_date,
                            'PLAN_ID' => $coverage_list->plan_id,
                            'COPAY_STRATEGY_ID' => $coverage_list->copay_strategy_id,
                            'ACCUM_BENEFIT_STRATEGY_ID' => $coverage_list->accum_benefit_strategy_id,
                            'PRICING_STRATEGY_ID' => $coverage_list->pricing_strategy_id,
                        ]);
                    $member_coverage = DB::table('MEMBER_COVERAGE')
                        ->where('customer_id', $request->customer_id)
                        ->where('client_id', $request->client_id)
                        ->where('client_group_id', $request->client_group_id)
                        ->where('member_id', $request->member_id)
                        ->first();

                    $record_snap_mem_coverage = json_encode($member_coverage);
                    // $save_audit_mem_coverage = $this->auditMethod('UP', $record_snap_mem_coverage, 'MEMBER_COVERAGE');



                    $add_member_coverage = DB::table('MEMBER_HIST')
                        ->where('CUSTOMER_ID', $coverage_list->customer_id)
                        ->where('CLIENT_ID', $coverage_list->client_id)
                        ->where('CLIENT_GROUP_ID', $coverage_list->client_group_id)
                        ->where('MEMBER_ID', $coverage_list->member_id)
                        ->where('FROM_EFFECTIVE_DATE', $coverage_list->effective_date)
                        ->first();

                    if ($add_member_coverage) {
                        $new = $add_member_coverage;
                        $new = DB::table('MEMBER_HIST')
                            ->where('CUSTOMER_ID', $coverage_list->customer_id)
                            ->where('CLIENT_ID', $coverage_list->client_id)
                            ->where('CLIENT_GROUP_ID', $coverage_list->client_group_id)
                            ->where('MEMBER_ID', $coverage_list->member_id)
                            ->update([

                                // 'MEMBER_ID' => $coverage_history->member_id,
                                'PERSON_CODE' => $coverage_list->person_code,
                                "DATE_TIME_MODIFIED" => date('Y-m-d H:i:s'),
                                // 'FROM_EFFECTIVE_DATE'=>$coverage_history->from_effective_date,
                                // 'FROM_TERMINATION_DATE'=>$coverage_list->from_termination_date,
                                // 'FROM_PLAN_ID'=>$coverage_history->from_plan_id,
                                // 'TO_EFFECTIVE_DATE'=>$coverage_list->to_effective_date,
                                // 'TO_TERMINATION_DATE'=>$coverage_list->to_termination_date,
                                // 'TO_PLAN_ID'=>$coverage_history->to_plan_id,
                                'CHG_TYPE_IND' => "A",
                                // 'FROM_COVERAGE_STRATEGY_ID'=>$coverage_history->from_coverage_strategy_id,
                                // 'FROM_DRUG_COV_STRATEGY_ID'=>$coverage_history->from_drug_cov_strategy,

                                // 'FROM_PREF_MAINT_DRUG_STRAT_ID'=>$coverage_history->from_pref_maint_drug_strat_id,
                                // 'FROM_PRICING_STRATEGY_ID'=>$coverage_history->from_pricing_strategy_id,
                                // 'FROM_COPAY_STRATEGY_ID'=>$coverage_history->from_copay_strategy_id,

                                // 'FROM_ACCUM_BENEFIT_STRAT_ID'=>$coverage_history->from_accum_benefit_strat_id,
                                // 'TO_COVERAGE_STRATEGY_ID'=>$coverage_history->to_coverage_strategy_id,
                                // 'TO_DRUG_COV_STRATEGY_ID'=>$coverage_history->to_drug_cov_strategy_id,
                                // 'TO_PREF_MAINT_DRUG_STRAT_ID'=>$coverage_history->to_pref_maint_drug_strat_id,
                                // 'TO_PRICING_STRATEGY_ID'=>$coverage_history->to_pricing_strategy_id,

                                // 'TO_COPAY_STRATEGY_ID'=>$coverage_history->to_copay_strategy_id,

                            ]);

                        $member_hist = DB::table('MEMBER_HIST')
                            ->where('CUSTOMER_ID', $coverage_list->customer_id)
                            ->where('CLIENT_ID', $coverage_list->client_id)
                            ->where('CLIENT_GROUP_ID', $coverage_list->client_group_id)
                            ->where('MEMBER_ID', $coverage_list->member_id)
                            ->first();
                        $record_snpa_mem_hist = json_encode($member_hist);
                        $save_audit_mem_hist = $this->auditMethod('UP', $record_snpa_mem_hist, 'MEMBER_HIST');
                    } else {

                        $new = DB::table('MEMBER_HIST')
                            ->insert([
                                'CUSTOMER_ID' => $coverage_list->customer_id,
                                'CLIENT_ID' => $coverage_list->client_id,
                                'CLIENT_GROUP_ID' => $coverage_list->client_group_id,
                                'MEMBER_ID' => $coverage_list->member_id,
                                // 'PERSON_CODE' => $coverage_history->person_code,
                                'person_code' => "0",
                                // 'FROM_EFFECTIVE_DATE'=>$coverage_list->from_effective_date,
                                // 'FROM_TERMINATION_DATE'=>$coverage_list->from_termination_date,
                                // 'FROM_PLAN_ID'=>$coverage_history->from_plan_id,
                                // 'TO_EFFECTIVE_DATE'=>$coverage_history->to_effective_date,
                                // 'TO_TERMINATION_DATE'=>$coverage_history->to_termination_date,
                                // 'TO_PLAN_ID'=>$coverage_history->to_plan_id,
                                // 'CHG_TYPE_IND'=>$coverage_history->chg_type_ind,
                                // 'FROM_COVERAGE_STRATEGY_ID'=>$coverage_history->from_coverage_strategy_id,
                                // 'FROM_DRUG_COV_STRATEGY_ID'=>$coverage_history->from_drug_cov_strategy,

                                // 'FROM_PREF_MAINT_DRUG_STRAT_ID'=>$coverage_history->from_pref_maint_drug_strat_id,
                                // 'FROM_PRICING_STRATEGY_ID'=>$coverage_history->from_pricing_strategy_id,
                                // 'FROM_COPAY_STRATEGY_ID'=>$coverage_history->from_copay_strategy_id,

                                // 'FROM_ACCUM_BENEFIT_STRAT_ID'=>$coverage_history->from_accum_benefit_strat_id,
                                // 'TO_COVERAGE_STRATEGY_ID'=>$coverage_history->to_coverage_strategy_id,
                                // 'TO_DRUG_COV_STRATEGY_ID'=>$coverage_history->to_drug_cov_strategy_id,
                                // 'TO_PREF_MAINT_DRUG_STRAT_ID'=>$coverage_history->to_pref_maint_drug_strat_id,
                                // 'TO_PRICING_STRATEGY_ID'=>$coverage_history->to_pricing_strategy_id,

                                // 'TO_COPAY_STRATEGY_ID'=>$coverage_history->to_copay_strategy_id,

                            ]);
                        $member_hist = DB::table('MEMBER_HIST')
                            ->where('CUSTOMER_ID', $coverage_list->customer_id)
                            ->where('CLIENT_ID', $coverage_list->client_id)
                            ->where('CLIENT_GROUP_ID', $coverage_list->client_group_id)
                            ->where('MEMBER_ID', $coverage_list->member_id)
                            ->first();
                        $record_snpa_mem_hist = json_encode($member_hist);
                        // $save_audit_mem_hist = $this->auditMethod('IN', $record_snpa_mem_hist, 'MEMBER_HIST');
                    }
                }
            }

            //Diagnosis Tab

            $delete_member_coverage = DB::table('MEMBER_DIAGNOSIS')->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->where('member_id', $request->member_id)
                ->delete();


            $diagnosis_list_array = json_decode(json_encode($request->diagnosis_form, true));

            if (!empty($request->diagnosis_form)) {
                $diagnosis_list = $diagnosis_list_array[0];


                foreach ($diagnosis_list_array as $key => $diagnosis_list) {


                    $add_diagnosis = DB::table('MEMBER_DIAGNOSIS')
                        ->insert([
                            'customer_id' => $diagnosis_list->customer_id,
                            'client_id' => $diagnosis_list->client_id,
                            'client_group_id' => $diagnosis_list->client_group_id,
                            'member_id' => $diagnosis_list->member_id,
                            'DIAGNOSIS_ID' => $diagnosis_list->diagnosis_id,
                            "person_code" => "0",
                            'EFFECTIVE_DATE' => $diagnosis_list->effective_date,
                            'TERMINATION_DATE' => $diagnosis_list->termination_date,
                        ]);

                    $member_diag = DB::table('MEMBER_DIAGNOSIS')
                        ->where('customer_id', $diagnosis_list->customer_id)
                        ->where('client_id', $diagnosis_list->client_id)
                        ->where('client_group_id', $diagnosis_list->client_group_id)
                        ->where('member_id', $diagnosis_list->member_id)
                        ->where('DIAGNOSIS_ID', $diagnosis_list->diagnosis_id)
                        ->first();

                    $record_snap_mem_diag = json_encode($member_diag);
                    // $save_audit_mem_diag = $this->auditMethod('UP', $member_diag, 'MEMBER_DIAGNOSIS');
                }


                $add_diagnosis_history = DB::table('MEMBER_DIAGNOSIS_HISTORY')
                    ->where('CUSTOMER_ID', $diagnosis_list->customer_id)
                    ->where('CLIENT_ID', $diagnosis_list->client_id)
                    ->where('CLIENT_GROUP_ID', $diagnosis_list->client_group_id)
                    ->where('MEMBER_ID', $diagnosis_list->member_id)
                    ->where('FROM_EFFECTIVE_DATE', $diagnosis_list->effective_date)->first();

                if ($add_diagnosis_history) {
                    $new = $add_diagnosis_history;
                    $new = DB::table('MEMBER_HIST')
                        ->where('CUSTOMER_ID', $diagnosis_list->customer_id)
                        ->where('CLIENT_ID', $diagnosis_list->client_id)
                        ->where('CLIENT_GROUP_ID', $diagnosis_list->client_group_id)
                        ->where('MEMBER_ID', $diagnosis_list->member_id)
                        ->update([
                            'DIAGNOSIS_ID' => $diagnosis_list->diagnosis_id,
                            "PERSON_CODE" => "0",
                            "CHG_TYPE_IND" => "A",
                            "BATCH_SEQUENCE_NUMBER" => '0',
                            // 'FROM_EFFECTIVE_DATE' =>$diagnosis_list->effective_date,
                            // 'FROM_TERMINATION_DATE' =>$diagnosis_list->termination_date,
                            "TO_EFFECTIVE_DATE" => $diagnosis_list->effective_date,
                            "TO_TERMINATION_DATE" => $diagnosis_list->termination_date,
                            "USER_ID_CREATED" => "",

                        ]);

                    $diagnosis_history = DB::table('MEMBER_DIAGNOSIS_HISTORY')
                        ->where('CUSTOMER_ID', $diagnosis_list->customer_id)
                        ->where('CLIENT_ID', $diagnosis_list->client_id)
                        ->where('CLIENT_GROUP_ID', $diagnosis_list->client_group_id)
                        ->where('MEMBER_ID', $diagnosis_list->member_id)
                        ->where('FROM_EFFECTIVE_DATE', $diagnosis_list->effective_date)
                        ->first();
                    $record_snap = json_encode($diagnosis_history);
                    $save_audit_diag_hist = $this->auditMethod('UP', $record_snap, 'MEMBER_DIAGNOSIS_HISTORY');
                } else {
                    $new = DB::table('MEMBER_DIAGNOSIS_HISTORY')
                        ->insert([
                            'CUSTOMER_ID' => $diagnosis_list->customer_id,
                            'CLIENT_ID' => $diagnosis_list->client_id,
                            'CLIENT_GROUP_ID' => $diagnosis_list->client_group_id,
                            'MEMBER_ID' => $diagnosis_list->member_id,
                            'DIAGNOSIS_ID' => $diagnosis_list->diagnosis_id,
                            "PERSON_CODE" => "0",
                            "CHG_TYPE_IND" => "A",
                            "BATCH_SEQUENCE_NUMBER" => '0',

                            'FROM_EFFECTIVE_DATE' => $diagnosis_list->effective_date,
                            'FROM_TERMINATION_DATE' => $diagnosis_list->termination_date,
                            "TO_EFFECTIVE_DATE" => $diagnosis_list->effective_date,
                            "TO_TERMINATION_DATE" => $diagnosis_list->termination_date,
                            "USER_ID_CREATED" => "",

                        ]);
                    $diagnosis_history = DB::table('MEMBER_DIAGNOSIS_HISTORY')
                        ->where('CUSTOMER_ID', $diagnosis_list->customer_id)
                        ->where('CLIENT_ID', $diagnosis_list->client_id)
                        ->where('CLIENT_GROUP_ID', $diagnosis_list->client_group_id)
                        ->where('MEMBER_ID', $diagnosis_list->member_id)
                        ->where('FROM_EFFECTIVE_DATE', $diagnosis_list->effective_date)
                        ->first();
                    $record_snap = json_encode($diagnosis_history);
                    $save_audit_diag_hist = $this->auditMethod('UP', $record_snap, 'MEMBER_DIAGNOSIS_HISTORY');
                }
            }



            return $this->respondWithToken($this->token(), 'Updated successfully!', $update_member);
        }
    }

    public function memberDetails(Request $request){

        $member_form_data=DB::table('member')
        ->select('member.*','CUSTOMER.CUSTOMER_NAME','CUSTOMER.EFFECTIVE_DATE as cust_eff_date','CUSTOMER.TERMINATION_DATE as cust_term_date',
        'CLIENT.CLIENT_NAME as client_name','CLIENT.EFFECTIVE_DATE as client_eff_date','CLIENT.TERMINATION_DATE as client_term_date',
        'CLIENT_GROUP.GROUP_NAME','CLIENT_GROUP.GROUP_EFFECTIVE_DATE as client_group_eff_date','CLIENT_GROUP.GROUP_TERMINATION_DATE as client_group_term_date')
        ->join('CUSTOMER','CUSTOMER.CUSTOMER_ID','=','member.CUSTOMER_ID')
        ->join('CLIENT','CLIENT.CLIENT_ID','=','member.CLIENT_ID')
        ->join('CLIENT_GROUP','CLIENT_GROUP.CLIENT_GROUP_ID','=','member.CLIENT_GROUP_ID')
        ->where('member.member_id',$request->member_id)->first();





        $member_form_data_effe=DB::table('MEMBER_COVERAGE')->where('member_id',$request->member_id)->get()->last();
        $member_coverages=DB::table('MEMBER_COVERAGE')->where('member_id',$request->member_id)->get();
        $member_coverage_history=DB::table('MEMBER_HIST')->where('member_id',$request->member_id)->get();
        $member_coverage_history=DB::table('MEMBER_HIST')->where('member_id',$request->member_id)->get();
        $member_diagnosis=DB::table('MEMBER_DIAGNOSIS')->where('member_id',$request->member_id)->get();
        $prior_authorizations=DB::table('PRIOR_AUTHORIZATIONS')->where('member_id',$request->member_id)->get();
        $change_log=DB::table('MEMBER_CHANGE_LOG')->where('member_id',$request->member_id)->get();

        
        
        // dd($member_form_data_effe);


        $merged = [
            'member_form_data' => $member_form_data,
            'member_form_data_effective_dates'=>$member_form_data_effe,
            "member_coverages"=>$member_coverages,
            "member_coverage_history"=>$member_coverage_history,
            "member_diagnosis"=>$member_diagnosis,
            "prior_authorizations"=>$prior_authorizations,
            "change_log"=>$change_log
          ];

          return $this->respondWithToken($this->token(), 'data fetched successfully', $merged);



    }
}