<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceScheduleController extends Controller
{
    public function get(Request $request)
    {
        $priceShedule = DB::table('PRICE_SCHEDULE')
                        ->where('PRICE_SCHEDULE', 'like', '%'.strtoupper($request->search). '%')
                        ->orWhere('PRICE_SCHEDULE_NAME', 'like', '%'. strtoupper($request->search). '%')
                        ->orWhere('COPAY_SCHEDULE', 'like', '%'. strtoupper($request->search). '%')
                        ->get();
        return $this->respondWithToken($this->token(), '', $priceShedule);
    }

    public function getPriceScheduleDetails(Request $erquest)
    {
        $priceScheduleRow = DB::table('PRICE_SCHEDULE')
                            ->where('PRICE_SCHEDULE', $erquest->search)
                            ->first();

        return $this->respondWithToken($this->token(), '', $priceScheduleRow);
    }
}
