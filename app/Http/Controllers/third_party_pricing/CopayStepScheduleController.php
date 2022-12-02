<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopayStepScheduleController extends Controller
{
    public function get(Request $request)
    {
       if($request->search == 'days_supply')
       {
        $copayStepData = DB::table('COPAY_MATRIX')
                         ->where('DAYS_SUPPLY', '!=', 0)
                         ->get();
        
       }else{
        $copayStepData = DB::table('COPAY_MATRIX')
                         ->where('COST_MAX', '!=', 0)
                         ->get();
       }
       return $this->respondWithToken($this->token(), '', $copayStepData);
    }
}
