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
            ->where('PRICE_SCHEDULE', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PRICE_SCHEDULE_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('COPAY_SCHEDULE', 'like', '%' . strtoupper($request->search) . '%')
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

    public function updateBrandItem(Request $request)
    {
        if ($request->has('new')) {
            $length = 10;
            echo substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);

            $add = DB::table('PRICE_SCHEDULE')
                ->insert(
                    [
                        'price_schedule' => $request->price_schedule,
                        'copay_schedule' => $request->copay_schedule,
                        'bng1_source' => $request->bng1_source,
                        'price_schedule_name' => $request->price_schedule_name,
                        'bng1_markup_percent' => $request->bng1_markup_percent,
                        'bng1_markup_amount' => $request->bng1_markup_amount,
                        'bng1_type' => $request->bng1_type,
                        'bng1_fee_percent' => $request->bng1_fee_percent,
                        'bng1_fee_amount' => $request->bng1_fee_amount,
                        'bng1_stdpkg' => $request->bng1_stdpkg,

                        'bga1_markup_percent' => $request->bga1_markup_percent,
                        'bga1_markup_amount' => $request->bga1_markup_amount,
                        'bga1_type' => $request->bga1_type,
                        'bga1_fee_percent' => $request->bga1_fee_percent,
                        'bga1_fee_amount' => $request->bga1_fee_amount,
                        'bga1_stdpkg' => $request->bga1_stdpkg,

                        'gen1_markup_percent' => $request->gen1_markup_percent,
                        'gen1_markup_amount' => $request->gen1_markup_amount,
                        'gen1_type' => $request->gen1_type,
                        'gen1_fee_percent' => $request->gen1_fee_percent,
                        'gen1_fee_amount' => $request->gen1_fee_amount,
                        'gen1_stdpkg' => $request->gen1_stdpkg,
                    ]
                );
            $add = DB::table('PRICE_SCHEDULE')->where('price_schedule', 'like', $request->price_schedule)->first();
            if ($add) {
                return $this->respondWithToken($this->token(), 'Added Successfully', $add);
            }
        } else {
            $update = DB::table('PRICE_SCHEDULE')
                ->where('price_schedule', 'like','%'.$request->price_schedule.'%')
                ->update(
                    [
                        'price_schedule_name' => $request->price_schedule_name,
                        'copay_schedule' => $request->copay_schedule,
                        'bng1_markup_percent' => $request->bng1_markup_percent,
                        'bng1_markup_amount' => $request->bng1_markup_amount,
                        'bng1_type' => $request->bng1_type,
                        'bng1_fee_percent' => $request->bng1_fee_percent,
                        'bng1_fee_amount' => $request->bng1_fee_amount,
                        'bng1_stdpkg' => $request->bng1_stdpkg,

                        'bga1_markup_percent' => $request->bga1_markup_percent,
                        'bga1_markup_amount' => $request->bga1_markup_amount,
                        'bga1_type' => $request->bga1_type,
                        'bga1_fee_percent' => $request->bga1_fee_percent,
                        'bga1_fee_amount' => $request->bga1_fee_amount,
                        'bga1_stdpkg' => $request->bga1_stdpkg,

                        'gen1_markup_percent' => $request->gen1_markup_percent,
                        'gen1_markup_amount' => $request->gen1_markup_amount,
                        'gen1_type' => $request->gen1_type,
                        'gen1_fee_percent' => $request->gen1_fee_percent,
                        'gen1_fee_amount' => $request->gen1_fee_amount,
                        'gen1_stdpkg' => $request->gen1_stdpkg,
                       
                    ]
                );
           
            if ($update) {
                $update = DB::table('price_schedule')->where('price_schedule', 'like', $request->price_schedule )->first();
                return $this->respondWithToken($this->token(), 'Updated Successfully', $update);
            }
        }
    }
}
