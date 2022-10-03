<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    function saveIdentification(Request $request)
    {

        $customer = Customer::create([
            'CUSTOMER_ID' => $request->cutomerid,
            'CUSTOMER_NAME' => $request->name,
            'ADDRESS_1' => $request->address1,
            'ADDRESS_2' => $request->address2,
            'CITY' => $request->city,
            'COUNTRY' => $request->country,
            'ZIP_CODE' => $request->zip,
            'PHONE' => $request->phone,
            'FAX' => $request->fax,
            'EDI_ADDRESS' => $request->ediaddress,
            'CONTACT' => $request->contact,
            // 'TEST' => $request->test // COLUMN NOT AVAILABLE
            'CUSTOMER_TYPE' => $request->type,
            'EFFECTIVE_DATE' => $request->effectivedate,
            'TERMINATION_DATE' => $request->terminationdate,
            'POLICY_ANNIV_MONTH' => $request->policyannmonth,
            'POLICY_ANNIV_DAY' => $request->policyanday,
            'CENSUS_DATE' => $request->censusdate,
            'NUM_OF_ACTIVE_CONTRACTS' => $request->noofactivecontracts,
            'NUM_OF_ACTIVE_MEMBERS' => $request->noofactivemembers,
            'NUM_OF_TERMED_CONTRACTS' => $request->nooftermedcontracts,
            'NUM_OF_TERMED_MEMBERS' => $request->nooftermedmembers,
            'NUM_OF_PENDING_CONTRACTS' => $request->noofpendingcontracts,
            'NUM_OF_PENDING_MEMBERS' => $request->noofpendinngmembers,
            'COVERAGE_EFF_DATE_1' => $request->noofpendinngmembers,
            'COVERAGE_STRATEGY_ID_1' => '',
            'PLAN_ID_1' => '',
            'MISC_DATA_1' => '',
            'COVERAGE_EFF_DATE_2' => '',
            'COVERAGE_STRATEGY_ID_2' => '',
            'PLAN_ID_2' => '',
            'MISC_DATA_2' => '',
            'COVERAGE_EFF_DATE_3' => '',
            'COVERAGE_STRATEGY_ID_3' => '',
            'PLAN_ID_3' => '',
            'MISC_DATA_3' => ''
        ]);

        $this->respondWithToken($this->token(), 'Successfully added', $customer);
    }
}
