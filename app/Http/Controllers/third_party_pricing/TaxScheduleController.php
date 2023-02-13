<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxScheduleController extends Controller
{
    //
    public function get(Request $request)
    {
        $taxData = DB::table('tax_schedule')->get();

        return $this->respondWithToken($this->token(), '', $taxData);
    }

    public function getCalculations(Request $request)
    {
        $calculations = [
            ['cal_id' => 'F', 'cal_title' => 'Flat Rate'],
            ['cal_id' => 'P', 'cal_title' => 'Percentage'],
            ['cal_id' => 'R', 'cal_title' => 'Percentage + Flat Rate'],
            ['cal_id' => 'C', 'cal_title' => 'Calculated Percentage'],
        ];
        return $this->respondWithToken($this->token(), '', $calculations);
    }

    public function getBasePrices(Request $request)
    {
        $base_prices = [
            ['base_price_id' => 'F', 'base_pricew_title' => 'Ingredient Cost + Fee'],
            ['base_price_id' => 'I', 'base_price_title' => 'Ingredient Cost']
        ];
        return $this->respondWithToken($this->token(), '', $base_prices);
    }

    public function submitTaxSchedule(Request $request)
    {
        $validation = DB::table('tax_schedule')->where('tax_schedule_id', $request->tax_schedule_id)->get()->count();

        if ($validation <= "0") {
            if ($request->add_new) {
                $add_tax_schedule = DB::table('tax_schedule')
                    ->insert([
                        'tax_schedule_id' => $request->tax_schedule_id,
                        'tax_schedule_name' => $request->tax_schedule_name,
                        'RX_TAX_PERCENTAGE' => $request->rx_tax_percentage,
                        'RX_FLAT_TAX_AMOUNT' => $request->rx_flat_tax_amount,
                        'RX_TAX_CALCULATION' => $request->rx_tax_calculation,
                        'RX_TAX_BASE_PRICE' => $request->rx_tax_base_price,
                        'OTC_TAX_PERCENTAGE' => $request->otc_tax_percentage,
                        'OTC_FLAT_TAX_AMOUNT' => $request->otc_flat_tx_amount,
                        'OTC_TAX_CALCULATION' => $request->otc_tax_calculation,
                        'OTC_TAX_BASE_PRICE' => $request->otc_tax_base_price,
                    ]);
                return $this->respondWithToken($this->token(), 'Added Successfully!', $add_tax_schedule);
            }
        } else {
            $update_tax_schedule = DB::table('tax_schedule')
                ->where('tax_schedule_id', $request->tax_schedule_id)
                ->update([
                    // 'tax_schedule_id' => $request->tax_schedule_id,
                    'tax_schedule_name' => $request->tax_schedule_name,
                    'RX_TAX_PERCENTAGE' => $request->rx_tax_percentage,
                    'RX_FLAT_TAX_AMOUNT' => $request->rx_flat_tax_amount,
                    'RX_TAX_CALCULATION' => $request->rx_tax_calculation,
                    'RX_TAX_BASE_PRICE' => $request->rx_tax_base_price,
                    'OTC_TAX_PERCENTAGE' => $request->otc_tax_percentage,
                    'OTC_FLAT_TAX_AMOUNT' => $request->otc_flat_tx_amount,
                    'OTC_TAX_CALCULATION' => $request->otc_tax_calculation,
                    'OTC_TAX_BASE_PRICE' => $request->otc_tax_base_price,
                ]);
            return $this->respondWithToken($this->token(), 'Updated Successfully!', $update_tax_schedule);
        }
    }
}
