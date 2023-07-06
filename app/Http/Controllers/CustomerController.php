<?php

namespace App\Http\Controllers;


use App\Models\Customer;
use App\Models\User;
// use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
//use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Session\Session as SessionSession;
use Session;
use App\getUserData;
use App\getUserData1;
use App\Traits\AuditTrait;
use Illuminate\Support\Facades\Cache;

class CustomerController extends Controller
{
    use AuditTrait;
    public $customerIdPrefix = 'CN';
    public $customerIdMaxDigits = 4;
    // public $user_id;
    // protected $user;

    // public function __construct()
    // {
    //     $this->user = new User;
    //     $this->middleware('apisession');
    // }

    public  function saveIdentification(Request $request)
    {



        $customerREQUEST = $request->all();

        // dd($customerREQUEST);

        $customer = Customer::create([
            'CUSTOMER_ID' => $customerREQUEST['identification']['cutomerid'],
            'CUSTOMER_NAME' => $customerREQUEST['identification']['name'],
            'ADDRESS_1' => $customerREQUEST['identification']['address1'],
            'ADDRESS_2' => $customerREQUEST['identification']['address2'],
            'CITY' => $customerREQUEST['identification']['city'],
            // 'STATE' => $customerREQUEST['identification']['city'],
            'COUNTRY' => $customerREQUEST['identification']['country'],
            'ZIP_CODE' => $customerREQUEST['identification']['zip'],
            'ZIP_PLUS_2' => $customerREQUEST['identification']['zip'],
            'PHONE' => $customerREQUEST['identification']['phone'],
            'FAX' => $customerREQUEST['identification']['fax'],
            'EDI_ADDRESS' => $customerREQUEST['identification']['ediaddress'],
            'CONTACT' => $customerREQUEST['identification']['contact'],
            // 'TEST' => $request->test // COLUMN NOT AVAILABLE IN DB
            'CUSTOMER_TYPE' => $customerREQUEST['identification']['type'],
            'CAP_AMOUNT' => '',
            'COMM_CHARGE_PAID' => '',
            'COMM_CHARGE_REJECT' => '',
            'DATE_TIME_CREATED' => '',
            'USER_ID' => Cache::get('userId'),
            'DATE_TIME_MODIFIED' => '',
            'FORM_ID' => '',
            'PROCESSING_CYCLE' => '',
            'AUTO_TERM_DAYS' => '',
            'PRIOR_AUTH_BASIS_TYPE' => '',
            'NON_FORMULARY_BASIS_TYPE' => '',
            'PLAN_ID_REQUIRED' => '',
            'ADMIN_FEE' => $customerREQUEST['Exceptions']['admin_fee'],
            'DMR_FEE' => $customerREQUEST['Exceptions']['dmr_fee'],
            'ADMIN_PERCENT' => $customerREQUEST['Exceptions']['admin_percentage'],
            'OVERLAP_COVERAGE_TIE_BREAKER' => '',
            'OTHER_COV_PROC_FLAG' => '',
            'EFFECTIVE_DATE' => $customerREQUEST['identification']['effectivedate'],
            'TERMINATION_DATE' => $customerREQUEST['identification']['terminationdate'],
            'POLICY_ANNIV_MONTH' => $customerREQUEST['identification']['policyannmonth'],
            'POLICY_ANNIV_DAY' => $customerREQUEST['identification']['policyanday'],
            'CENSUS_DATE' => $customerREQUEST['identification']['censusdate'],
            'NUM_OF_ACTIVE_CONTRACTS' => $customerREQUEST['identification']['noofactivecontracts'],
            'NUM_OF_ACTIVE_MEMBERS' => $customerREQUEST['identification']['noofactivemembers'],
            'NUM_OF_TERMED_CONTRACTS' => $customerREQUEST['identification']['nooftermedcontracts'],
            'NUM_OF_TERMED_MEMBERS' => $customerREQUEST['identification']['nooftermedmembers'],
            'NUM_OF_PENDING_CONTRACTS' => $customerREQUEST['identification']['noofpendingcontracts'],
            'NUM_OF_PENDING_MEMBERS' => $customerREQUEST['identification']['noofpendinngmembers'],

            'ELIG_DATE_EDIT_OVR_FLAG' => '',
            'UCF_FEE' => $customerREQUEST['Exceptions']['ucf_claim_fee'],
            'ELIG_UPD_FEE' => $customerREQUEST['Exceptions']['elig_update_fee'],
            'PRIOR_AUTH_FEE' => $customerREQUEST['Exceptions']['prior_auth_fee'],
            'MAIL_ORD_LETTER_FEE' => $customerREQUEST['Exceptions']['mail_srv_ltr'],
            'MSG_PRIORITY_ID' => '',
            'DUR_EXCEPTION_LIST' => '',
            'MAX_NUM_TRANS_INTERIM_ELIG' => $customerREQUEST['Indicators']['max_no_of_transaction_allowed'],
            'MAX_DAYS_INTERIM_ELIG' => $customerREQUEST['Indicators']['max_no_of_days'],
            'STEP_THERAPY_ID' => '',

            'COVERAGE_EFF_DATE_1' => $customerREQUEST['strategy']['tier1'],
            // 'COVERAGE_STRATEGY_ID_1' => $customerREQUEST['strategy'][''],
            'PLAN_ID_1' => $customerREQUEST['strategy']['plan_id_1'],
            'MISC_DATA_1' => $customerREQUEST['strategy']['miscellaneous_1'],
            'COVERAGE_EFF_DATE_2' => $customerREQUEST['strategy']['tier2'],
            // 'COVERAGE_STRATEGY_ID_2' => $customerREQUEST['strategy'][''],
            'PLAN_ID_2' => $customerREQUEST['strategy']['plan_id_2'],
            'MISC_DATA_2' => $customerREQUEST['strategy']['miscellaneous_2'],
            'COVERAGE_EFF_DATE_3' => $customerREQUEST['strategy']['tier3'],
            // 'COVERAGE_STRATEGY_ID_3' => $customerREQUEST['strategy'][''],
            'PLAN_ID_3' => $customerREQUEST['strategy']['plan_id_3'],
            'MISC_DATA_3' => $customerREQUEST['strategy']['miscellaneous_3'],
            'PHARMACY_EXCEPTIONS_FLAG' => $customerREQUEST['strategy']['provider_vefification_option'],
            'SUPER_RX_NETWORK_ID' => $customerREQUEST['strategy']['super_provider_network'],
            'PRESCRIBER_EXCEPTIONS_FLAG' => $customerREQUEST['strategy']['Prescriber_Verification_Options_1'],
            'PRESCRIBER_EXCEPTIONS_FLAG_2' => $customerREQUEST['strategy']['Prescriber_Verification_Options_2'],
            // Prescriber_Grouping_id
            // Indicators
            // '' => $customerREQUEST['strategy']['Secondary_Coverage_indicator'],
            'AUTO_TERM_LEVEL' => $customerREQUEST['eligibility']['auto_termination_level'],
            'AUTO_FAM_MEMBER_TERM' => $customerREQUEST['eligibility']['auto_family_member_terminate'],
            'ELIGIBILITY_EXCEPTIONS_FLAG' => $customerREQUEST['eligibility']['eligibility_options'],
            'MEMBERSHIP_PROCESSING_FLAG' => $customerREQUEST['eligibility']['membership_processing_flag'],
            'ELIG_VALIDATION_ID' => $customerREQUEST['eligibility']['eligibility_validation_list'],

            'SUPER_MD_NETWORK_ID' => '',
            'MD_STRATEGY_ID' => '',
            'PHYSICIAN_TEMPLATE_ID' => '',
            'ACCUM_BENE_FAM_SUM_IND' => '',
            'USER_ID_CREATED' => $request->session()->get('user'),
            'DRUG_COV_STRATEGY_ID_1' => '',
            'PREF_MAINT_DRUG_STRATEGY_ID_1' => '',
            'PRICING_STRATEGY_ID_1' => '',
            'COPAY_STRATEGY_ID_1' => '',
            'ACCUM_BENE_STRATEGY_ID_1' => '',
            'DRUG_COV_STRATEGY_ID_2' => '',
            'PREF_MAINT_DRUG_STRATEGY_ID_2' => '',
            'PRICING_STRATEGY_ID_2' => '',
            'COPAY_STRATEGY_ID_2' => '',
            'ACCUM_BENE_STRATEGY_ID_2' => '',
            'DRUG_COV_STRATEGY_ID_3' => '',
            'PREF_MAINT_DRUG_STRATEGY_ID_3' => '',
            'PRICING_STRATEGY_ID_3' => '',
            'COPAY_STRATEGY_ID_3' => '',
            'ACCUM_BENE_STRATEGY_ID_3' => '',
            'GENERIC_CODE_CONV_ID' => '',
            'DATE_WRITTEN_TO_FIRST_FILL' => $customerREQUEST['Indicators']['no_of_days_to_first_fill'],
            'DATE_FILLED_TO_SUB_ONLINE' => $customerREQUEST['Indicators']['no_of_days_to_first_fill_submit'],
            'DATE_FILLED_TO_SUB_DMR' => $customerREQUEST['Indicators']['no_of_days_to_first_fill_submit_manual'],
            'DATE_SUB_TO_FILLED_FUTURE' => $customerREQUEST['Indicators']['no_of_days_from_date_filled_to_future'],
            'DAYS_FOR_REVERSALS' => $customerREQUEST['Indicators']['no_of_days_reversal'],
            'NON_PROFIT_TAX_EXEMPT_FLAG' => $customerREQUEST['Indicators']['tax_exempty_entity'],
            'REQD_U_AND_C_FLAG' => $customerREQUEST['Indicators']['mandatory_u_c'],
            'EXCL_PLAN_NDC_GPI_EXCEP_FLAG' => $customerREQUEST['Exceptions']['bypass_plan_ndc_gpi'],
            'EXCL_SYS_NDC_GPI_EXCEP_FLAG' => $customerREQUEST['Exceptions']['bypass_plan_ndc_gpi_exception_list_process'],
            'AUTH_XFER_IND' => $customerREQUEST['eligibility']['authorization_transfer'],
            'ELIG_TYPE' => $customerREQUEST['eligibility']['eligibility_type'],
            'MEMBER_CHANGE_LOG_OPT' => $customerREQUEST['eligibility']['membership_processing_willbe_done'],
            'PHYS_FILE_SRCE_ID' => '',
            'SHOEBOX_ACCESS' => '',
            'SMBPP' =>  $customerREQUEST['Exceptions']['smbpp'],
            'RVA_LIST_ID' => $customerREQUEST['Exceptions']['rva_list_id'],
            'COUNTRY_CODE' => '',
            'HISTORY_XFER_IND' => ''
        ]);



        $this->respondWithToken($this->token() ?? '', 'Record Added Successfully', $customer);
    }


    public function add(Request $request)
    {
        if ($request->add_new) {
            $validator = Validator::make($request->all(), [
                'customer_id' => ['required', 'max:10', Rule::unique('CUSTOMER')->where(function ($q) {
                    $q->whereNotNull('customer_id');
                })],
                'customer_name' => ['max:25'],
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
                $update_data = DB::table('CUSTOMER')->get();
                // dd($fieldsWithErrorMessagesArray);
                return $this->respondWithToken($this->token(), $fieldsWithErrorMessagesArray, $update_data, false);
            } else {
                $accum_benfit_stat_names = DB::table('CUSTOMER')->insert(
                    [
                        'customer_id' => $request->customer_id,
                        'customer_name' => strtoupper($request->customer_name),
                        'address_1' => $request->address_1,
                        'address_2' => $request->address_2,
                        'city' => $request->city,
                        'state' => $request->state,
                        'zip_code' => $request->zip_code,
                        'zip_plus_2' => $request->zip_plus_2,
                        'phone' => $request->phone,
                        'fax' => $request->fax,
                        'contact' => $request->contact,
                        'edi_address' => $request->edi_address,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'customer_type' => $request->customer_type,
                        'cap_amount' => $request->cap_amount,
                        'comm_charge_paid' => $request->comm_charge_paid,
                        'comm_charge_reject' => $request->comm_charge_reject,
                        'date_time_created' => date('Ymd'),
                        'date_time_modified' => date('Ymd'),
                        'user_id' => Cache::get('userId'),
                        'processing_cycle' => $request->processing_cycle,
                        'auto_term_days' => $request->auto_term_days,
                        'admin_fee' => $request->admin_fee,
                        'dmr_fee' => $request->dmr_fee,
                        'auto_term_level' => $request->auto_term_level,
                        'census_date' => date('Ymd', strtotime($request->census_date)),
                        'ucf_fee' => $request->ucf_fee,
                        'prior_auth_fee' => $request->prior_auth_fee,
                        'mail_ord_letter_fee' => $request->mail_ord_letter_fee,
                        'msg_priority_id' => $request->msg_priority_id,
                        'dur_exception_list' => $request->dur_exception_list,

                        'pharmacy_exceptions_flag' => $request->pharmacy_exceptions_flag,
                        'super_rx_network_id' => $request->super_rx_network_id,
                        'PRESCRIBER_EXCEPTIONS_FLAG' => $request->prescriber_exceptions_flag,
                        'PRESCRIBER_EXCEPTIONS_FLAG_2' => $request->prescriber_exceptions_flag_2,
                        'plan_id_1' => $request->plan_id_1,
                        'plan_id_2' => $request->plan_id_2,
                        'plan_id_3' => $request->plan_id_3,
                        'auto_term_level' => $request->auto_term_level,
                        'auto_fam_member_term' => $request->auto_fam_member_term,
                        'elig_type' => $request->elig_type,
                        'membership_processing_flag' => $request->membership_processing_flag,
                        'overlap_coverage_tie_breaker' => $request->overlap_coverage_tie_breaker,
                        'elig_date_edit_ovr_flag' => $request->elig_date_edit_ovr_flag,
                        'auth_xfer_ind' => $request->auth_xfer_ind,
                        'member_change_log_opt' => $request->member_change_log_opt,
                        'other_cov_proc_flag' => $request->other_cov_proc_flag,
                        'accum_bene_fam_sum_ind' => $request->accum_bene_fam_sum_ind,
                        'max_num_trans_interim_elig' => $request->max_num_trans_interim_elig,
                        'max_days_interim_elig' => $request->max_days_interim_elig,
                        'excl_plan_ndc_gpi_excep_flag' => $request->excl_plan_ndc_gpi_excep_flag,
                        'date_written_to_first_fill' => $request->date_written_to_first_fill,
                        'date_filled_to_sub_online' => $request->date_filled_to_sub_online,
                        'date_filled_to_sub_dmr' => $request->date_filled_to_sub_dmr,
                        'date_sub_to_filled_future' => $request->date_sub_to_filled_future,
                        'days_for_reversals' => $request->days_for_reversals,
                        'non_profit_tax_exempt_flag' => $request->non_profit_tax_exempt_flag,
                        'reqd_u_and_c_flag' => $request->reqd_u_and_c_flag,
                        'excl_sys_ndc_gpi_excep_flag' => $request->excl_sys_ndc_gpi_excep_flag,
                        'smbpp' => $request->smbpp,
                        'rva_list_id' => $request->rva_list_id,
                        'admin_fee' => $request->admin_fee,
                        'admin_percent' => $request->admin_percent,
                        'dmr_fee' => $request->dmr_fee,
                        'ucf_fee' => $request->ucf_fee,
                        'elig_upd_fee' => $request->elig_upd_fee,
                        'prior_auth_fee' => $request->prior_auth_fee,
                        'mail_ord_letter_fee' => $request->mail_ord_letter_fee,
                        'policy_anniv_month' => $request->policy_anniv_month,
                        'policy_anniv_day' => $request->policy_anniv_day,
                        'country' => $request->country,
                        'num_of_active_contracts' => $request->num_of_active_contracts,
                        'num_of_active_members' => $request->num_of_active_members,
                        'num_of_termed_contracts' => $request->num_of_termed_contracts,
                        'num_of_pending_members' => $request->num_of_pending_members,
                        'num_of_termed_members' => $request->num_of_termed_members,
                        'num_of_pending_contracts' => $request->num_of_pending_contracts,
                        'elig_validation_id' => $request->elig_validation_id,
                        'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                        'phys_file_srce_id' => strtoupper($request->phys_file_srce_id),

                        'coverage_eff_date_1' => $request->coverage_eff_date_1 ? date('Ymd', strtotime($request->coverage_eff_date_1)) : null,
                        'coverage_eff_date_2' =>  $request->coverage_eff_date_2 ? date('Ymd', strtotime($request->coverage_eff_date_2)) : null,
                        'coverage_eff_date_3' =>  $request->coverage_eff_date_3 ? date('Ymd', strtotime($request->coverage_eff_date_3)) : null,
                        'misc_data_1' => $request->misc_data_1,
                        'misc_data_2' => $request->misc_data_2,
                        'misc_data_3' => $request->misc_data_3,
                    ]
                );
                // $benefitcode = DB::table('CUSTOMER')
                //     ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
                //     ->first();
                $benefitcode = DB::table('CUSTOMER')->where('customer_id', $request->customer_id)->first();
                $updated_data = DB::table('CUSTOMER')->where('customer_id', 'like', '%' . $request->customer_id . '%')->get();
                //Audit 
                // $record_snapshot = implode('|', (array) $benefitcode);
                $record_snapshot = json_encode($benefitcode);
                $save_audit = $this->auditMethod('IN', $record_snapshot, 'PH_CUSTOMER');
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $updated_data);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'customer_id' => ['required', 'max:10'],
                'customer_name' => ['max:25'],
                'address_1' => ['max:25'],
                'address_2' => ['max:25'],
                'city' => ['max:18'],
                'state' => ['max:2'],
                'zip_code' => ['max:10'],
                'effective_date' => ['max:10'],
                'termination_date' => ['max:10', 'after:effective_date'],
            ]);
            if ($validator->fails()) {
                $update_data = DB::table('CUSTOMER')->get();
                $fieldsWithErrorMessagesArray = $validator->messages()->get('*');
                return $this->respondWithToken($this->token(), $fieldsWithErrorMessagesArray, $update_data,  false);
            } else {
                $accum_benfit_stat = DB::table('CUSTOMER')
                    ->where(DB::raw('UPPER(CUSTOMER_ID)'), strtoupper($request->customer_id))
                    ->update(
                        [
                            'customer_name' => strtoupper($request->customer_name),
                            'address_1' => $request->address_1,
                            'address_2' => $request->address_2,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip_code' => $request->zip_code,
                            'zip_plus_2' => $request->zip_plus_2,
                            'phone' => $request->phone,
                            'fax' => $request->fax,
                            'contact' => $request->contact,
                            'edi_address' => $request->edi_address,
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'termination_date' => date('Ymd', strtotime($request->termination_date)),
                            'customer_type' => $request->customer_type,
                            'cap_amount' => $request->cap_amount,
                            'comm_charge_paid' => $request->comm_charge_paid,
                            'comm_charge_reject' => $request->comm_charge_reject,
                            'date_time_modified' => date('Ymd'),
                            'user_id' => Cache::get('userId'),
                            'processing_cycle' => $request->processing_cycle,
                            'auto_term_days' => $request->auto_term_days,
                            'admin_fee' => $request->admin_fee,
                            'dmr_fee' => $request->dmr_fee,
                            'auto_term_level' => $request->auto_term_level,
                            // 'census_date' => date('Ymd', strtotime($request->census_date)),
                            'ucf_fee' => $request->ucf_fee,
                            'prior_auth_fee' => $request->prior_auth_fee,
                            'mail_ord_letter_fee' => $request->mail_ord_letter_fee,
                            'msg_priority_id' => $request->msg_priority_id,
                            'dur_exception_list' => $request->dur_exception_list,

                            'pharmacy_exceptions_flag' => $request->pharmacy_exceptions_flag,
                            'super_rx_network_id' => $request->super_rx_network_id,
                            'PRESCRIBER_EXCEPTIONS_FLAG' => $request->prescriber_exceptions_flag,
                            'PRESCRIBER_EXCEPTIONS_FLAG_2' => $request->prescriber_exceptions_flag_2,
                            'plan_id_1' => $request->plan_id_1,
                            'plan_id_2' => $request->plan_id_2,
                            'plan_id_3' => $request->plan_id_3,
                            'auto_term_level' => $request->auto_term_level,
                            'auto_fam_member_term' => $request->auto_fam_member_term,
                            'elig_type' => $request->elig_type,
                            'membership_processing_flag' => $request->membership_processing_flag,
                            'overlap_coverage_tie_breaker' => $request->overlap_coverage_tie_breaker,
                            'elig_date_edit_ovr_flag' => $request->elig_date_edit_ovr_flag,
                            'auth_xfer_ind' => $request->auth_xfer_ind,
                            'member_change_log_opt' => $request->member_change_log_opt,
                            'other_cov_proc_flag' => $request->other_cov_proc_flag,
                            'accum_bene_fam_sum_ind' => $request->accum_bene_fam_sum_ind,
                            'max_num_trans_interim_elig' => $request->max_num_trans_interim_elig,
                            'max_days_interim_elig' => $request->max_days_interim_elig,
                            'excl_plan_ndc_gpi_excep_flag' => $request->excl_plan_ndc_gpi_excep_flag,
                            'date_written_to_first_fill' => $request->date_written_to_first_fill,
                            'date_filled_to_sub_online' => $request->date_filled_to_sub_online,
                            'date_filled_to_sub_dmr' => $request->date_filled_to_sub_dmr,
                            'date_sub_to_filled_future' => $request->date_sub_to_filled_future,
                            'days_for_reversals' => $request->days_for_reversals,
                            'non_profit_tax_exempt_flag' => $request->non_profit_tax_exempt_flag,
                            'reqd_u_and_c_flag' => $request->reqd_u_and_c_flag,
                            'excl_sys_ndc_gpi_excep_flag' => $request->excl_sys_ndc_gpi_excep_flag,
                            'smbpp' => $request->smbpp,
                            'rva_list_id' => $request->rva_list_id,
                            'admin_fee' => $request->admin_fee,
                            'admin_percent' => $request->admin_percent,
                            'dmr_fee' => $request->dmr_fee,
                            'ucf_fee' => $request->ucf_fee,
                            'elig_upd_fee' => $request->elig_upd_fee,
                            'prior_auth_fee' => $request->prior_auth_fee,
                            'mail_ord_letter_fee' => $request->mail_ord_letter_fee,
                            'policy_anniv_month' => $request->policy_anniv_month,
                            'policy_anniv_day' => $request->policy_anniv_day,
                            'country' => $request->country,
                            'num_of_active_contracts' => $request->num_of_active_contracts,
                            'num_of_active_members' => $request->num_of_active_members,
                            'num_of_termed_contracts' => $request->num_of_termed_contracts,
                            'num_of_pending_members' => $request->num_of_pending_members,
                            'num_of_termed_members' => $request->num_of_termed_members,
                            'num_of_pending_contracts' => $request->num_of_pending_contracts,
                            'elig_validation_id' => $request->elig_validation_id,
                            'eligibility_exceptions_flag' => $request->eligibility_exceptions_flag,
                            'phys_file_srce_id' => strtoupper($request->phys_file_srce_id),

                            'coverage_eff_date_1' => $request->coverage_eff_date_1 ? date('Ymd', strtotime($request->coverage_eff_date_1)) : null,
                            'coverage_eff_date_2' =>  $request->coverage_eff_date_2 ? date('Ymd', strtotime($request->coverage_eff_date_2)) : null,
                            'coverage_eff_date_3' =>  $request->coverage_eff_date_3 ? date('Ymd', strtotime($request->coverage_eff_date_3)) : null,
                            'misc_data_1' => $request->misc_data_1,
                            'misc_data_2' => $request->misc_data_2,
                            'misc_data_3' => $request->misc_data_3,
                        ]
                    );
                $update_data = DB::table('CUSTOMER')->where('customer_id', 'like', '%' . $request->customer_id . '%')->get();

                $benefitcode_audit = DB::table('CUSTOMER')
                    ->where('customer_id', 'like', '%' . $request->customer_id . '%')->first();
                $record_snapshot = json_encode($benefitcode_audit);
                // $save_audit = $this->auditMethod('UP', $record_snapshot, 'CUSTOMER');
                $save_audit = DB::table('FE_RECORD_LOG')
                    ->insert([
                        'user_id' => Cache::get('userId'),
                        'date_created' => date('Ymd'),
                        'time_created' => date('gisA'),
                        'table_name' => 'PH_CUSTOMER',
                        'record_action' => 'UP',
                        'application' => 'ProPBM',
                        // 'record_snapshot' => $request->client_id . '-' . $record_snapshot,
                        'record_snapshot' => $record_snapshot,
                    ]);

                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update_data);
                // return $this->respondWithToken($this->token(), auth('web')->user(), $benefitcode);
            }
        }
    }

    public function generateCustomerId()
    {
        $total = Customer::count() + 1;
        $id = $this->customerIdPrefix . sprintf("%0" . $this->customerIdMaxDigits . "d", $total);
        // $newid = $this->_generateAndvalidate();

        // while ($newid) {
        //     $id = $newid;
        // }

        return $id;
    }
    public function searchCutomer(Request $request)
    {
        $customer = DB::table('customer')
            ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where(DB::raw('UPPER(CUSTOMER_ID)'), 'like', '%' . strtoupper($request->customerid) . '%')
            // ->orWhere(DB::raw('UPPER(CUSTOMER_NAME)'), 'like', '%' . strtoupper($request->customerid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $customer);
    }

    public function getPlanId($planid)
    {
        $customer = DB::table('plan_table_extensions')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where('PLAN_ID', 'like', '%' . strtoupper($planid) . '%')
            ->first();

        // dd($customer);

        return $this->respondWithToken($this->token(), '', $customer);
    }



    public function searchPlanId(Request $request)
    {
        $priceShedule = DB::table('plan_table_extensions')
            ->where('PLAN_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $priceShedule);
    }





    public function searchSuperProviderNetworkId(Request $request)
    {
        // dd($request);
        $customer = DB::table('super_rx_network_names')
            ->where('SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->rva_list_id) . '%')
            ->orWhere('SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->rva_list_id) . '%')
            ->first();
        return $this->respondWithToken($this->token(), '', $customer);
    }

    public function ALLSuperProviderNetworkIdS(Request $request)
    {
        $data = DB::table('super_rx_network_names')->paginate(100);


        return $this->respondWithToken($this->token(), '', $data);
    }




    public function GetCustomer($customerid)
    {
        $customer = DB::table('customer')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where(DB::raw('UPPER(CUSTOMER_ID)'), strtoupper($customerid))
            ->first();
        return $this->respondWithToken($this->token(), '', $customer);
    }

    public function deleteCutomer(Request $request)
    {
        //CUSTOMER
        $customer = DB::table('customer')
            ->where('customer_id', $request->customer_id)
            ->first();
        //save audit
        $record_snapshot = json_encode($customer);
        $save_audit = $this->auditMethod('DE', $record_snapshot, 'PH_CUSTOMER');
        $delete_customer = DB::table('customer')
            ->where('customer_id', $request->customer_id)
            ->delete();

        //CLIENT
        $client  = DB::table('CLIENT')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->get();
        for ($i = 0; $i < count($client); $i++) {
            $record_snapshot_client = json_encode($client[$i]);
            $save_audit = $this->auditMethod('DE', $record_snapshot_client, 'CLIENT');
        }
        $delete_client  = DB::table('CLIENT')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->delete();

        //CLIENT GROUP
        $client_group = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->get();
        for ($i = 0; $i < count($client_group); $i++) {
            $record_snapshot = json_encode($client_group[$i]);
            $save_audit = $this->auditMethod('DE', $record_snapshot, 'CLIENT_GROUP');
        }
        $delete_client_group  = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
            ->delete();


        return $this->respondWithToken($this->token(), "Record Deleted Successfully", '');
    }
}
