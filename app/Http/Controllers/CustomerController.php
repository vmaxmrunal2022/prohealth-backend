<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public $customerIdPrefix = 'CN';
    public $customerIdMaxDigits = 4;

    public  function saveIdentification(Request $request)
    {
        // return response([], 200);


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
            'USER_ID' => '',
            'DATE_TIME_MODIFIED' => '',
            'FORM_ID' => '',
            'PROCESSING_CYCLE' => '',
            'AUTO_TERM_DAYS' => '',
            'PRIOR_AUTH_BASIS_TYPE' => '',
            'NON_FORMULARY_BASIS_TYPE' => '',
            'PLAN_ID_REQUIRED' => '',
            'ADMIN_FEE' => $customerREQUEST['Exceptions']['admin_fee'],
            'DMR_FEE' => $customerREQUEST['Exceptions']['dmr_fee'],
            'ADMIN_PERCENT' => $customerREQUEST['Exceptions']['admin_percent'],
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
            'UCF_FEE' => $customerREQUEST['Exceptions']['ucf_fee'],
            'ELIG_UPD_FEE' => $customerREQUEST['Exceptions']['elig_upd_fee'],
            'PRIOR_AUTH_FEE' => $customerREQUEST['Exceptions']['prior_auth_fee'],
            'MAIL_ORD_LETTER_FEE' => $customerREQUEST['Exceptions']['mail_ord_letter_fee'],
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
            'USER_ID_CREATED' => '',
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
            'DATE_WRITTEN_TO_FIRST_FILL' => $customerREQUEST['Indicators']['date_written_to_first_fill'],
            'DATE_FILLED_TO_SUB_ONLINE' => $customerREQUEST['Indicators']['date_filled_to_sub_online'],
            'DATE_FILLED_TO_SUB_DMR' => $customerREQUEST['Indicators']['date_filled_to_sub_dmr'],
            'DATE_SUB_TO_FILLED_FUTURE' => $customerREQUEST['Indicators']['date_sub_to_filled_future'],
            'DAYS_FOR_REVERSALS' => $customerREQUEST['Indicators']['days_for_reversals'],
            'NON_PROFIT_TAX_EXEMPT_FLAG' => $customerREQUEST['Indicators']['non_profit_tax_exempt_flag'],
            'REQD_U_AND_C_FLAG' => $customerREQUEST['Indicators']['reqd_u_and_c_flag'],
            'EXCL_PLAN_NDC_GPI_EXCEP_FLAG' => $customerREQUEST['Exceptions']['excl_plan_ndc_gpi_excep_flag'],
            'EXCL_SYS_NDC_GPI_EXCEP_FLAG' => $customerREQUEST['Exceptions']['excl_sys_ndc_gpi_excep_flag'],
            'AUTH_XFER_IND' => $customerREQUEST['eligibility']['auth_xfer_ind'],
            'ELIG_TYPE' => $customerREQUEST['eligibility']['eligibility_type'],
            'MEMBER_CHANGE_LOG_OPT' => $customerREQUEST['eligibility']['membership_processing_willbe_done'],
            'PHYS_FILE_SRCE_ID' => '',
            'SHOEBOX_ACCESS' => '',
            'SMBPP' =>  $customerREQUEST['Exceptions']['smbpp'],
            'RVA_LIST_ID' => $customerREQUEST['Exceptions']['rva_list_id'],
            'COUNTRY_CODE' => '',
            'HISTORY_XFER_IND' => ''
        ]);



        $this->respondWithToken($this->token() ?? '', 'Successfully added', $customer);
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
            ->where('CUSTOMER_ID', 'like', '%' . strtoupper($request->customerid) . '%')
            ->orWhere('CUSTOMER_NAME', 'like', '%' . strtoupper($request->customerid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $customer);
    }

    public function searchPlanId($planid)
    {
        $customer = DB::table('plan_table_extensions')
        // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
        ->where('PLAN_ID', 'like', '%' . strtoupper($planid) . '%')
        ->first();

        // dd($customer);

    return $this->respondWithToken($this->token(),'', $customer);
    }

    public function searchSuperProviderNetworkId(Request $request)
    {
        // dd($request);
        $customer = DB::table('super_rx_network_names')
        ->where('SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->rva_list_id) . '%')
        ->orWhere('SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->rva_list_id) . '%')
        ->first();
    return $this->respondWithToken($this->token(),'', $customer);
    }

    public function GetCustomer($customerid)
    {
        $customer = DB::table('customer')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where('CUSTOMER_ID', 'like', '%' . strtoupper($customerid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $customer);
    }
}
