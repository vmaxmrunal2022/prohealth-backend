<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{



    public function add(Request $request)
    {

        $createddate = date('y-m-d');
        if ($request->add_new) {
            $accum_benfit_stat_names = DB::table('CLIENT')->insert(
                [
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

                ]
            );
            $benefitcode = DB::table('CLIENT')->where('client_id', 'like', '%' . $request->client_id . '%')
                ->where('customer_id', 'like', '%' . $request->customer_id . '%')->first();
            return $this->respondWithToken($this->token(), 'Added Successfully!', $benefitcode);
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
                    ]
                );


            $benefitcode = DB::table('CLIENT')->where('client_id', 'like', '%' . $request->client_id . '%')
                ->where('customer_id', 'like', '%' . $request->customer_id . '%')
                ->first();
            return $this->respondWithToken($this->token(), 'Updated Successfully!', $benefitcode);
        }
    }


    public function getClient(Request $request)
    {
        // $customerid = $request->customerid;
        // $customername = $request->customername;
        // $clientid = $request->clientid;
        // $clientname = $request->clientname;

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

        $this->respondWithToken($this->token() ?? '', '', $clients);
    }

    public function GetOneClient($clientid)
    {
        $client = DB::table('client')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where(DB::raw('UPPER(CLIENT_ID)'), 'like', '%' . strtoupper($clientid) . '%')
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
}
