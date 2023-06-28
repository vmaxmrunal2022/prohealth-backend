<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaxScheduleController extends Controller
{
    //
    use AuditTrait;
    public function get(Request $request)
    {
        $taxData = DB::table('tax_schedule')
        // ->where('tax_schedule_id', 'like', '%' . $request->search. '%')
        ->whereRaw('LOWER(tax_schedule_id) LIKE ?', ['%' . strtolower($request->search) . '%'])
        ->orWhere('tax_schedule_name', 'like', '%' . strtoupper($request->search) . '%')->paginate(100);

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
            ['base_price_id' => 'F', 'base_price_title' => 'Ingredient Cost + Fee'],
            ['base_price_id' => 'I', 'base_price_title' => 'Ingredient Cost']
        ];
        return $this->respondWithToken($this->token(), '', $base_prices);
    }

    public function submitTaxSchedule(Request $request)
    {
        $validation = DB::table('tax_schedule')->where('tax_schedule_id', $request->tax_schedule_id)->get();

        if ($request->add_new == 1) {

            // if ($validation->count() > 0) {
            //     return $this->respondWithToken($this->token(), [['Tax Schedule ID is Already Exists']], $validation, 'false', 200, 1);
            // }
            $validator = Validator::make($request->all(), [
                'tax_schedule_id' => ['required', 'max:10', Rule::unique('tax_schedule')->where(function ($q) {
                    $q->whereNotNull('tax_schedule_id');
                })],
                'tax_schedule_name' => ['required', 'max:36'],
                
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }
            $add_tax_schedule = DB::table('tax_schedule')
                ->insert([
                    'tax_schedule_id' => $request->tax_schedule_id,
                    'tax_schedule_name' => $request->tax_schedule_name,
                    'RX_TAX_PERCENTAGE' => $request->rx_tax_percentage,
                    'RX_FLAT_TAX_AMOUNT' => $request->rx_flat_tax_amount,
                    'RX_TAX_CALCULATION' => $request->rx_tax_calculation,
                    'RX_TAX_BASE_PRICE' => $request->rx_tax_base_price,
                    'OTC_TAX_PERCENTAGE' => $request->otc_tax_percentage,
                    'OTC_FLAT_TAX_AMOUNT' => $request->otc_flat_tax_amount,
                    'OTC_TAX_CALCULATION' => $request->otc_tax_calculation,
                    'OTC_TAX_BASE_PRICE' => $request->otc_tax_base_price,
                ]);
            return $this->respondWithToken($this->token(), 'Record Added Successfully', $add_tax_schedule);
        } else if ($request->add_new == 0) {

           
            if ($validation->count() < 1) {
                return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
            }
            $validator = Validator::make($request->all(), [
                'tax_schedule_id' => ['required', 'max:10', Rule::unique('tax_schedule')->where(function ($q) use($request) {
                    $q->whereNotNull('tax_schedule_id');
                    $q->where('tax_schedule_id','!=', $request->tax_schedule_id);
                })],
                'tax_schedule_name' => ['required', 'max:36'],
                
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }
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
                    'OTC_FLAT_TAX_AMOUNT' => $request->otc_flat_tax_amount,
                    'OTC_TAX_CALCULATION' => $request->otc_tax_calculation,
                    'OTC_TAX_BASE_PRICE' => $request->otc_tax_base_price,
                ]);
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update_tax_schedule);
        }
    }


    public function tax_schedule_delete(Request $request)
    {
        if(isset($request->tax_schedule_id)){
            $all_exceptions_lists =  DB::table('tax_schedule')
                                        ->where('TAX_SCHEDULE_ID', $request->tax_schedule_id)
                                        ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
        
    }
}
