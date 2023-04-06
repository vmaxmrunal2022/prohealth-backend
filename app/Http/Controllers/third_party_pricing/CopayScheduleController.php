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
             ->where('copay_schedule', 'like', '%' . $request->search. '%')
            // ->where('copay_schedule', 'like', '%' . strtoupper($request->search) . '%')
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
        if ($request->add_new == 1) {

            $check=DB::table('COPAY_SCHEDULE')
            ->where('copay_schedule',$request->copay_schedule)
            ->first();

            if($check){
            return $this->respondWithToken($this->token(), 'Copay Schedule ID Already Exists', $check);

            }else{
                $add_copay_schedule = DB::table('copay_schedule')
                ->insert([
                    'bga1_copay_amount'=> $request->bga1_copay_amount,
                    'bga1_copay_factor'=> $request->bga1_copay_factor,
                    'bga1_copay_matrix'=> $request->bga1_copay_matrix,
                    'bga1_copay_percent'=> $request->bga1_copay_percent,
                    'bga1_source'=> $request->bga1_source,
                    'bga1_type'=> $request->bga1_type,
                    'bga2_copay_amount'=> $request->bga2_copay_amount,
                    'bga2_copay_factor'=> $request->bga2_copay_factor,
                    'bga2_copay_matrix'=> $request->bga2_copay_matrix,
                    'bga2_copay_percent'=> $request->bga2_copay_percent,
                    'bga2_source'=> $request->bga2_source,
                    'bga2_type'=> $request->bga2_type,
                    'bga3_copay_amount'=> $request->bga3_copay_amount,
                    'bga3_copay_factor'=> $request->bga3_copay_factor,
                    'bga3_copay_matrix'=> $request->bga3_copay_matrix,
                    'bga3_copay_percent'=> $request->bga3_copay_percent,
                    'bga3_source'=> $request->bga3_source,
                    'bga3_type'=> $request->bga3_type,
                    'bga4_copay_amount'=> $request->bga4_copay_amount,
                    'bga4_copay_factor'=> $request->bga4_copay_factor,
                    'bga4_copay_matrix'=> $request->bga4_copay_matrix,
                    'bga4_copay_percent'=> $request->bga4_copay_percent,
                    'bga4_source'=> $request->bga4_source,
                    'bga4_type'=> $request->bga4_type,
                    'bga5_8_daw'=> $request->bga5_8_daw,
                    'bga5_copay_amount'=> $request->bga5_copay_amount,
                    'bga5_copay_factor'=> $request->bga5_copay_factor,
                    'bga5_copay_matrix'=> $request->bga5_copay_matrix,
                    'bga5_copay_percent'=> $request->bga5_copay_percent,
                    'bga5_source'=> $request->bga5_source,
                    'bga5_type'=> $request->bga5_type,
                    'bga6_copay_amount'=> $request->bga6_copay_amount,
                    'bga6_copay_factor'=> $request->bga6_copay_factor,
                    'bga6_copay_matrix'=> $request->bga6_copay_matrix,
                    'bga6_copay_percent'=> $request->bga6_copay_percent,
                    'bga6_source'=> $request->bga6_source,
                    'bga6_type'=> $request->bga6_type,
                    'bga7_copay_amount'=> $request->bga7_copay_amount,
                    'bga7_copay_factor'=> $request->bga7_copay_factor,
                    'bga7_copay_matrix'=> $request->bga7_copay_matrix,
                    'bga7_copay_percent'=> $request->bga7_copay_percent,
                    'bga7_source'=> $request->bga7_source,
                    'bga7_type'=> $request->bga7_type,
                    'bga8_copay_amount'=> $request->bga8_copay_amount,
                    'bga8_copay_factor'=> $request->bga8_copay_factor,
                    'bga8_copay_matrix'=> $request->bga8_copay_matrix,
                    'bga8_copay_percent'=> $request->bga8_copay_percent,
                    'bga8_source'=> $request->bga8_source,
                    'bga8_type'=> $request->bga8_type,
                    'bga9_12_daw'=> $request->bga9_12_daw,
                    'bga9_copay_amount'=> $request->bga9_copay_amount,
                    'bga9_copay_factor'=>$request->bga9_copay_factor,
                    'bga9_copay_matrix'=> $request->bga9_copay_matrix,
                    'bga9_copay_percent'=> $request->bga9_copay_percent,
                    'bga9_source'=> $request->bga9_source,
                    'bga9_type'=> $request->bga9_type,
                    'bga10_copay_amount'=> $request->bga10_copay_amount,
                    'bga10_copay_factor'=> $request->bga10_copay_factor,
                    'bga10_copay_matrix'=> $request->bga10_copay_matrix,
                    'bga10_copay_percent'=> $request->bga10_copay_percent,
                    'bga10_source'=> $request->bga10_source,
                    'bga10_type'=> $request->bga10_type,
                    'bga11_copay_amount'=> $request->bga11_copay_amount,
                    'bga11_copay_factor'=> $request->bga11_copay_factor,
                    'bga11_copay_matrix'=> $request->bga11_copay_matrix,
                    'bga11_copay_percent'=> $request->bga11_copay_percent,
                    'bga11_source'=> $request->bga11_source,
                    'bga11_type'=> $request->bga11_type,
                    'bga12_copay_amount'=> $request->bga12_copay_amount,
                    'bga12_copay_factor'=> $request->bga12_copay_factor,
                    'bga12_copay_matrix'=> $request->bga12_copay_matrix,
                    'bga12_copay_percent'=> $request->bga12_copay_percent,
                    'bga12_source'=> $request->bga12_source,
                    'bga12_type'=> $request->bga12_type,
                    'bga13_16_daw'=> $request->bga13_16_daw,
                    'bga13_copay_amount'=> $request->bga13_copay_amount,
                    'bga13_copay_factor'=> $request->bga13_copay_factor,
                    'bga13_copay_matrix'=> $request->bga13_copay_matrix,
                    'bga13_copay_percent'=> $request->bga13_copay_percent,
                    'bga13_source'=> $request->bga13_source,
                    'bga13_type'=> $request->bga13_type,
                    'bga14_copay_amount'=> $request->bga14_copay_amount,
                    'bga14_copay_factor'=> $request->bga14_copay_factor,
                    'bga14_copay_matrix'=> $request->bga14_copay_matrix,
                    'bga14_copay_percent'=> $request->bga14_copay_percent,
                    'bga14_source'=> $request->bga14_source,
                    'bga14_type'=> $request->bga14_type,
                    'bga15_copay_amount'=> $request->bga15_copay_amount,
                    'bga15_copay_factor'=> $request->bga15_copay_factor,
                    'bga15_copay_matrix'=> $request->bga15_copay_matrix,
                    'bga15_copay_percent'=> $request->bga15_copay_percent,
                    'bga15_source'=> $request->bga15_source,
                    'bga15_type'=> $request->bga15_type,
                    'bga16_copay_amount'=> $request->bga16_copay_amount,
                    'bga16_copay_factor'=>$request->bga16_copay_factor,
                    'bga16_copay_matrix'=> $request->bga16_copay_matrix,
                    'bga16_copay_percent'=> $request->bga16_copay_percent,
                    'bga16_source'=> $request->bga16_source,
                    'bga16_type'=> $request->bga16_type,
                    'bga_comparison_1_4'=> $request->bga_comparison_1_4,
                    'bga_comparison_5_8'=> $request->bga_comparison_5_8,
                    'bga_comparison_9_12'=> $request->bga_comparison_9_12,
                    'bga_comparison_13_16'=> $request->bga_comparison_13_16,
                    'bga_copay_modification_1_4'=> $request->bga_copay_modification_1_4,
                    'bga_copay_modification_5_8'=> $request->bga_copay_modification_5_8,
                    'bga_copay_modification_9_12'=> $request->bga_copay_modification_9_12,
                    'bga_copay_modification_13_16'=> $request->bga_copay_modification_13_16,
                    'bng1_copay_amount'=> $request->bng1_copay_amount,
                    'bng1_copay_factor'=> $request->bng1_copay_factor,
                    'bng1_copay_matrix'=> $request->bng1_copay_matrix,
                    'bng1_copay_percent'=> $request->bng1_copay_percent,
                    'bng1_source'=> $request->bng1_source,
                    'bng1_type'=> $request->bng1_type,
                    'bng2_copay_amount'=> $request->bng2_copay_amount,
                    'bng2_copay_factor'=> $request->bng2_copay_factor,
                    'bng2_copay_matrix'=> $request->bng2_copay_matrix,
                    'bng2_copay_percent'=> $request->bng2_copay_percent,
                    'bng2_source'=> $request->bng2_source,
                    'bng2_type'=> $request->bng2_type,
                    'bng3_copay_amount'=> $request->bng3_copay_amount,
                    'bng3_copay_factor'=> $request->bng3_copay_factor,
                    'bng3_copay_matrix'=> $request->bng3_copay_matrix,
                    'bng3_copay_percent'=> $request->bng3_copay_percent,
                    'bng3_source'=> $request->bng3_source,
                    'bng3_type'=> $request->bng3_type,
                    'bng4_copay_amount'=> $request->bng4_copay_amount,
                    'bng4_copay_factor'=> $request->bng4_copay_factor,
                    'bng4_copay_matrix'=> $request->bng4_copay_matrix,
                    'bng4_copay_percent'=> $request->bng4_copay_percent,
                    'bng4_source'=>$request->bng4_source,
                    'bng4_type'=> $request->bng4_type,
                    'bng_comparison'=> $request->bng_comparison,
                    'bng_copay_modification'=> $request->bng_copay_modification,
                    'coins_calc_opt'=> $request->coins_calc_opt,
                    'copay_schedule'=> $request->copay_schedule,
                    'copay_schedule_name'=> $request->copay_schedule_name,
                    'date_time_created'=> '',
                    'date_time_modified'=> '',
                    'gen1_copay_amount'=> $request->gen1_copay_amount,
                    'gen1_copay_factor'=> $request->gen1_copay_factor,
                    'gen1_copay_matrix'=> $request->gen1_copay_matrix,
                    'gen1_copay_percent'=> $request->gen1_copay_percent,
                    'gen1_source'=> $request->gen1_source,
                    'gen1_type'=> $request->gen1_type,
                    'gen2_copay_amount'=> $request->gen2_copay_amount,
                    'gen2_copay_factor'=> $request->gen2_copay_factor,
                    'gen2_copay_matrix'=> $request->gen2_copay_matrix,
                    'gen2_copay_percent'=> $request->gen2_copay_percent,
                    'gen2_source'=> $request->gen2_source,
                    'gen2_type'=> $request->gen2_type,
                    'gen3_copay_amount'=> $request->gen3_copay_amount,
                    'gen3_copay_factor'=> $request->gen3_copay_factor,
                    'gen3_copay_matrix'=> $request->gen3_copay_matrix,
                    'gen3_copay_percent'=> $request->gen3_copay_percent,
                    'gen3_source'=> $request->gen3_source,
                    'gen3_type'=> $request->gen3_type,
                    'gen4_copay_amount'=> $request->gen4_copay_amount,
                    'gen4_copay_factor'=> $request->gen4_copay_factor,
                    'gen4_copay_matrix'=> $request->gen4_copay_matrix,
                    'gen4_copay_percent'=> $request->gen4_copay_percent,
                    'gen4_source'=> $request->gen4_source,
                    'gen4_type'=> $request->gen4_type,
                    'gen_comparison'=> $request->gen_comparison,
                    'gen_copay_modification'=> $request->gen_copay_modification,
                    'bga1_4_daw'=>$request->bga1_4_daw,
                ]);

            return $this->respondWithToken($this->token(), 'Record Added Successfully', $add_copay_schedule);
                
            }
           
        } else if($request->add_new == 0) {
            $update_copay_schedule = DB::table('copay_schedule')
                ->where('copay_schedule', $request->copay_schedule)
                ->update([

                    'bga1_copay_amount'=> $request->bga1_copay_amount,
                    'bga1_copay_factor'=> $request->bga1_copay_factor,
                    'bga1_copay_matrix'=> $request->bga1_copay_matrix,
                    'bga1_copay_percent'=> $request->bga1_copay_percent,
                    'bga1_source'=> $request->bga1_source,
                    'bga1_type'=> $request->bga1_type,
                    'bga2_copay_amount'=> $request->bga2_copay_amount,
                    'bga2_copay_factor'=> $request->bga2_copay_factor,
                    'bga2_copay_matrix'=> $request->bga2_copay_matrix,
                    'bga2_copay_percent'=> $request->bga2_copay_percent,
                    'bga2_source'=> $request->bga2_source,
                    'bga2_type'=> $request->bga2_type,
                    'bga3_copay_amount'=> $request->bga3_copay_amount,
                    'bga3_copay_factor'=> $request->bga3_copay_factor,
                    'bga3_copay_matrix'=> $request->bga3_copay_matrix,
                    'bga3_copay_percent'=> $request->bga3_copay_percent,
                    'bga3_source'=> $request->bga3_source,
                    'bga3_type'=> $request->bga3_type,
                    'bga4_copay_amount'=> $request->bga4_copay_amount,
                    'bga4_copay_factor'=> $request->bga4_copay_factor,
                    'bga4_copay_matrix'=> $request->bga4_copay_matrix,
                    'bga4_copay_percent'=> $request->bga4_copay_percent,
                    'bga4_source'=> $request->bga4_source,
                    'bga4_type'=> $request->bga4_type,
                    'bga5_8_daw'=> $request->bga5_8_daw,
                    'bga5_copay_amount'=> $request->bga5_copay_amount,
                    'bga5_copay_factor'=> $request->bga5_copay_factor,
                    'bga5_copay_matrix'=> $request->bga5_copay_matrix,
                    'bga5_copay_percent'=> $request->bga5_copay_percent,
                    'bga5_source'=> $request->bga5_source,
                    'bga5_type'=> $request->bga5_type,
                    'bga6_copay_amount'=> $request->bga6_copay_amount,
                    'bga6_copay_factor'=> $request->bga6_copay_factor,
                    'bga6_copay_matrix'=> $request->bga6_copay_matrix,
                    'bga6_copay_percent'=> $request->bga6_copay_percent,
                    'bga6_source'=> $request->bga6_source,
                    'bga6_type'=> $request->bga6_type,
                    'bga7_copay_amount'=> $request->bga7_copay_amount,
                    'bga7_copay_factor'=> $request->bga7_copay_factor,
                    'bga7_copay_matrix'=> $request->bga7_copay_matrix,
                    'bga7_copay_percent'=> $request->bga7_copay_percent,
                    'bga7_source'=> $request->bga7_source,
                    'bga7_type'=> $request->bga7_type,
                    'bga8_copay_amount'=> $request->bga8_copay_amount,
                    'bga8_copay_factor'=> $request->bga8_copay_factor,
                    'bga8_copay_matrix'=> $request->bga8_copay_matrix,
                    'bga8_copay_percent'=> $request->bga8_copay_percent,
                    'bga8_source'=> $request->bga8_source,
                    'bga8_type'=> $request->bga8_type,
                    'bga9_12_daw'=> $request->bga9_12_daw,
                    'bga9_copay_amount'=> $request->bga9_copay_amount,
                    'bga9_copay_factor'=>$request->bga9_copay_factor,
                    'bga9_copay_matrix'=> $request->bga9_copay_matrix,
                    'bga9_copay_percent'=> $request->bga9_copay_percent,
                    'bga9_source'=> $request->bga9_source,
                    'bga9_type'=> $request->bga9_type,
                    'bga10_copay_amount'=> $request->bga10_copay_amount,
                    'bga10_copay_factor'=> $request->bga10_copay_factor,
                    'bga10_copay_matrix'=> $request->bga10_copay_matrix,
                    'bga10_copay_percent'=> $request->bga10_copay_percent,
                    'bga10_source'=> $request->bga10_source,
                    'bga10_type'=> $request->bga10_type,
                    'bga11_copay_amount'=> $request->bga11_copay_amount,
                    'bga11_copay_factor'=> $request->bga11_copay_factor,
                    'bga11_copay_matrix'=> $request->bga11_copay_matrix,
                    'bga11_copay_percent'=> $request->bga11_copay_percent,
                    'bga11_source'=> $request->bga11_source,
                    'bga11_type'=> $request->bga11_type,
                    'bga12_copay_amount'=> $request->bga12_copay_amount,
                    'bga12_copay_factor'=> $request->bga12_copay_factor,
                    'bga12_copay_matrix'=> $request->bga12_copay_matrix,
                    'bga12_copay_percent'=> $request->bga12_copay_percent,
                    'bga12_source'=> $request->bga12_source,
                    'bga12_type'=> $request->bga12_type,
                    'bga13_16_daw'=> $request->bga13_16_daw,
                    'bga13_copay_amount'=> $request->bga13_copay_amount,
                    'bga13_copay_factor'=> $request->bga13_copay_factor,
                    'bga13_copay_matrix'=> $request->bga13_copay_matrix,
                    'bga13_copay_percent'=> $request->bga13_copay_percent,
                    'bga13_source'=> $request->bga13_source,
                    'bga13_type'=> $request->bga13_type,
                    'bga14_copay_amount'=> $request->bga14_copay_amount,
                    'bga14_copay_factor'=> $request->bga14_copay_factor,
                    'bga14_copay_matrix'=> $request->bga14_copay_matrix,
                    'bga14_copay_percent'=> $request->bga14_copay_percent,
                    'bga14_source'=> $request->bga14_source,
                    'bga14_type'=> $request->bga14_type,
                    'bga15_copay_amount'=> $request->bga15_copay_amount,
                    'bga15_copay_factor'=> $request->bga15_copay_factor,
                    'bga15_copay_matrix'=> $request->bga15_copay_matrix,
                    'bga15_copay_percent'=> $request->bga15_copay_percent,
                    'bga15_source'=> $request->bga15_source,
                    'bga15_type'=> $request->bga15_type,
                    'bga16_copay_amount'=> $request->bga16_copay_amount,
                    'bga16_copay_factor'=>$request->bga16_copay_factor,
                    'bga16_copay_matrix'=> $request->bga16_copay_matrix,
                    'bga16_copay_percent'=> $request->bga16_copay_percent,
                    'bga16_source'=> $request->bga16_source,
                    'bga16_type'=> $request->bga16_type,
                    'bga_comparison_1_4'=> $request->bga_comparison_1_4,
                    'bga_comparison_5_8'=> $request->bga_comparison_5_8,
                    'bga_comparison_9_12'=> $request->bga_comparison_9_12,
                    'bga_comparison_13_16'=> $request->bga_comparison_13_16,
                    'bga_copay_modification_1_4'=> $request->bga_copay_modification_1_4,
                    'bga_copay_modification_5_8'=> $request->bga_copay_modification_5_8,
                    'bga_copay_modification_9_12'=> $request->bga_copay_modification_9_12,
                    'bga_copay_modification_13_16'=> $request->bga_copay_modification_13_16,
                    'bng1_copay_amount'=> $request->bng1_copay_amount,
                    'bng1_copay_factor'=> $request->bng1_copay_factor,
                    'bng1_copay_matrix'=> $request->bng1_copay_matrix,
                    'bng1_copay_percent'=> $request->bng1_copay_percent,
                    'bng1_source'=> $request->bng1_source,
                    'bng1_type'=> $request->bng1_type,
                    'bng2_copay_amount'=> $request->bng2_copay_amount,
                    'bng2_copay_factor'=> $request->bng2_copay_factor,
                    'bng2_copay_matrix'=> $request->bng2_copay_matrix,
                    'bng2_copay_percent'=> $request->bng2_copay_percent,
                    'bng2_source'=> $request->bng2_source,
                    'bng2_type'=> $request->bng2_type,
                    'bng3_copay_amount'=> $request->bng3_copay_amount,
                    'bng3_copay_factor'=> $request->bng3_copay_factor,
                    'bng3_copay_matrix'=> $request->bng3_copay_matrix,
                    'bng3_copay_percent'=> $request->bng3_copay_percent,
                    'bng3_source'=> $request->bng3_source,
                    'bng3_type'=> $request->bng3_type,
                    'bng4_copay_amount'=> $request->bng4_copay_amount,
                    'bng4_copay_factor'=> $request->bng4_copay_factor,
                    'bng4_copay_matrix'=> $request->bng4_copay_matrix,
                    'bng4_copay_percent'=> $request->bng4_copay_percent,
                    'bng4_source'=>$request->bng4_source,
                    'bng4_type'=> $request->bng4_type,
                    'bng_comparison'=> $request->bng_comparison,
                    'bng_copay_modification'=> $request->bng_copay_modification,
                    'coins_calc_opt'=> $request->coins_calc_opt,
                    'copay_schedule_name'=> $request->copay_schedule_name,
                    'date_time_created'=> '',
                    'date_time_modified'=> '',
                    'gen1_copay_amount'=> $request->gen1_copay_amount,
                    'gen1_copay_factor'=> $request->gen1_copay_factor,
                    'gen1_copay_matrix'=> $request->gen1_copay_matrix,
                    'gen1_copay_percent'=> $request->gen1_copay_percent,
                    'gen1_source'=> $request->gen1_source,
                    'gen1_type'=> $request->gen1_type,
                    'gen2_copay_amount'=> $request->gen2_copay_amount,
                    'gen2_copay_factor'=> $request->gen2_copay_factor,
                    'gen2_copay_matrix'=> $request->gen2_copay_matrix,
                    'gen2_copay_percent'=> $request->gen2_copay_percent,
                    'gen2_source'=> $request->gen2_source,
                    'gen2_type'=> $request->gen2_type,
                    'gen3_copay_amount'=> $request->gen3_copay_amount,
                    'gen3_copay_factor'=> $request->gen3_copay_factor,
                    'gen3_copay_matrix'=> $request->gen3_copay_matrix,
                    'gen3_copay_percent'=> $request->gen3_copay_percent,
                    'gen3_source'=> $request->gen3_source,
                    'gen3_type'=> $request->gen3_type,
                    'gen4_copay_amount'=> $request->gen4_copay_amount,
                    'gen4_copay_factor'=> $request->gen4_copay_factor,
                    'gen4_copay_matrix'=> $request->gen4_copay_matrix,
                    'gen4_copay_percent'=> $request->gen4_copay_percent,
                    'gen4_source'=> $request->gen4_source,
                    'gen4_type'=> $request->gen4_type,
                    'gen_comparison'=> $request->gen_comparison,
                    'gen_copay_modification'=> $request->gen_copay_modification,
                    'bga1_4_daw'=>$request->bga1_4_daw,

                    
                ]);

            return $this->respondWithToken($this->token(), 'Record Update Successfully', $update_copay_schedule);
        }
    }

    public function getDawOptions(Request $request)
    {
        $daw_options = [
            ['daw_id' => '0', 'daw_title' => 'No Product'],
            ['daw_id' => '1', 'daw_title' => 'Substitution Not Allowed By Presciber'],
            ['daw_id' => '2', 'daw_title' => 'Substitution Allowed => Patient Requested Product Dispensed'],
            ['daw_id' => '3', 'daw_title' => 'Substitution Allowed => Pharmacist Selected Product Dispensed'],
            ['daw_id' => '4', 'daw_title' => 'Substitution Allowed => Generic Drug Not In Stock'],
            ['daw_id' => '5', 'daw_title' => 'Substitution Allowed => Brand Drug Dispensed as a Generic'],
            ['daw_id' => '6', 'daw_title' => 'Override'],
            ['daw_id' => '7', 'daw_title' => 'Substitution not Allowed => Brand Drug Mandated By Law'],
            ['daw_id' => '8', 'daw_title' => 'Substitution Allowed => Generic Drug Not Available In Marketplace'],
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
