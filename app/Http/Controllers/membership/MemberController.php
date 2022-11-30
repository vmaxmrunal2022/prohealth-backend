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
            ->select('MEMBER.CUSTOMER_ID', 'MEMBER.MEMBER_ID', 'MEMBER.MEMBER_FIRST_NAME', 'MEMBER.MEMBER_LAST_NAME', 'MEMBER.EFFECTIVE_DATE_OVERRIDE', 'MEMBER.ELIG_VALIDATION_ID', 'MEMBER.ELIGIBILITY_OVRD', 'MEMBER.ELIG_LOCK_DATE', 'MEMBER.LOAD_PROCESS_DATE', 'MEMBER.PRIM_COVERAGE_INS_CARRIER', 'MEMBER.ADDRESS_1',
                      'MEMBER.ADDRESS_2', 'MEMBER.CITY', 'MEMBER.COUNTRY', 'MEMBER.DATE_OF_BIRTH', 'MEMBER.RELATIONSHIP', 'MEMBER.ANNIV_DATE', 'MEMBER.PATIENT_PIN_NUMBER', 'MEMBER.ALT_MEMBER_ID', 'MEMBER.SEX_OF_PATIENT',
                      'CUSTOMER.CUSTOMER_ID as cust_cust_id', 'CUSTOMER.CUSTOMER_NAME', 'CUSTOMER.EFFECTIVE_DATE as cust_eff_date', 'CUSTOMER.TERMINATION_DATE as cust_term_date',
                      'CLIENT.CUSTOMER_ID as client_cust_id', 'CLIENT.CLIENT_ID', 'CLIENT.CLIENT_NAME', 'CLIENT.EFFECTIVE_DATE as client_eff_date', 'CLIENT.TERMINATION_DATE as client_term_date',
                      'CLIENT_GROUP.CUSTOMER_ID as client_group_cust_id', 'CLIENT_GROUP.GROUP_NAME', 'CLIENT_GROUP.EFFECTIVE_DATE as client_group_eff_date', 'CLIENT_GROUP.GROUP_TERMINATION_DATE as client_group_term_date', 'CLIENT_GROUP.CLIENT_GROUP_ID',
                      )
            ->join('CUSTOMER', 'MEMBER.CUSTOMER_ID', '=', 'CUSTOMER.CUSTOMER_ID')
            ->join('CLIENT', 'MEMBER.CUSTOMER_ID', '=', 'CLIENT.CUSTOMER_ID')
            ->join('CLIENT_GROUP', 'MEMBER.CUSTOMER_ID', '=', 'CLIENT_GROUP.CUSTOMER_ID')
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
}
