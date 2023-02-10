<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LDAP\Result;

class CopayScheduleController extends Controller
{
    public function get(Request $request)
    {
        $copayList = DB::table('COPAY_SCHEDULE')
                     ->where('copay_schedule', 'like', '%'.strtoupper($request->search).'%')
                     ->orWhere('copay_schedule_name' ,'like', '%'.strtoupper($request->search).'%')
                     ->get();

        return $this->respondWithToken($this->token(), '', $copayList);
    }

    public function getCopayData(Request $request)
    {
        $copay = DB::table('COPAY_SCHEDULE')
                 ->where('copay_schedule', $request->search)
                 ->first();

        return $this->respondWithToken($this->token(), '', $copay);
    }
}
