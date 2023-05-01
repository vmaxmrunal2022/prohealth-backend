<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ClientGroupController extends Controller
{



    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        if ($request->add_new) {

            $accum_benfit_stat_names = DB::table('CLIENT_GROUP')->insert(
                [
                    'customer_id' => $request->customer_id,
                    'client_id' => $request->client_id,
                    'client_group_id' => $request->client_group_id,
                    'address_1' => $request->address_1,
                    'address_2' => $request->address_2,
                    'city' => $request->city,
                    'country' => $request->country,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'zip_plus_2' => $request->zip_plus_2,
                    'phone' => $request->phone,
                    'fax' => $request->fax,
                    'contact' => $request->contact,
                    'group_effective_date' => date('Ymd', strtotime($request->group_effective_date)),
                    'group_termination_date' => date('Ymd', strtotime($request->group_termination_date)),
                    'user_id' => $request->user_id,
                    'census_date' => date('Ymd', strtotime($request->census_date)),
                    'group_name' => $request->group_name,
                    'misc_data_1' => $request->misc_data_1,
                    'misc_data_2' => $request->misc_data_2,
                    'misc_data_3' => $request->misc_data_3,
                    'prescriber_exceptions_flag' => $request->prescriber_exceptions_flag,
                    'prescriber_exceptions_flag_2' => $request->prescriber_exceptions_flag_2,
                    // 'marketing_rep_id'=>$request->marketing_rep_id,

                    'edi_address' => $request->edi_address,
                    'plan_id' => $request->plan_id,
                    'policy_anniv_month' => $request->policy_anniv_month,
                    'policy_anniv_day' => $request->policy_anniv_day,
                    'num_of_active_contracts' => $request->num_of_active_contracts,
                    'num_of_active_members' => $request->num_of_active_members,
                    'num_of_termed_contracts' => $request->num_of_termed_contracts,
                    'num_of_termed_members' => $request->num_of_termed_members,
                    'num_of_pending_contracts' => $request->num_of_pending_contracts,
                    'num_of_pending_members' => $request->num_of_pending_members,
                    'anniv_date' => date('Ymd', strtotime($request->anniv_date)),
                    'marketing_rep_id' => $request->marketing_rep_id,
                    'plan_classification' => $request->plan_classification,
                    'pharmacy_exceptions_flag' => $request->pharmacy_exceptions_flag,
                    'auto_fam_member_term' => $request->auto_fam_member_term,
                    'elig_type' => $request->elig_type,
                    'membership_processing_flag' => $request->membership_processing_flag,
                    'elig_date_edit_ovr_flag' => $request->elig_date_edit_ovr_flag,
                    'elig_validation_id' => $request->elig_validation_id,
                    'member_change_log_opt' => $request->member_change_log_opt,

                    'accum_bene_fam_sum_ind' => $request->accum_bene_fam_sum_ind,
                    'other_cov_proc_flag' => $request->other_cov_proc_flag,
                    'max_num_trans_interim_elig' => $request->max_num_trans_interim_elig,
                    'max_days_interim_elig' => $request->max_days_interim_elig,
                    'copay_sched_ovr_flag' => $request->copay_sched_ovr_flag,
                    'copay_sched_ovr' => $request->copay_sched_ovr,
                    'admin_fee' => $request->admin_fee,
                    'admin_percent' => $request->admin_percent,
                    'dmr_fee' => $request->dmr_fee,
                    'ucf_fee' => $request->ucf_fee,
                    'elig_upd_fee' => $request->elig_upd_fee,
                    'prior_auth_fee' => $request->prior_auth_fee,
                    'excl_plan_ndc_gpi_excep_flag' => $request->excl_plan_ndc_gpi_excep_flag,
                    'excl_sys_ndc_gpi_excep_flag' => $request->excl_sys_ndc_gpi_excep_flag,
                    'date_written_to_first_fill' => $request->date_written_to_first_fill,
                    'date_filled_to_sub_online' => $request->date_filled_to_sub_online,
                    'date_filled_to_sub_dmr' => $request->date_filled_to_sub_dmr,
                    'date_sub_to_filled_future' => $request->date_sub_to_filled_future,
                    'days_for_reversals' => $request->days_for_reversals,
                    'non_profit_tax_exempt_flag' => $request->non_profit_tax_exempt_flag,
                    'reqd_u_and_c_flag' => $request->reqd_u_and_c_flag,
                    'smbpp' => $request->smbpp,
                    'rva_list_id' => $request->rva_list_id,
                    'person_code_reqd_flag' => $request->person_code_reqd_flag,
                    'super_rx_network_id' => $request->super_rx_network_id,
                    'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                    'phys_file_srce_id' => $request->phys_file_srce_id,
                ]
            );
            $benefitcode = DB::table('CLIENT_GROUP')->where('client_group_id', 'like', '%' . $request->client_group_id . '%')->first();
            $record_snapshot = json_encode($benefitcode);
            $save_audit = DB::table('FE_RECORD_LOG')
                ->insert([
                    'user_id' => Cache::get('userId'),
                    'date_created' => date('Ymd'),
                    'time_created' => date('gisA'),
                    'table_name' => 'CLIENT_GROUP',
                    'record_action' => 'IN',
                    'application' => 'ProPBM',
                    'record_snapshot' => $record_snapshot,
                    // 'record_snapshot' => $record_snapshot,
                ]);
        } else {


            $accum_benfit_stat = DB::table('CLIENT_GROUP')
                ->where('CLIENT_GROUP_ID', $request->client_group_id)
                ->update(
                    [
                        'customer_id' => $request->customer_id,
                        'client_id' => $request->client_id,
                        'address_1' => $request->address_1,
                        'address_2' => $request->address_2,
                        'city' => $request->city,
                        'country' => $request->country,
                        'state' => $request->state,
                        'zip_code' => $request->zip_code,
                        'zip_plus_2' => $request->zip_plus_2,
                        'phone' => $request->phone,
                        'fax' => $request->fax,
                        'contact' => $request->contact,
                        'group_effective_date' => date('Ymd', strtotime($request->group_effective_date)),
                        'group_termination_date' => date('Ymd', strtotime($request->group_termination_date)),
                        'user_id' => $request->user_id,
                        'census_date' => date('Ymd', strtotime($request->census_date)),
                        'group_name' => $request->group_name,
                        'misc_data_1' => $request->misc_data_1,
                        'misc_data_2' => $request->misc_data_2,
                        'misc_data_3' => $request->misc_data_3,
                        'prescriber_exceptions_flag' => $request->prescriber_exceptions_flag,
                        'prescriber_exceptions_flag_2' => $request->prescriber_exceptions_flag_2,
                        'marketing_rep_id' => $request->marketing_rep_id,

                        'edi_address' => $request->edi_address,
                        'plan_id' => $request->plan_id,
                        'policy_anniv_month' => $request->policy_anniv_month,
                        'policy_anniv_day' => $request->policy_anniv_day,
                        'num_of_active_contracts' => $request->num_of_active_contracts,
                        'num_of_active_members' => $request->num_of_active_members,
                        'num_of_termed_contracts' => $request->num_of_termed_contracts,
                        'num_of_termed_members' => $request->num_of_termed_members,
                        'num_of_pending_contracts' => $request->num_of_pending_contracts,
                        'num_of_pending_members' => $request->num_of_pending_members,
                        'anniv_date' => date('Ymd', strtotime($request->anniv_date)),
                        'marketing_rep_id' => $request->marketing_rep_id,
                        'plan_classification' => $request->plan_classification,
                        'pharmacy_exceptions_flag' => $request->pharmacy_exceptions_flag,
                        'auto_fam_member_term' => $request->auto_fam_member_term,
                        'elig_type' => $request->elig_type,
                        'membership_processing_flag' => $request->membership_processing_flag,
                        'elig_date_edit_ovr_flag' => $request->elig_date_edit_ovr_flag,
                        'elig_validation_id' => $request->elig_validation_id,
                        'member_change_log_opt' => $request->member_change_log_opt,

                        'accum_bene_fam_sum_ind' => $request->accum_bene_fam_sum_ind,
                        'other_cov_proc_flag' => $request->other_cov_proc_flag,
                        'max_num_trans_interim_elig' => $request->max_num_trans_interim_elig,
                        'max_days_interim_elig' => $request->max_days_interim_elig,
                        'copay_sched_ovr_flag' => $request->copay_sched_ovr_flag,
                        'copay_sched_ovr' => $request->copay_sched_ovr,
                        'admin_fee' => $request->admin_fee,
                        'admin_percent' => $request->admin_percent,
                        'dmr_fee' => $request->dmr_fee,
                        'ucf_fee' => $request->ucf_fee,
                        'elig_upd_fee' => $request->elig_upd_fee,
                        'prior_auth_fee' => $request->prior_auth_fee,
                        'excl_plan_ndc_gpi_excep_flag' => $request->excl_plan_ndc_gpi_excep_flag,
                        'excl_sys_ndc_gpi_excep_flag' => $request->excl_sys_ndc_gpi_excep_flag,
                        'date_written_to_first_fill' => $request->date_written_to_first_fill,
                        'date_filled_to_sub_online' => $request->date_filled_to_sub_online,
                        'date_filled_to_sub_dmr' => $request->date_filled_to_sub_dmr,
                        'date_sub_to_filled_future' => $request->date_sub_to_filled_future,
                        'days_for_reversals' => $request->days_for_reversals,
                        'non_profit_tax_exempt_flag' => $request->non_profit_tax_exempt_flag,
                        'reqd_u_and_c_flag' => $request->reqd_u_and_c_flag,
                        'smbpp' => $request->smbpp,
                        'rva_list_id' => $request->rva_list_id,
                        'person_code_reqd_flag' => $request->person_code_reqd_flag,
                        'super_rx_network_id' => $request->super_rx_network_id,
                        'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                        'phys_file_srce_id' => $request->phys_file_srce_id,
                    ]
                );
            $benefitcode = DB::table('CLIENT_GROUP')->where('client_group_id', 'like', '%' . $request->client_group_id . '%')->first();
            if (!Cache::get('userId')) {

                $responseMessage = "Sorry, this user does not exist";
                return redirect()->route('login.user');
            }
            $record_snapshot = json_encode($benefitcode);
            $save_audit = DB::table('FE_RECORD_LOG')
                ->insert([
                    'user_id' => Cache::get('userId'),
                    'date_created' => date('Ymd'),
                    'time_created' => date('gisA'),
                    'table_name' => 'CLIENT_GROUP',
                    'record_action' => 'UP',
                    'application' => 'ProPBM',
                    'record_snapshot' => $record_snapshot,
                    // 'record_snapshot' => $record_snapshot,
                ]);
        }


        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }
    public function getClientGroup(Request $request)
    {
        $customerid = $request->customerid;
        $clientid = $request->clientid;
        $groupid = $request->groupid;

        $clientgroup = DB::table('client_group')
            ->when($customerid, function ($q) use ($customerid) {
                $q->where('CUSTOMER_ID', $customerid);
            })
            ->when($clientid, function ($q) use ($clientid) {
                $q->where('CLIENT_ID', $clientid);
            })
            ->when($groupid, function ($q) use ($groupid) {
                $q->where('CLIENT_GROUP_ID', $groupid);
            })
            ->get();

        $this->respondWithToken($this->token() ?? '', '', $clientgroup);
    }


    public function GetOneClientGroup($clientgrpid)
    {
        $client = DB::table('CLIENT_GROUP')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where('CLIENT_GROUP_ID', 'like', '%' . strtoupper($clientgrpid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $client);
    }

    public function searchClientgroup(Request $request)
    {

        $search = $request->search;

        $client = DB::table('CLIENT_GROUP')
            // ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
            ->select('CLIENT_ID', 'GROUP_NAME', 'CUSTOMER_ID', 'CLIENT_GROUP_ID')
            ->where('CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('CLIENT_GROUP_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GROUP_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $client);
    }
}