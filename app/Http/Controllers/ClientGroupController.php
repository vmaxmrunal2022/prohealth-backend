<?php

namespace App\Http\Controllers;

use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientGroupController extends Controller
{

    use AuditTrait;

    public function add(Request $request)
    {
        $client_Data = DB::table('Client')
            ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
            ->first();
        $errorMsg = ["Client group effective date must be greater than client effective date"];
        // if ($client_Data && (date('Y-m-d', strtotime($request->group_effective_date))) < (date('Y-m-d', strtotime($client_Data->effective_date)))) {
        //     return $this->respondWithToken(
        //         $this->token(),
        //         [$errorMsg],
        //         '',
        //         false
        //     );
        // } else {
        //     return $this->respondWithToken(
        //         $this->token(),
        //         [$errorMsg],
        //         '',
        //         false
        //     );
        // }

        $createddate = date('y-m-d');
        if ($request->add_new) {
            $validator = Validator::make($request->all(), [
                'client_group_id' => ['required', 'max:10', Rule::unique('CLIENT_GROUP')->where(function ($q) {
                    $q->whereNotNull('client_group_id');
                })],
                'customer_id' => ['required'],
                'client_id' => ['required'],
                'group_name' => ['max:25'],
                'address_1' => ['max:25'],
                'address_2' => ['max:25'],
                'city' => ['max:18'],
                'state' => ['max:2'],
                'zip_code' => ['max:10'],
                'group_effective_date' => ['max:10'],
                'group_termination_date' => ['max:10', 'after:group_effective_date'],
                // 'comm_charge_paid' => ['numeric'],
                // 'comm_charge_reject' => ['numeric'],

            ]);
            if ($validator->fails()) {
                $fieldsWithErrorMessagesArray = $validator->messages()->get('*');
                $benefitcode = DB::table('CLIENT_GROUP')->get();
                // dd($fieldsWithErrorMessagesArray);
                return $this->respondWithToken($this->token(), $validator->errors(), $benefitcode, false);
            } else {

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

                        'plan_id' => $request->plan_id_1,
                        'plan_id_2' => $request->plan_id_2,
                        'plan_id_3' => $request->plan_id_3,
                        'coverage_eff_date_1' => $request->coverage_eff_date_1 ? date('Ymd', strtotime($request->coverage_eff_date_1)) : null,
                        'coverage_eff_date_2' =>  $request->coverage_eff_date_2 ? date('Ymd', strtotime($request->coverage_eff_date_2)) : null,
                        'coverage_eff_date_3' =>  $request->coverage_eff_date_3 ? date('Ymd', strtotime($request->coverage_eff_date_3)) : null,
                        'misc_data_1' => $request->misc_data_1,
                        'misc_data_2' => $request->misc_data_2,
                        'misc_data_3' => $request->misc_data_3,
                    ]
                );
                $benefitcode = DB::table('CLIENT_GROUP')
                    ->where('client_group_id', $request->client_group_id)->first();
                $updated = DB::table('CLIENT_GROUP')
                    ->where('client_group_id', 'like', '%' . $request->client_group_id . '%')
                    ->where('client_id', 'like', '%' . $request->client_id . '%')
                    ->where('customer_id', 'like', '%' . $request->customer_id . '%')
                    ->get();
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
            }
        } else {

            $validator = Validator::make($request->all(), [
                'customer_id' => ['required'],
                'client_id' => ['required', 'max:10'],
                'client_group_id' => ['required'],
                'group_name' => ['max:25'],
                'address_1' => ['max:25'],
                'address_2' => ['max:25'],
                'city' => ['max:18'],
                'state' => ['max:2'],
                'zip_code' => ['max:10'],
                'group_effective_date' => ['max:10'],
                'group_termination_date' => ['max:10', 'after:group_effective_date'],
                // 'comm_charge_paid' => ['numeric'],
                // 'comm_charge_reject' => ['numeric'],

            ]);
            if ($validator->fails()) {
                $fieldsWithErrorMessagesArray = $validator->messages()->get('*');
                $benefitcode = DB::table('CLIENT_GROUP')->get();
                // dd($fieldsWithErrorMessagesArray);
                return $this->respondWithToken($this->token(), $validator->errors(), $benefitcode, false);
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

                            'plan_id' => $request->plan_id_1,
                            'plan_id_2' => $request->plan_id_2,
                            'plan_id_3' => $request->plan_id_3,
                            'coverage_eff_date_1' => $request->coverage_eff_date_1 ? date('Ymd', strtotime($request->coverage_eff_date_1)) : null,
                            'coverage_eff_date_2' =>  $request->coverage_eff_date_2 ? date('Ymd', strtotime($request->coverage_eff_date_2)) : null,
                            'coverage_eff_date_3' =>  $request->coverage_eff_date_3 ? date('Ymd', strtotime($request->coverage_eff_date_3)) : null,
                            'misc_data_1' => $request->misc_data_1,
                            'misc_data_2' => $request->misc_data_2,
                            'misc_data_3' => $request->misc_data_3,
                        ]
                    );
                $benefitcode = DB::table('CLIENT_GROUP')->where('client_group_id', $request->client_group_id)->first();
                $updated = DB::table('CLIENT_GROUP')
                    ->where('client_group_id', 'like', '%' . $request->client_group_id . '%')
                    ->where('client_id', 'like', '%' . $request->client_id . '%')
                    ->where('customer_id', 'like', '%' . $request->customer_id . '%')
                    ->get();
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
        }
        return $this->respondWithToken($this->token(), 'Added Successfully!', [$benefitcode]);
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
            ->where(DB::raw('UPPER(CLIENT_GROUP_ID)'), 'like', '%' . strtoupper($clientgrpid) . '%')
            ->first();
        return $this->respondWithToken($this->token(), '', $client);
    }

    public function searchClientgroup(Request $request)
    {
        // $search = $request->search;
        // $client = DB::table('CLIENT_GROUP')
        //     // ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
        //     ->select('CLIENT_ID', 'GROUP_NAME', 'CUSTOMER_ID', 'CLIENT_GROUP_ID')
        //     ->where('CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
        //     ->orWhere('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')
        //     ->orWhere('CLIENT_GROUP_ID', 'like', '%' . strtoupper($request->search) . '%')
        //     ->orWhere('GROUP_NAME', 'like', '%' . strtoupper($request->search) . '%')
        //     ->get();

        // return $this->respondWithToken($this->token(), '', $client);
        $client = DB::table('CLIENT_GROUP')
            ->join('client', 'client.client_id', '=', 'client_group.client_id')
            ->where(DB::raw('UPPER(CLIENT_GROUP.CLIENT_GROUP_ID)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orwhere(DB::raw('UPPER(CLIENT_GROUP.CLIENT_ID)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orwhere(DB::raw('UPPER(CLIENT_GROUP.customer_id)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();


        return $this->respondWithToken($this->token(), '', $client);
    }

    public function deleteRecord(Request $request)
    {
        $client_group = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
            ->where(DB::raw('UPPER(client_group_id)'), strtoupper($request->client_group_id))
            ->first();

        $record_snapshot = json_encode($client_group);
        $save_audit = $this->auditMethod('DE', $record_snapshot, 'CLIENT_GROUP');
        $client_group = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
            ->where(DB::raw('UPPER(client_group_id)'), strtoupper($request->client_group_id))
            ->delete();

        $updated = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(customer_id)'), 'like', '%' . strtoupper($request->customer_id) . '%')
            ->where(DB::raw('UPPER(client_id)'), 'like', '%' . strtoupper($request->client_id) . '%')
            ->where(DB::raw('UPPER(client_group_id)'), 'like', '%' .  strtoupper($request->client_group_id) . '%')
            ->get();

        return $this->respondWithToken($this->token(), "Record Deleted Successfully", [$client_group]);
    }
}
