<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemParameterController extends Controller
{
    public function updateSystemParameter(Request $request)
    {

        // $getusersData = DB::table('GLOBAL_PARAMS')
        //     ->where('user_id', $request->user_id)
        //     ->first();

        // if ($request->has('new')) {

        //     if ($getusersData) {

        //         return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getusersData);
        //     } else {
        //         $addData = DB::table('GLOBAL_PARAMS')
        //             ->insert([
        //                 'NUM_ROUTERS' => $request->num_routers,
        //                 'ROUTER_PRIORITY' => $request->router_priority,
        //                 'SLEEP_MINS' => $request->sleep_mins,
        //                 'PREADJ_MSG_PRIORITY' => $request->preadj_msg_priority,
        //                 'DATE_TIME_CREATED' => date('d-M-y'),
        //                 'TPA_MSG_PRIORITY' => $request->tpa_msg_priority,
        //                 'POST_MSG_PRIORITY' => $request->post_msg_priority,
        //                 'IMMEDIATE_SHUTDOWN' => $request->immediate_shutdown,
        //                 'RETAIN_PERF_STATS_DAYS' => $request->retain_perf_stats_days,
        //                 'RETAIN_LOG_DAYS' => $request->retain_log_days,
        //                 'RETAIN_TRANSACTION_DAYS' => $request->retain_transaction_days,
        //                 'MAINTENANCE_TIME' => $request->maintenance_time,
        //                 'SYSTEM_USE' => $request->system_use,
        //                 'USER_ID' => $request->user_id,
        //                 'DATE_TIME_MODIFIED' => date('d-M-y'),
        //                 'NUM_GENS' => $request->num_gens,
        //                 'GEN_PRIORITY' => $request->gen_priority,
        //                 'GEN_PRIORITY' => $request->processor_number,
        //                 'PROCESSOR_NAME' => $request->processor_name,
        //                 'PROCESSOR_ADDRESS' => $request->processor_address,
        //                 'PROCESSOR_CITY' => $request->processor_city,
        //                 'PROCESSOR_STATE' => $request->processor_state,
        //                 'PROCESSOR_ZIP' => $request->processor_zip,
        //                 'PROCESSOR_PHONE' => $request->processor_phone,
        //                 'THIRD_PARTY_TYPE' => $request->third_party_type,
        //                 'VERSION_NUMBER' => $request->version_number,
        //                 'FRONT_END_RECORD_AUDIT' => $request->front_end_record_audit,
        //             ]);

        //         $addData2 = DB::table('FE_USERS_PASSWORD_HISTORY')

        //             ->insert([
        //                 'USER_ID' => $request->user_id,
        //                 'APPLICATION' => $request->application,

        //             ]);

        //         if ($addData) {
        //             return $this->respondWithToken($this->token(), 'Added Successfully!!!', $addData);
        //         }
        //     }
        // } else { {
        //         $updateUser = DB::table('FE_USERS')
        //             ->where('user_id', $request->user_id)
        //             ->update([
        //                 'user_password' => $request->user_password,
        //                 'user_first_name' => $request->user_first_name,
        //                 'user_last_name' => $request->user_last_name,
        //                 'DATE_TIME_CREATED' => date('d-M-y'),
        //                 'group_id' => $request->group_id,
        //                 'application' => $request->application,
        //                 'user_password' => $request->user_password,
        //                 'sql_server_user_id' => $request->sql_server_user_id,
        //                 'sql_server_user_password' => $request->sql_server_user_password,
        //                 'privs' => $request->privs,
        //             ]);

        //         $updateUser = DB::table('FE_USERS_PASSWORD_HISTORY')
        //             ->where('user_id', $request->user_id)
        //             ->update([
        //                 'APPLICATION' => $request->application,
        //                 'USER_PASSWORD' => $request->user_password,
        //                 'ENCRYPTION_TYPE' => $request->encryption_type,
        //             ]);




        //         if ($updateUser) {
        //             return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateUser);
        //         }
        //     }
        // }

        $update_system_parameter = DB::table('GLOBAL_PARAMS')
            ->update([
                'NUM_ROUTERS' => $request->num_routers,
                'ROUTER_PRIORITY' => $request->router_priority,
                'SLEEP_MINS' => $request->sleep_mins,
                'PREADJ_MSG_PRIORITY' => $request->preadj_msg_priority,
                'DATE_TIME_CREATED' => date('d-M-y'),
                'TPA_MSG_PRIORITY' => $request->tpa_msg_priority,
                'POST_MSG_PRIORITY' => $request->post_msg_priority,
                'IMMEDIATE_SHUTDOWN' => $request->immediate_shutdown,
                'RETAIN_PERF_STATS_DAYS' => $request->retain_perf_stats_days,
                'RETAIN_LOG_DAYS' => $request->retain_log_days,
                'RETAIN_TRANSACTION_DAYS' => $request->retain_transaction_days,
                'MAINTENANCE_TIME' => $request->maintenance_time,
                'SYSTEM_USE' => $request->system_use,
                'USER_ID' => $request->user_id,
                'DATE_TIME_MODIFIED' => date('d-M-y'),
                'NUM_GENS' => $request->num_gens,
                'GEN_PRIORITY' => $request->gen_priority,
                'GEN_PRIORITY' => $request->processor_number,
                'PROCESSOR_NAME' => $request->processor_name,
                'PROCESSOR_ADDRESS' => $request->processor_address,
                'PROCESSOR_CITY' => $request->processor_city,
                'PROCESSOR_STATE' => $request->processor_state,
                'PROCESSOR_ZIP' => $request->processor_zip,
                'PROCESSOR_PHONE' => $request->processor_phone,
                'THIRD_PARTY_TYPE' => $request->third_party_type,
                'VERSION_NUMBER' => $request->version_number,
                'FRONT_END_RECORD_AUDIT' => $request->front_end_record_audit,
            ]);

        return $this->respondWithToken($this->token(), 'System Parameters Updated Successfully!', $update_system_parameter);
    }

    public function getSystemParameters(Request $request)
    {
        $systemParameter = DB::table("GLOBAL_PARAMS")
            ->first();
        return $this->respondWithToken($this->token(), '', $systemParameter);
    }

    public function getState(Request $request)
    {
        $data = DB::table('COUNTRY_STATES')
            ->select('COUNTRY_STATES.state_code', 'ZIP_CODES.ZIP_CODES')
            ->join('ZIP_CODES', 'ZIP_CODES.state', '=', 'COUNTRY_STATES.state_code')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getCountries(Request $request)
    {
        $countries = DB::table('country_states')
            ->select('country_code')
            ->get();

        return $this->respondWithToken($this->token(), '', $countries);
    }

    public function getThirdPartyPrice(Request $request)
    {
        $third_party_types = [
            ['third_party_type_id' => 'G', 'third_party_type_title' => 'Government'],
            ['third_party_type_id' => 'P', 'third_party_type_title' => 'Private'],
            ['third_party_type_id' => '', 'third_party_type_title' => 'None']
        ];
        return $this->respondWithToken($this->token(), '', $third_party_types);
    }

    public function getTaxStatus(Request $request)
    {
        $tax_status = [
            ['tax_status_id' => '0', 'tax_status_name' => 'Taxable'],
            ['tax_status_id' => '1', 'tax_status_name' => 'Tax Exempt']
        ];
        return $this->respondWithToken($this->token(), '', $tax_status);
    }

    public function getUCPlan(Request $request)
    {
        $UC_plan = [
            ['uc_plan_id' => '0', 'uc_plan_title' => 'No'],
            ['uc_plan_id' => '1', 'uc_plan_title' => 'Yes']
        ];
        return $this->respondWithToken($this->token(), '', $UC_plan);
    }

    public function getAutomatedTermination(Request $request)
    {
        $automated_termination_level = [
            ['id' => '0', 'title' => 'Overlap allowed within database'],
            ['id' => '1', 'title' => 'Automated termination within client'],
            ['id' => '2', 'title' => 'Automated termination within customer'],
            ['id' => '4', 'title' => 'Automated termination within database'],
            ['id' => '5', 'title' => 'No automated termination - Reject - within database (not specified)'],
        ];
        return $this->respondWithToken($this->token(), '', $automated_termination_level);
    }

    public function getOverlapCoverageTie(Request $request)
    {
        $overlap_coverage_tie = [
            ['id' => 'G', 'title' => 'Use group submitted by provider, if no match use last added'],
            ['id' => 'A', 'title' => 'Use member record last added'],
            ['id' => 'U', 'title' => 'Use member record last update'],
            ['id' => '', 'title' => '(none)'],
        ];
        return $this->respondWithToken($this->token(), '', $overlap_coverage_tie);
    }

    public function getProcessorControlFlag(Request $request)
    {
        $processor_control_flag = [
            ['id'  => 'N', 'title' => 'Do not use processor control number  to retrive eligibility'],
            ['id'  => 'C', 'title' => 'Customer will be determined from processor control number'],
            ['id'  => 'B', 'title' => 'Both customer & client will be determined from processor control number'],
            ['id'  => '', 'title' => 'none'],
        ];
        return $this->respondWithToken($this->token(), '', $processor_control_flag);
    }

    public function getEligibilityChangeLog(Request $request)
    {
        $eligibility_change_log = [
            ['id' => '0', 'title' => 'Member record changes will NOT be logged'],
            ['id' => '1', 'title' => 'Member record changes will be logged'],
            ['id' => '', 'title' => '(Not specified)'],
        ];
        return $this->respondWithToken($this->token(), '', $eligibility_change_log);
    }
}
