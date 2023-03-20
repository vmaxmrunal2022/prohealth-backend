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
            ->where('copay_schedule', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('copay_schedule_name', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $copayList);
    }
    public function getAll(Request $request)
    {
        $copayList = DB::table('COPAY_SCHEDULE')->get();
        return $this->respondWithToken($this->token(), '', $copayList);
    }

    public function getCopayData(Request $request)
    {
        $copay = DB::table('COPAY_SCHEDULE')
            ->where('copay_schedule', $request->search)
            ->first();

        return $this->respondWithToken($this->token(), '', $copay);
    }

    public function getSourceOptions(Request $request)
    {
        $copay_schedule_source = [
            [
                'source_id' => 'CALC', 'source_title' => 'Predefined Calculations(see type)',
                'type' => [
                    ['type_id' => 'BAL', 'type_title' => 'Remaining Balance',]
                ]
            ],
            [
                'source_id' => 'TOT', 'source_title' => 'Based on Calculated Amount',
                'type' => [
                    ['type_id' => 'INGCST', 'type_title' => 'Ingredient Cost Calculated'],
                    ['type_id' => 'PRICE', 'type_title' => 'Total Price Calculated'],
                    ['type_id' => 'PENALTY', 'type_title' => 'Penaulty Copay/ Coinsurance'],
                ]
            ],
            [
                'source_id' => 'TRX', 'source_title' => 'Inbound from the Transaction',
                'type' => [
                    ['type_id' => 'U&C', 'type_title' => 'Usual & Customary Charge Calculated by the System'],
                ],
            ]
        ];

        return $this->respondWithToken($this->token(), '', $copay_schedule_source);
    }

    public function getFactor(Request $request)
    {
        $get_factor = [
            ['factor_id' => '0', 'factor_title' => 'N/A'],
            ['factor_id' => '1', 'factor_title' => '1 per fill'],
        ];

        return $this->respondWithToken($this->token(), '', $get_factor);
    }

    public function getListOptions(Request $request)
    {
        $list_options = DB::table('COPAY_LIST')
            ->where(DB::raw('UPPER(COPAY_LIST)'), 'like', '%' . strtoupper($request->search . '%'))
            ->get();
        return $this->respondWithToken($this->token(), '', $list_options);
    }

    public function submitCopaySchedule(Request $request)
    {
        if ($request->add_new) {
            $add_copay_schedule = DB::table('copay_schedule')
                ->insert([
                    'copay_schedule' => $request->copay_schedule,
                    'copay_schedule_name' => $request->copay_schedule_name,
                    'COINS_CALC_OPT' => $request->coinsurance_calculation_option,

                    'BNG_COMPARISON' => $request->bng_comparision,
                    'BNG1_SOURCE' => $request->bng1_source,
                    'BNG1_TYPE' => $request->bng1_type,
                    'BNG1_COPAY_AMOUNT' => $request->bng1_copay_amount,
                    'BNG1_COPAY_PERCENT' => $request->bng1_copay_percent,
                    'BNG1_COPAY_FACTOR' => $request->bng1_copay_factor,
                    'BNG2_SOURCE' => $request->bng2_source,
                    'BNG2_TYPE' => $request->bng2_type,
                    'BNG2_COPAY_AMOUNT' => $request->bng2_copay_amount,
                    'BNG2_COPAY_PERCENT' => $request->bng2_copay_percent,
                    'BNG2_COPAY_FACTOR' => $request->bng2_copay_factor,
                    'BNG3_SOURCE' => $request->bng3_source,
                    'BNG3_TYPE' => $request->bng3_type,
                    'BNG3_COPAY_AMOUNT' => $request->bng3_copay_amount,
                    'BNG3_COPAY_PERCENT' => $request->bng3_copay_percent,
                    'BNG3_COPAY_FACTOR' => $request->bng3_copay_factor,
                    'BNG4_SOURCE' => $request->bng4_source,
                    'BNG4_TYPE' => $request->bng4_type,
                    'BNG4_COPAY_AMOUNT' => $request->bng4_copay_amount,
                    'BNG4_COPAY_PERCENT' => $request->bng4_copay_percent,
                    'BNG4_COPAY_FACTOR' => $request->bng4_copay_factor,

                    'BGA1_SOURCE' => $request->bga1_source,
                    'BGA1_TYPE' => $request->bga1_type,
                    'BGA1_COPAY_PERCENT' => $request->bga1_copay_percent,
                    'BGA1_COPAY_AMOUNT' => $request->bga1_copay_amount,
                    'BGA1_COPAY_FACTOR' =>  $request->bga1_copay_factor,
                    'BGA2_SOURCE' => $request->bga2_source,
                    'BGA2_TYPE' => $request->bga2_type,
                    'BGA2_COPAY_PERCENT' => $request->bga2_copay_percent,
                    'BGA2_COPAY_AMOUNT' => $request->bga2_copay_amount,
                    'BGA2_COPAY_FACTOR' =>  $request->bga2_copay_factor,
                    'BGA3_SOURCE' => $request->bga3_source,
                    'BGA3_TYPE' => $request->bga3_type,
                    'BGA3_COPAY_PERCENT' => $request->bga3_copay_percent,
                    'BGA3_COPAY_AMOUNT' => $request->bga3_copay_amount,
                    'BGA3_COPAY_FACTOR' =>  $request->bga3_copay_factor,
                    'BGA4_SOURCE' => $request->bga4_source,
                    'BGA4_TYPE' => $request->bga4_type,
                    'BGA4_COPAY_PERCENT' => $request->bga4_copay_percent,
                    'BGA4_COPAY_AMOUNT' => $request->bga4_copay_amount,
                    'BGA4_COPAY_FACTOR' =>  $request->bga4_copay_factor,
                    'BGA_COMPARISON_1_4' => $request->bga_comparision_1_4,
                    'BGA1_4_DAW' => $request->bgs1_4_daw,

                    'BGA5_SOURCE' => $request->bga5_source,
                    'BGA5_TYPE' => $request->bga5_type,
                    'BGA5_COPAY_PERCENT' => $request->bga5_copay_percent,
                    'BGA5_COPAY_AMOUNT' => $request->bga5_copay_amount,
                    'BGA5_COPAY_FACTOR' =>  $request->bga5_copay_factor,
                    'BGA6_SOURCE' => $request->bga6_source,
                    'BGA6_TYPE' => $request->bga6_type,
                    'BGA6_COPAY_PERCENT' => $request->bga6_copay_percent,
                    'BGA6_COPAY_AMOUNT' => $request->bga6_copay_amount,
                    'BGA6_COPAY_FACTOR' =>  $request->bga6_copay_factor,
                    'BGA7_SOURCE' => $request->bga7_source,
                    'BGA7_TYPE' => $request->bga7_type,
                    'BGA7_COPAY_PERCENT' => $request->bga7_copay_percent,
                    'BGA7_COPAY_AMOUNT' => $request->bga7_copay_amount,
                    'BGA7_COPAY_FACTOR' =>  $request->bga7_copay_factor,
                    'BGA8_SOURCE' => $request->bga8_source,
                    'BGA8_TYPE' => $request->bga8_type,
                    'BGA8_COPAY_PERCENT' => $request->bga8_copay_percent,
                    'BGA8_COPAY_AMOUNT' => $request->bga8_copay_amount,
                    'BGA8_COPAY_FACTOR' =>  $request->bga8_copay_factor,
                    'BGA_COMPARISON_5_8' => $request->bga_comparision_5_8,
                    'BGA5_8_DAW' => $request->bgs5_8_daw,

                    'BGA9_SOURCE' => $request->bga9_source,
                    'BGA9_TYPE' => $request->bga9_type,
                    'BGA9_COPAY_PERCENT' => $request->bga9_copay_percent,
                    'BGA9_COPAY_AMOUNT' => $request->bga9_copay_amount,
                    'BGA9_COPAY_FACTOR' =>  $request->bga9_copay_factor,
                    'BGA10_SOURCE' => $request->bga10_source,
                    'BGA10_TYPE' => $request->bga10_type,
                    'BGA10_COPAY_PERCENT' => $request->bga10_copay_percent,
                    'BGA10_COPAY_AMOUNT' => $request->bga10_copay_amount,
                    'BGA10_COPAY_FACTOR' =>  $request->bga10_copay_factor,
                    'BGA11_SOURCE' => $request->bga11_source,
                    'BGA11_TYPE' => $request->bga11_type,
                    'BGA11_COPAY_PERCENT' => $request->bga11_copay_percent,
                    'BGA11_COPAY_AMOUNT' => $request->bga11_copay_amount,
                    'BGA11_COPAY_FACTOR' =>  $request->bga11_copay_factor,
                    'BGA12_SOURCE' => $request->bga12_source,
                    'BGA12_TYPE' => $request->bga12_type,
                    'BGA12_COPAY_PERCENT' => $request->bga12_copay_percent,
                    'BGA12_COPAY_AMOUNT' => $request->bga12_copay_amount,
                    'BGA12_COPAY_FACTOR' =>  $request->bga12_copay_factor,
                    'BGA_COMPARISON_9_12' => $request->bga_comparision_9_12,
                    'BGA9_12_DAW' => $request->bgs9_12_daw,

                    'BGA13_SOURCE' => $request->bga13_source,
                    'BGA13_TYPE' => $request->bga13_type,
                    'BGA13_COPAY_PERCENT' => $request->bga13_copay_percent,
                    'BGA13_COPAY_AMOUNT' => $request->bga13_copay_amount,
                    'BGA13_COPAY_FACTOR' =>  $request->bga13_copay_factor,
                    'BGA14_SOURCE' => $request->bga14_source,
                    'BGA14_TYPE' => $request->bga14_type,
                    'BGA14_COPAY_PERCENT' => $request->bga14_copay_percent,
                    'BGA14_COPAY_AMOUNT' => $request->bga14_copay_amount,
                    'BGA14_COPAY_FACTOR' =>  $request->bga14_copay_factor,
                    'BGA15_SOURCE' => $request->bga15_source,
                    'BGA15_TYPE' => $request->bga15_type,
                    'BGA15_COPAY_PERCENT' => $request->bga15_copay_percent,
                    'BGA15_COPAY_AMOUNT' => $request->bga15_copay_amount,
                    'BGA15_COPAY_FACTOR' =>  $request->bga15_copay_factor,
                    'BGA16_SOURCE' => $request->bga16_source,
                    'BGA16_TYPE' => $request->bga16_type,
                    'BGA16_COPAY_PERCENT' => $request->bga16_copay_percent,
                    'BGA16_COPAY_AMOUNT' => $request->bga16_copay_amount,
                    'BGA16_COPAY_FACTOR' =>  $request->bga16_copay_factor,
                    'BGA_COMPARISON_13_16' => $request->bga_comparision_13_16,
                    'BGA13_16_DAW' => $request->bgs13_16_daw,
                ]);

            return $this->respondWithToken($this->token(), 'Added successfully!', $add_copay_schedule);
        } else {
            $update_copay_schedule = DB::table('copay_schedule')
                ->where('copay_schedule', $request->copay_schedule)
                ->update([
                    //'copay_schedule' => $request->copay_schedule,
                    'copay_schedule_name' => $request->copay_schedule_name,
                    'COINS_CALC_OPT' => $request->coinsurance_calculation_option,

                    'BNG_COMPARISON' => $request->bng_comparision,
                    'BNG1_SOURCE' => $request->bng1_source,
                    'BNG1_TYPE' => $request->bng1_type,
                    'BNG1_COPAY_AMOUNT' => $request->bng1_copay_amount,
                    'BNG1_COPAY_PERCENT' => $request->bng1_copay_percent,
                    'BNG1_COPAY_FACTOR' => $request->bng1_copay_factor,
                    'BNG2_SOURCE' => $request->bng2_source,
                    'BNG2_TYPE' => $request->bng2_type,
                    'BNG2_COPAY_AMOUNT' => $request->bng2_copay_amount,
                    'BNG2_COPAY_PERCENT' => $request->bng2_copay_percent,
                    'BNG2_COPAY_FACTOR' => $request->bng2_copay_factor,
                    'BNG3_SOURCE' => $request->bng3_source,
                    'BNG3_TYPE' => $request->bng3_type,
                    'BNG3_COPAY_AMOUNT' => $request->bng3_copay_amount,
                    'BNG3_COPAY_PERCENT' => $request->bng3_copay_percent,
                    'BNG3_COPAY_FACTOR' => $request->bng3_copay_factor,
                    'BNG4_SOURCE' => $request->bng4_source,
                    'BNG4_TYPE' => $request->bng4_type,
                    'BNG4_COPAY_AMOUNT' => $request->bng4_copay_amount,
                    'BNG4_COPAY_PERCENT' => $request->bng4_copay_percent,
                    'BNG4_COPAY_FACTOR' => $request->bng4_copay_factor,

                    'BGA1_SOURCE' => $request->bga1_source,
                    'BGA1_TYPE' => $request->bga1_type,
                    'BGA1_COPAY_PERCENT' => $request->bga1_copay_percent,
                    'BGA1_COPAY_AMOUNT' => $request->bga1_copay_amount,
                    'BGA1_COPAY_FACTOR' =>  $request->bga1_copay_factor,
                    'BGA2_SOURCE' => $request->bga2_source,
                    'BGA2_TYPE' => $request->bga2_type,
                    'BGA2_COPAY_PERCENT' => $request->bga2_copay_percent,
                    'BGA2_COPAY_AMOUNT' => $request->bga2_copay_amount,
                    'BGA2_COPAY_FACTOR' =>  $request->bga2_copay_factor,
                    'BGA3_SOURCE' => $request->bga3_source,
                    'BGA3_TYPE' => $request->bga3_type,
                    'BGA3_COPAY_PERCENT' => $request->bga3_copay_percent,
                    'BGA3_COPAY_AMOUNT' => $request->bga3_copay_amount,
                    'BGA3_COPAY_FACTOR' =>  $request->bga3_copay_factor,
                    'BGA4_SOURCE' => $request->bga4_source,
                    'BGA4_TYPE' => $request->bga4_type,
                    'BGA4_COPAY_PERCENT' => $request->bga4_copay_percent,
                    'BGA4_COPAY_AMOUNT' => $request->bga4_copay_amount,
                    'BGA4_COPAY_FACTOR' =>  $request->bga4_copay_factor,
                    'BGA_COMPARISON_1_4' => $request->bga_comparision_1_4,
                    'BGA1_4_DAW' => $request->bgs1_4_daw,

                    'BGA5_SOURCE' => $request->bga5_source,
                    'BGA5_TYPE' => $request->bga5_type,
                    'BGA5_COPAY_PERCENT' => $request->bga5_copay_percent,
                    'BGA5_COPAY_AMOUNT' => $request->bga5_copay_amount,
                    'BGA5_COPAY_FACTOR' =>  $request->bga5_copay_factor,
                    'BGA6_SOURCE' => $request->bga6_source,
                    'BGA6_TYPE' => $request->bga6_type,
                    'BGA6_COPAY_PERCENT' => $request->bga6_copay_percent,
                    'BGA6_COPAY_AMOUNT' => $request->bga6_copay_amount,
                    'BGA6_COPAY_FACTOR' =>  $request->bga6_copay_factor,
                    'BGA7_SOURCE' => $request->bga7_source,
                    'BGA7_TYPE' => $request->bga7_type,
                    'BGA7_COPAY_PERCENT' => $request->bga7_copay_percent,
                    'BGA7_COPAY_AMOUNT' => $request->bga7_copay_amount,
                    'BGA7_COPAY_FACTOR' =>  $request->bga7_copay_factor,
                    'BGA8_SOURCE' => $request->bga8_source,
                    'BGA8_TYPE' => $request->bga8_type,
                    'BGA8_COPAY_PERCENT' => $request->bga8_copay_percent,
                    'BGA8_COPAY_AMOUNT' => $request->bga8_copay_amount,
                    'BGA8_COPAY_FACTOR' =>  $request->bga8_copay_factor,
                    'BGA_COMPARISON_5_8' => $request->bga_comparision_1_4,
                    'BGA5_8_DAW' => $request->bgs5_8_daw,

                    'BGA9_SOURCE' => $request->bga9_source,
                    'BGA9_TYPE' => $request->bga9_type,
                    'BGA9_COPAY_PERCENT' => $request->bga9_copay_percent,
                    'BGA9_COPAY_AMOUNT' => $request->bga9_copay_amount,
                    'BGA9_COPAY_FACTOR' =>  $request->bga9_copay_factor,
                    'BGA10_SOURCE' => $request->bga10_source,
                    'BGA10_TYPE' => $request->bga10_type,
                    'BGA10_COPAY_PERCENT' => $request->bga10_copay_percent,
                    'BGA10_COPAY_AMOUNT' => $request->bga10_copay_amount,
                    'BGA10_COPAY_FACTOR' =>  $request->bga10_copay_factor,
                    'BGA11_SOURCE' => $request->bga11_source,
                    'BGA11_TYPE' => $request->bga11_type,
                    'BGA11_COPAY_PERCENT' => $request->bga11_copay_percent,
                    'BGA11_COPAY_AMOUNT' => $request->bga11_copay_amount,
                    'BGA11_COPAY_FACTOR' =>  $request->bga11_copay_factor,
                    'BGA12_SOURCE' => $request->bga12_source,
                    'BGA12_TYPE' => $request->bga12_type,
                    'BGA12_COPAY_PERCENT' => $request->bga12_copay_percent,
                    'BGA12_COPAY_AMOUNT' => $request->bga12_copay_amount,
                    'BGA12_COPAY_FACTOR' =>  $request->bga12_copay_factor,
                    'BGA_COMPARISON_9_12' => $request->bga_comparision_1_4,
                    'BGA9_12_DAW' => $request->bgs9_12_daw,

                    'BGA13_SOURCE' => $request->bga13_source,
                    'BGA13_TYPE' => $request->bga13_type,
                    'BGA13_COPAY_PERCENT' => $request->bga13_copay_percent,
                    'BGA13_COPAY_AMOUNT' => $request->bga13_copay_amount,
                    'BGA13_COPAY_FACTOR' =>  $request->bga13_copay_factor,
                    'BGA14_SOURCE' => $request->bga14_source,
                    'BGA14_TYPE' => $request->bga14_type,
                    'BGA14_COPAY_PERCENT' => $request->bga14_copay_percent,
                    'BGA14_COPAY_AMOUNT' => $request->bga14_copay_amount,
                    'BGA14_COPAY_FACTOR' =>  $request->bga14_copay_factor,
                    'BGA15_SOURCE' => $request->bga15_source,
                    'BGA15_TYPE' => $request->bga15_type,
                    'BGA15_COPAY_PERCENT' => $request->bga15_copay_percent,
                    'BGA15_COPAY_AMOUNT' => $request->bga15_copay_amount,
                    'BGA15_COPAY_FACTOR' =>  $request->bga15_copay_factor,
                    'BGA16_SOURCE' => $request->bga16_source,
                    'BGA16_TYPE' => $request->bga16_type,
                    'BGA16_COPAY_PERCENT' => $request->bga16_copay_percent,
                    'BGA16_COPAY_AMOUNT' => $request->bga16_copay_amount,
                    'BGA16_COPAY_FACTOR' =>  $request->bga16_copay_factor,
                    'BGA_COMPARISON_13_16' => $request->bga_comparision_13_16,
                    'BGA13_16_DAW' => $request->bgs13_16_daw,
                ]);

            return $this->respondWithToken($this->token(), 'Update successfully!', $update_copay_schedule);
        }
    }

    public function getDawOptions(Request $request)
    {
        $daw_options = [
            ['daw_id' => '0', 'daw_title' => 'No Product'],
            ['daw_id' => '1', 'daw_title' => 'Substitution Not Allowed By Presciber'],
            ['daw_id' => '2', 'daw_title' => 'Substitution Allowed : Patient Requested Product Dispensed'],
            ['daw_id' => '3', 'daw_title' => 'Substitution Allowed : Pharmacist Selected Product Dispensed'],
            ['daw_id' => '4', 'daw_title' => 'Substitution Allowed : Generic Drug Not In Stock'],
            ['daw_id' => '5', 'daw_title' => 'Substitution Allowed : Brand Drug Dispensed as a Generic'],
            ['daw_id' => '6', 'daw_title' => 'Override'],
            ['daw_id' => '7', 'daw_title' => 'Substitution not Allowed : Brand Drug Mandated By Law'],
            ['daw_id' => '8', 'daw_title' => 'Substitution Allowed : Generic Drug Not Available In Marketplace'],
            ['daw_id' => '9', 'daw_title' => 'Other'],
            ['daw_id' => '*', 'daw_title' => 'All Other DAW Codes Not Specially Defined'],
            ['daw_id' => 'N', 'daw_title' => 'Not Defined'],
        ];
        return $this->respondWithToken($this->token(), '', $daw_options);
    }

    public function getConinsuranceCalculationOption(Request $request)
    {
        $coinsurance_calculation = [
            ['cco_id' => 'T', 'cco_title' => 'Total Transaction Cost'],
            ['cco_id' => 'R', 'cco_title' => 'Transaction Cost Minus Copay'],
        ];

        return $this->respondWithToken($this->token(), '', $coinsurance_calculation);
    }
}
