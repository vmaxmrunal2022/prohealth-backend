<?php

namespace App\Http\Controllers;

use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    use AuditTrait;
    public function add(Request $request)
    {
        $customer_data = DB::table('CUSTOMER')
            ->where(DB::raw('UPPER(CUSTOMER_ID)'), strtoupper($request->customer_id))
            ->first();
        $errorMsg = ["Client effective date must be greater than customer effective date"];
        if ((date('Y-m-d', strtotime($request->effective_date))) < (date('Y-m-d', strtotime($customer_data->effective_date)))) {
            return $this->respondWithToken(
                $this->token(),
                [$errorMsg],
                '',
                false
            );
        }

        $createddate = date('y-m-d');
        if ($request->add_new) {
            $validator = Validator::make($request->all(), [
                'client_id' => ['required', 'max:10', Rule::unique('CLIENT')->where(function ($q) {
                    $q->whereNotNull('client_id');
                })],
                'customer_id' => ['required'],
                'client_name' => ['max:25'],
                'address_1' => ['max:25'],
                'address_2' => ['max:25'],
                'city' => ['max:18'],
                'state' => ['max:2'],
                'zip_code' => ['max:10'],
                'effective_date' => ['max:10'],
                'termination_date' => ['max:10', 'after:effective_date'],
                // 'comm_charge_paid' => ['numeric'],
                // 'comm_charge_reject' => ['numeric'],

            ]);
            if ($validator->fails()) {
                $fieldsWithErrorMessagesArray = $validator->messages()->get('*');
                // dd($fieldsWithErrorMessagesArray);
                return $this->respondWithToken($this->token(), $validator->errors(), $fieldsWithErrorMessagesArray, false);
            } else {
                $accum_benfit_stat_names = DB::table('CLIENT')->insert(
                    [
                        'country' => $request->country,
                        'country_code' => $request->country_code,
                        'client_name' => $request->client_name,
                        'customer_id' => $request->customer_id,
                        'client_id' => $request->client_id,
                        'address_1' => $request->address_1,
                        'address_2' => $request->address_2,
                        'city' => $request->city,
                        'state' => $request->state,
                        'zip_code' => $request->zip_code,
                        'zip_plus_2' => $request->zip_plus_2,
                        'phone' => $request->phone,
                        'fax' => $request->fax,
                        'contact' => $request->contact,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'date_time_created' => date('Ymd H:i:s'),
                        'user_id' => '1',
                        'date_time_modified' => date('Ymd H:i:s'),
                        'form_id' => '1',
                        'auto_term_level' => $request->auto_term_level,
                        'census_date' => date('Ymd', strtotime($request->census_date)),
                        'policy_anniv_day' => $request->policy_anniv_day,
                        'auto_term_level' => $request->auto_term_level,
                        'census_date' => date('Ymd', strtotime($request->census_date)),
                        'policy_anniv_month' => $request->policy_anniv_month,
                        'policy_anniv_day' => $request->policy_anniv_day,
                        'num_of_active_contracts' => $request->num_of_active_contracts,
                        'num_of_active_members' => $request->num_of_active_members,
                        'num_of_termed_contracts' => $request->num_of_termed_contracts,
                        'num_of_termed_members' => $request->num_of_termed_members,
                        'num_of_pending_contracts' => $request->num_of_pending_contracts,
                        'num_of_pending_members' => $request->num_of_pending_members,
                        'pharmacy_exceptions_flag' => $request->pharmacy_exceptions_flag,
                        'prescriber_exceptions_flag' => $request->prescriber_exceptions_flag,
                        'prescriber_exceptions_flag_2' => $request->prescriber_exceptions_flag_2,
                        // 'Prescriber_Grouping_id' => $request->Prescriber_Grouping_id,
                        'super_rx_network_id' => $request->super_rx_network_id,
                        'auto_term_level' => $request->auto_term_level,
                        'auto_fam_member_term' => $request->auto_fam_member_term,
                        'elig_type' => $request->elig_type,
                        'membership_processing_flag' => $request->membership_processing_flag,
                        'overlap_coverage_tie_breaker' => $request->overlap_coverage_tie_breaker,
                        'elig_date_edit_ovr_flag' => $request->elig_date_edit_ovr_flag,
                        // 'rule_id' => $request->rule_id,
                        'auth_xfer_ind' => $request->auth_xfer_ind,
                        'member_change_log_opt' => $request->member_change_log_opt,
                        'rva_list_id' => $request->rva_list_id,
                        'elig_validation_id' => $request->elig_validation_id,
                        'smbpp' => $request->smbpp,
                        'other_cov_proc_flag' => $request->other_cov_proc_flag,
                        'accum_bene_fam_sum_ind' => $request->accum_bene_fam_sum_ind,
                        'max_num_trans_interim_elig' => $request->max_num_trans_interim_elig,
                        'max_days_interim_elig' => $request->max_days_interim_elig,
                        'date_written_to_first_fill' => $request->date_written_to_first_fill,
                        'date_filled_to_sub_online' => $request->date_filled_to_sub_online,
                        'date_filled_to_sub_dmr' => $request->date_filled_to_sub_dmr,
                        'date_sub_to_filled_future' => $request->date_sub_to_filled_future,
                        'days_for_reversals' => $request->days_for_reversals,
                        'non_profit_tax_exempt_flag' => $request->non_profit_tax_exempt_flag,
                        'reqd_u_and_c_flag' => $request->reqd_u_and_c_flag,
                        'excl_plan_ndc_gpi_excep_flag' => $request->excl_plan_ndc_gpi_excep_flag,
                        'excl_sys_ndc_gpi_excep_flag' => $request->excl_sys_ndc_gpi_excep_flag,
                        'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                        'phys_file_srce_id' => $request->phys_file_srce_id,

                        'plan_id_1' => $request->plan_id_1,
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
                $benefitcode = DB::table('CLIENT')
                    ->where('client_id', 'like', '%' . $request->client_id . '%')
                    ->where('customer_id', 'like', '%' . $request->customer_id . '%')
                    ->first();
                $update_code = DB::table('CLIENT')
                    ->where(DB::raw('UPPER(client_id)'), 'like', '%' . strtoupper($request->client_id) . '%')
                    ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
                    ->get();
                $record_snapshot = json_encode($benefitcode);
                $save_audit = DB::table('FE_RECORD_LOG')
                    ->insert([
                        'user_id' => Cache::get('userId'),
                        'date_created' => date('Ymd'),
                        'time_created' => date('gisA'),
                        'table_name' => 'CLIENT',
                        'record_action' => 'IN',
                        'application' => 'ProPBM',
                        // 'record_snapshot' => $request->client_id . '-' . $record_snapshot,
                        'record_snapshot' => $record_snapshot,
                    ]);
                return $this->respondWithToken($this->token(), 'Added Successfully!', $update_code);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'client_id' => ['required', 'max:10'],
                'customer_id' => ['required'],
                'client_name' => ['max:25'],
                'address_1' => ['max:25'],
                'address_2' => ['max:25'],
                'city' => ['max:18'],
                'state' => ['max:2'],
                'zip_code' => ['max:10'],
                'effective_date' => ['max:10'],
                'termination_date' => ['max:10', 'after:effective_date'],
                // 'comm_charge_paid' => ['numeric'],
                // 'comm_charge_reject' => ['numeric'],

            ]);
            if ($validator->fails()) {
                $fieldsWithErrorMessagesArray = $validator->messages()->get('*');
                // dd($fieldsWithErrorMessagesArray);
                return $this->respondWithToken($this->token(), $validator->errors(), $fieldsWithErrorMessagesArray, false);
            } else {
                $accum_benfit_stat = DB::table('CLIENT')
                    ->where('CLIENT_ID', $request->client_id)
                    ->update(
                        [
                            'client_name' => $request->client_name,
                            'country' => $request->country,
                            'country_code' => $request->country_code,
                            'address_1' => $request->address_1,
                            'address_2' => $request->address_2,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip_code' => $request->zip_code,
                            'zip_plus_2' => $request->zip_plus_2,
                            'phone' => $request->phone,
                            'fax' => $request->fax,
                            'contact' => $request->contact,
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'termination_date' => date('Ymd', strtotime($request->termination_date)),
                            'user_id' => '1',

                            'auto_term_level' => $request->auto_term_level,
                            'census_date' => date('Ymd', strtotime($request->census_date)),
                            'policy_anniv_month' => $request->policy_anniv_month,
                            'policy_anniv_day' => $request->policy_anniv_day,
                            'num_of_active_contracts' => $request->num_of_active_contracts,
                            'num_of_active_members' => $request->num_of_active_members,
                            'num_of_termed_contracts' => $request->num_of_termed_contracts,
                            'num_of_termed_members' => $request->num_of_termed_members,
                            'num_of_pending_contracts' => $request->num_of_pending_contracts,
                            'num_of_pending_members' => $request->num_of_pending_members,
                            'pharmacy_exceptions_flag' => $request->pharmacy_exceptions_flag,
                            'prescriber_exceptions_flag' => $request->prescriber_exceptions_flag,
                            'prescriber_exceptions_flag_2' => $request->prescriber_exceptions_flag_2,
                            // 'Prescriber_Grouping_id' => $request->Prescriber_Grouping_id,
                            'super_rx_network_id' => $request->super_rx_network_id,
                            'auto_term_level' => $request->auto_term_level,
                            'auto_fam_member_term' => $request->auto_fam_member_term,
                            'elig_type' => $request->elig_type,
                            'membership_processing_flag' => $request->membership_processing_flag,
                            'overlap_coverage_tie_breaker' => $request->overlap_coverage_tie_breaker,
                            'elig_date_edit_ovr_flag' => $request->elig_date_edit_ovr_flag,
                            // 'rule_id' => $request->rule_id,
                            'auth_xfer_ind' => $request->auth_xfer_ind,
                            'member_change_log_opt' => $request->member_change_log_opt,
                            'rva_list_id' => $request->rva_list_id,
                            'elig_validation_id' => $request->elig_validation_id,
                            'smbpp' => $request->smbpp,
                            'other_cov_proc_flag' => $request->other_cov_proc_flag,
                            'accum_bene_fam_sum_ind' => $request->accum_bene_fam_sum_ind,
                            'max_num_trans_interim_elig' => $request->max_num_trans_interim_elig,
                            'max_days_interim_elig' => $request->max_days_interim_elig,
                            'date_written_to_first_fill' => $request->date_written_to_first_fill,
                            'date_filled_to_sub_online' => $request->date_filled_to_sub_online,
                            'date_filled_to_sub_dmr' => $request->date_filled_to_sub_dmr,
                            'date_sub_to_filled_future' => $request->date_sub_to_filled_future,
                            'days_for_reversals' => $request->days_for_reversals,
                            'non_profit_tax_exempt_flag' => $request->non_profit_tax_exempt_flag,
                            'reqd_u_and_c_flag' => $request->reqd_u_and_c_flag,
                            'excl_plan_ndc_gpi_excep_flag' => $request->excl_plan_ndc_gpi_excep_flag,
                            'excl_sys_ndc_gpi_excep_flag' => $request->excl_sys_ndc_gpi_excep_flag,
                            'phys_file_srce_id' => $request->phys_file_srce_id,
                            'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,

                            'plan_id_1' => $request->plan_id_1,
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
                $benefitcode = DB::table('client')
                    ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
                    ->select('CLIENT_ID', 'CLIENT_NAME', 'customer.CUSTOMER_NAME as customername', 'client.CUSTOMER_ID as customerid', 'client.EFFECTIVE_DATE as clienteffectivedate', 'client.TERMINATION_DATE as clientterminationdate')
                    ->where(DB::raw('UPPER(client.CLIENT_ID)'), 'like', '%' . strtoupper($request->client_id) . '%')
                    // ->orWhere(DB::raw('UPPER(client.CLIENT_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
                    ->orWhere('customer.CUSTOMER_ID', 'like', '%' . strtoupper($request->customer_id) . '%')
                    ->get();
                $benefitcode_audit = DB::table('CLIENT')->where('client_id', 'like', '%' . $request->client_id . '%')
                    ->where('customer_id', 'like', '%' . $request->customer_id . '%')->first();
                // $record_snapshot = implode('|', (array) $benefitcode);
                $record_snapshot = json_encode($benefitcode_audit);
                // $record_snapshot = json_encode($benefitcode);
                $save_audit = DB::table('FE_RECORD_LOG')
                    ->insert([
                        'user_id' => Cache::get('userId'),
                        'date_created' => date('Ymd'),
                        'time_created' => date('gisA'),
                        'table_name' => 'CLIENT',
                        'record_action' => 'UP',
                        'application' => 'ProPBM',
                        // 'record_snapshot' => $request->client_id . '-' . $record_snapshot,
                        'record_snapshot' => $record_snapshot,
                    ]);

                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
            }
        }
    }


    public function getClient(Request $request)
    {
        // $customerid = $request->customerid;
        // $customername = $request->customername;
        // $clientid = $request->clientid;
        // $clientname = $request->clientname;
        // return $request->all();
        $search = $request->search;


        $clients = DB::table('client')
            ->when($search, function ($q) use ($search) {
                $q->where('CUSTOMER_ID', $search);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('CLIENT_ID', $search);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('CLIENT_NAME', $search);
            })
            ->get();

        $this->respondWithToken($this->token() ?? '', 'clients loaded', $clients);
    }

    public function GetOneClient($clientid)
    {
        $client = DB::table('client')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where(DB::raw('UPPER(CLIENT_ID)'),  strtoupper($clientid))
            // ->orWhere('CLIENT_NAME', 'like', '%' . strtoupper($clientid) . '%')
            // ->orWhere('CUSTOMER_ID', 'like', '%' . strtoupper($clientid) . '%')
            ->first();
        return $this->respondWithToken($this->token(), '', $client);
    }

    public function searchClient(Request $request)
    {

        $search = $request->search;

        $client = DB::table('client')
            ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
            ->select('CLIENT_ID', 'CLIENT_NAME', 'customer.CUSTOMER_NAME as customername', 'client.CUSTOMER_ID as customerid', 'client.EFFECTIVE_DATE as clienteffectivedate', 'client.TERMINATION_DATE as clientterminationdate')
            ->where(DB::raw('UPPER(client.CLIENT_ID)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere(DB::raw('UPPER(client.CLIENT_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('customer.CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('customer.CUSTOMER_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $client);
    }

    public function deleteClient(Request $request)
    {
        $client = DB::table('CLIENT')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
            ->first();

        $record_snapshot = json_encode($client);
        $save_audit = $this->auditMethod('DE', $record_snapshot, 'CLIENT');
        $client = DB::table('CLIENT')
            ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
            ->delete();
        //Client Group
        $client_group = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
            ->get();
        for ($i = 0; $i < count($client_group); $i++) {
            // $record_snapshot = json_encode($client_group[$i]);
            $save_audit = $this->auditMethod('DE', json_encode($client_group[$i]), 'CLIENT_GROUP');
        }
        $client_group_delete = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
            ->delete();
        return $this->respondWithToken($this->token(), "Record Deleted Successfully", '');
    }
}
