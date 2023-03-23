<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker\Extension\Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PriceScheduleController extends Controller
{
    public function getAll(Request $request)
    {
        $priceShedule = DB::table('PRICE_SCHEDULE')->get();
        return $this->respondWithToken($this->token(), '', $priceShedule);
    }

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

    // public function updateBrandItem(Request $request)
    // {
    //     if ($request->has('new')) {
    //         $length = 10;
    //         echo substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);

    //         $add = DB::table('PRICE_SCHEDULE')
    //             ->insert(
    //                 [
    //                     'price_schedule' => $request->price_schedule,
    //                     'copay_schedule' => $request->copay_schedule,
    //                     'bng1_source' => $request->bng1_source,
    //                     'price_schedule_name' => $request->price_schedule_name,
    //                     'bng1_markup_percent' => $request->bng1_markup_percent,
    //                     'bng1_markup_amount' => $request->bng1_markup_amount,
    //                     'bng1_type' => $request->bng1_type,
    //                     'bng1_fee_percent' => $request->bng1_fee_percent,
    //                     'bng1_fee_amount' => $request->bng1_fee_amount,
    //                     'bng1_stdpkg' => $request->bng1_stdpkg,

    //                     // 'bga1_markup_percent' => $request->bga1_markup_percent,
    //                     // 'bga1_markup_amount' => $request->bga1_markup_amount,
    //                     // 'bga1_type' => $request->bga1_type,
    //                     // 'bga1_fee_percent' => $request->bga1_fee_percent,
    //                     // 'bga1_fee_amount' => $request->bga1_fee_amount,
    //                     // 'bga1_stdpkg' => $request->bga1_stdpkg,

    //                     // 'gen1_markup_percent' => $request->gen1_markup_percent,
    //                     // 'gen1_markup_amount' => $request->gen1_markup_amount,
    //                     // 'gen1_type' => $request->gen1_type,
    //                     // 'gen1_fee_percent' => $request->gen1_fee_percent,
    //                     // 'gen1_fee_amount' => $request->gen1_fee_amount,
    //                     // 'gen1_stdpkg' => $request->gen1_stdpkg,
    //                 ]
    //             );
    //         $add = DB::table('PRICE_SCHEDULE')->where('price_schedule', 'like', $request->price_schedule)->first();
    //         if ($add) {
    //             return $this->respondWithToken($this->token(), 'Added Successfully', $add);
    //         }
    //     } else {
    //         $update = DB::table('PRICE_SCHEDULE')
    //             ->where('price_schedule', 'like', '%' . $request->price_schedule . '%')
    //             ->update(
    //                 [
    //                     'price_schedule_name' => $request->price_schedule_name,
    //                     'copay_schedule' => $request->copay_schedule,
    //                     'bng1_markup_percent' => $request->bng1_markup_percent,
    //                     'bng1_markup_amount' => $request->bng1_markup_amount,
    //                     'bng1_type' => $request->bng1_type,
    //                     'bng1_fee_percent' => $request->bng1_fee_percent,
    //                     'bng1_fee_amount' => $request->bng1_fee_amount,
    //                     'bng1_stdpkg' => $request->bng1_stdpkg,

    //                     // 'bga1_markup_percent' => $request->bga1_markup_percent,
    //                     // 'bga1_markup_amount' => $request->bga1_markup_amount,
    //                     // 'bga1_type' => $request->bga1_type,
    //                     // 'bga1_fee_percent' => $request->bga1_fee_percent,
    //                     // 'bga1_fee_amount' => $request->bga1_fee_amount,
    //                     // 'bga1_stdpkg' => $request->bga1_stdpkg,

    //                     // 'gen1_markup_percent' => $request->gen1_markup_percent,
    //                     // 'gen1_markup_amount' => $request->gen1_markup_amount,
    //                     // 'gen1_type' => $request->gen1_type,
    //                     // 'gen1_fee_percent' => $request->gen1_fee_percent,
    //                     // 'gen1_fee_amount' => $request->gen1_fee_amount,
    //                     // 'gen1_stdpkg' => $request->gen1_stdpkg,

    //                 ]
    //             );

    //         if ($update) {
    //             $update = DB::table('price_schedule')->where('price_schedule', 'like', $request->price_schedule)->first();
    //             return $this->respondWithToken($this->token(), 'Updated Successfully', $update);
    //         }
    //     }
    // }

    public function getBrandType(Request $request)
    {
        $brand_type = [
            'CALC' => 'Predefined Calculator (see type)',
            'FDB' => 'First Data Bank',
            'MDS' => 'MediSpan',
            'NATL' => 'National',
            'PLAN' => 'Set by the Plan',
            'TRX' => 'Inbound from the Provider',
            'USR' => 'User Defined',
            'U&C' => 'Usual & Customer Retail Charge',
        ];

        $new_array = [];
        $r = Helper::searchElement('~' . $request->search . '~', $brand_type);
        foreach ($r as $key => $value) {
            $arr = ['type_id' => $key, 'type_title' => $value];
            array_push($new_array, $arr);
        }
        return $this->respondWithToken($this->token(), '', $new_array);
    }



    public function getBrandSource(Request $request)
    {
        $brand_source = [
            //   ['source_id' => 'U&C', 'source_title' => 'Usual & Customer Retail Charge'],
            [
                'source_id' => 'CALC', 'source_title' => 'Predefined Calculator (see type)',
                'type' => [
                    ['type_id' => 'AWP', 'type_title' => 'Average Wholesale Price'],
                    ['type_id' => 'DIR', 'type_title' => 'Published Direct Price'],
                    ['type_id' => 'FFP', 'type_title' => 'Fed Financial Participation(MAC)'],
                    ['type_id' => 'WAC', 'type_title' => 'Wholesale Acquisition Cost']
                ]
            ],
            [
                'source_id' => 'FDB', 'source_title' => 'First Data Bank',
                'type' => [
                    ['type_id' => 'AWP', 'type_title' => 'Average Wholesale Price'],
                    ['type_id' => 'DIR', 'type_title' => 'Published Direct Price'],
                    ['type_id' => 'FFP', 'type_title' => 'Fed Financial Participation(MAC)'],
                    ['type_id' => 'WAC', 'type_title' => 'Wholesale Acquisition Cost']
                ]
            ],
            [
                'source_id' => 'MDS', 'source_title' => 'MediSpan',
                'type' => [
                    ['type_id' => 'AWP', 'type_title' => 'Average Wholesale Price'],
                    ['type_id' => 'DIR', 'type_title' => 'Published Direct Price'],
                    ['type_id' => 'FFP', 'type_title' => 'Fed Financial Participation(MAC)'],
                    ['type_id' => 'WAC', 'type_title' => 'Wholesale Acquisition Cost']
                ]
            ],
            [
                'source_id' => 'NATL', 'source_title' => 'National',
                'type' => [
                    ['type_id' => 'REG1', 'type_title' => 'Region 1'],
                    ['type_id' => 'REG2', 'type_title' => 'Region 2'],
                    ['type_id' => 'REG3', 'type_title' => 'Region 3'],
                    ['type_id' => 'REG4', 'type_title' => 'Region 4'],
                    ['type_id' => 'REG5', 'type_title' => 'Region 5'],
                ]
            ],
            [
                'source_id' => 'PLAN', 'source_title' => 'Set by the Plan',
                'type' => [
                    ['type_id' => 'MAC', 'type_title' => "Plans's Own Maximum Allowed Change"],
                    ['type_id' => 'UCR', 'type_title' => 'Usual & Customary Reimbursement'],
                ]
            ],
            [
                'source_id' => 'TRX', 'source_title' => 'Inbound from the Provider',
                'type' => [
                    ['type_id' => 'GRASMT', 'type_title' => 'Gross Amount Due'],
                    ['type_id' => 'INGCST', 'type_title' => 'Ingredient Cost from Transaction'],
                    ['type_id' => 'SBMPRC', 'type_title' => 'Total Price from Transaction'],
                    ['type_id' => 'U&C', 'type_title' => 'Usual & Customary Reimbursement'],
                ]
            ],
            [
                'source_id' => 'USR', 'source_title' => 'User Defined', 'type' => [
                    ['type_id' => 'SBC', 'type_title' => 'Store Biling(Acquisition) Cost'],
                    ['type_id' => 'WAC', 'type_title' => 'Warehouse Acquisition Cost'],
                    ['type_id' => 'UCR', 'type_title' => 'Usual and Customary Reimbursement'],
                    ['type_id' => 'USR', 'type_title' => 'User Defined'],
                ]
            ]
        ];

        // $new_array = [];
        // $r = Helper::searchElement('~' . $request->search . '~', $brand_source);
        // foreach ($r as $key => $value) {
        //     $arr = ['type_id' => $key, 'type_title' => $value];
        //     array_push($new_array, $arr);
        // }

        return $this->respondWithToken($this->token(), '', $brand_source);
    }

    public function submitPriceSchedule(Request $request)
    {
        if ($request->add_new) {
            $validator = Validator::make($request->all(), [
                'price_schedule' => ['required', 'max:10', Rule::unique('price_schedule')->where(function ($q) {
                    $q->whereNotNull('price_schedule');
                })],
                'price_schedule_name' => ['required', 'max:35'],
                'bng1_stdpkg' => ['required', 'max:1'],
                'bng2_stdpkg' => ['required', 'max:1'],
                'bng3_stdpkg' => ['required', 'max:1'],
                'bng4_stdpkg' => ['required', 'max:1'],
                'bng5_stdpkg' => ['required', 'max:1'],
                'bng6_stdpkg' => ['required', 'max:1'],
                'bga1_stdpkg' => ['required', 'max:1'],
                'bga2_stdpkg' => ['required', 'max:1'],
                'bga3_stdpkg' => ['required', 'max:1'],
                'bga4_stdpkg' => ['required', 'max:1'],
                'bga5_stdpkg' => ['required', 'max:1'],
                'bga6_stdpkg' => ['required', 'max:1'],
                'gen1_stdpkg' => ['required', 'max:1'],
                'gen2_stdpkg' => ['required', 'max:1'],
                'gen3_stdpkg' => ['required', 'max:1'],
                'gen4_stdpkg' => ['required', 'max:1'],
                'gen5_stdpkg' => ['required', 'max:1'],
                'gen6_stdpkg' => ['required', 'max:1'],
                'tax_flag' => ['required', 'max:1'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $add_price_schedule = DB::table('price_schedule')
                    ->insert([
                        'PRICE_SCHEDULE' => $request->price_schedule,
                        'PRICE_SCHEDULE_NAME' => $request->price_schedule_name,
                        'COPAY_SCHEDULE' => $request->copay_schedule,
                        'tax_flag' => $request->tax_flag,
                        'BNG1_STDPKG' => $request->bng1_stdpkg,
                        'BNG1_SOURCE' => $request->bng1_source,
                        'BNG1_TYPE' => $request->bng1_type,
                        'BNG1_MARKUP_PERCENT' => $request->bng1_markup_percent,
                        'BNG1_MARKUP_AMOUNT' => $request->bng1_markup_amount,
                        'BNG1_FEE_PERCENT' => $request->bng1_fee_percent,
                        'BNG1_FEE_AMOUNT' => $request->bng1_fee_amount,
                        'BNG2_STDPKG' => $request->bng2_stdpkg,
                        'BNG2_SOURCE' => $request->bng2_source,
                        'BNG2_TYPE' => $request->bng2_type,
                        'BNG2_MARKUP_PERCENT' => $request->bng2_markup_percent,
                        'BNG2_MARKUP_AMOUNT' => $request->bng2_markup_amount,
                        'BNG2_FEE_PERCENT' => $request->bng2_fee_percent,
                        'BNG2_FEE_AMOUNT' => $request->bng2_fee_amount,
                        'BNG3_STDPKG' => $request->bng3_stdpkg,
                        'BNG3_SOURCE' => $request->bng3_source,
                        'BNG3_TYPE' => $request->bng3_type,
                        'BNG3_MARKUP_PERCENT' => $request->bng3_markup_percent,
                        'BNG3_MARKUP_AMOUNT' => $request->bng3_markup_amount,
                        'BNG3_FEE_PERCENT' => $request->bng3_fee_percent,
                        'BNG3_FEE_AMOUNT' => $request->bng3_fee_amount,
                        'BNG4_STDPKG' => $request->bng4_stdpkg,
                        'BNG4_SOURCE' => $request->bng4_source,
                        'BNG4_TYPE' => $request->bng4_type,
                        'BNG4_MARKUP_PERCENT' => $request->bng4_markup_percent,
                        'BNG4_MARKUP_AMOUNT' => $request->bng4_markup_amount,
                        'BNG4_FEE_PERCENT' => $request->bng4_fee_percent,
                        'BNG4_FEE_AMOUNT' => $request->bng4_fee_amount,
                        'BNG5_STDPKG' => $request->bng5_stdpkg,
                        'BNG5_SOURCE' => $request->bng5_source,
                        'BNG5_TYPE' => $request->bng5_type,
                        'BNG5_MARKUP_PERCENT' => $request->bng5_markup_percent,
                        'BNG5_MARKUP_AMOUNT' => $request->bng5_markup_amount,
                        'BNG5_FEE_PERCENT' => $request->bng5_fee_percent,
                        'BNG5_FEE_AMOUNT' => $request->bng5_fee_amount,
                        'BNG6_STDPKG' => $request->bng6_stdpkg,
                        'BNG6_SOURCE' => $request->bng6_source,
                        'BNG6_TYPE' => $request->bng6_type,
                        'BNG6_MARKUP_PERCENT' => $request->bng6_markup_percent,
                        'BNG6_MARKUP_AMOUNT' => $request->bng6_markup_amount,
                        'BNG6_FEE_PERCENT' => $request->bng6_fee_percent,
                        'BNG6_FEE_AMOUNT' => $request->bng6_fee_amount,


                        'BGA1_STDPKG' => $request->bga1_stdpkg,
                        'BGA1_SOURCE' => $request->bga1_source,
                        'BGA1_TYPE' => $request->bga1_type,
                        'BGA1_MARKUP_PERCENT' => $request->bga1_markup_percent,
                        'BGA1_MARKUP_AMOUNT' => $request->bga1_markup_amount,
                        'BGA1_FEE_PERCENT' => $request->bga1_fee_percent,
                        'BGA1_FEE_AMOUNT' => $request->bga1_fee_amount,
                        'BGA2_STDPKG' => $request->bga2_stdpkg,
                        'BGA2_SOURCE' => $request->bga2_source,
                        'BGA2_TYPE' => $request->bga2_type,
                        'BGA2_MARKUP_PERCENT' => $request->bga2_markup_percent,
                        'BGA2_MARKUP_AMOUNT' => $request->bga2_markup_amount,
                        'BGA2_FEE_PERCENT' => $request->bga2_fee_percent,
                        'BGA2_FEE_AMOUNT' => $request->bga2_fee_amount,
                        'BGA3_STDPKG' => $request->bga3_stdpkg,
                        'BGA3_SOURCE' => $request->bga3_source,
                        'BGA3_TYPE' => $request->bga3_type,
                        'BGA3_MARKUP_PERCENT' => $request->bga3_markup_percent,
                        'BGA3_MARKUP_AMOUNT' => $request->bga3_markup_amount,
                        'BGA3_FEE_PERCENT' => $request->bga3_fee_percent,
                        'BGA3_FEE_AMOUNT' => $request->bga3_fee_amount,
                        'BGA4_STDPKG' => $request->bga4_stdpkg,
                        'BGA4_SOURCE' => $request->bga4_source,
                        'BGA4_TYPE' => $request->bga4_type,
                        'BGA4_MARKUP_PERCENT' => $request->bga4_markup_percent,
                        'BGA4_MARKUP_AMOUNT' => $request->bga4_markup_amount,
                        'BGA4_FEE_PERCENT' => $request->bga4_fee_percent,
                        'BGA4_FEE_AMOUNT' => $request->bga4_fee_amount,
                        'BGA5_STDPKG' => $request->bga5_stdpkg,
                        'BGA5_SOURCE' => $request->bga5_source,
                        'BGA5_TYPE' => $request->bga5_type,
                        'BGA5_MARKUP_PERCENT' => $request->bga5_markup_percent,
                        'BGA5_MARKUP_AMOUNT' => $request->bga5_markup_amount,
                        'BGA5_FEE_PERCENT' => $request->bga5_fee_percent,
                        'BGA5_FEE_AMOUNT' => $request->bga5_fee_amount,
                        'BGA6_STDPKG' => $request->bga6_stdpkg,
                        'BGA6_SOURCE' => $request->bga6_source,
                        'BGA6_TYPE' => $request->bga6_type,
                        'BGA6_MARKUP_PERCENT' => $request->bga6_markup_percent,
                        'BGA6_MARKUP_AMOUNT' => $request->bga6_markup_amount,
                        'BGA6_FEE_PERCENT' => $request->bga6_fee_percent,
                        'BGA6_FEE_AMOUNT' => $request->bga6_fee_amount,

                        'GEN1_STDPKG' => $request->gen1_stdpkg,
                        'GEN1_SOURCE' => $request->gen1_source,
                        'GEN1_TYPE' => $request->gen1_type,
                        'GEN1_MARKUP_PERCENT' => $request->gen1_markup_percent,
                        'GEN1_MARKUP_AMOUNT' => $request->gen1_markup_amount,
                        'GEN1_FEE_PERCENT' => $request->gen1_fee_percent,
                        'GEN1_FEE_AMOUNT' => $request->gen1_fee_amount,
                        'GEN2_STDPKG' => $request->gen2_stdpkg,
                        'GEN2_SOURCE' => $request->gen2_source,
                        'GEN2_TYPE' => $request->gen2_type,
                        'GEN2_MARKUP_PERCENT' => $request->gen2_markup_percent,
                        'GEN2_MARKUP_AMOUNT' => $request->gen2_markup_amount,
                        'GEN2_FEE_PERCENT' => $request->gen2_fee_percent,
                        'GEN2_FEE_AMOUNT' => $request->gen2_fee_amount,
                        'GEN3_STDPKG' => $request->gen3_stdpkg,
                        'GEN3_SOURCE' => $request->gen3_source,
                        'GEN3_TYPE' => $request->gen3_type,
                        'GEN3_MARKUP_PERCENT' => $request->gen3_markup_percent,
                        'GEN3_MARKUP_AMOUNT' => $request->gen3_markup_amount,
                        'GEN3_FEE_PERCENT' => $request->gen3_fee_percent,
                        'GEN3_FEE_AMOUNT' => $request->gen3_fee_amount,
                        'GEN4_STDPKG' => $request->gen4_stdpkg,
                        'GEN4_SOURCE' => $request->gen4_source,
                        'GEN4_TYPE' => $request->gen4_type,
                        'GEN4_MARKUP_PERCENT' => $request->gen4_markup_percent,
                        'GEN4_MARKUP_AMOUNT' => $request->gen4_markup_amount,
                        'GEN4_FEE_PERCENT' => $request->gen4_fee_percent,
                        'GEN4_FEE_AMOUNT' => $request->gen4_fee_amount,
                        'GEN5_STDPKG' => $request->gen5_stdpkg,
                        'GEN5_SOURCE' => $request->gen5_source,
                        'GEN5_TYPE' => $request->gen5_type,
                        'GEN5_MARKUP_PERCENT' => $request->gen5_markup_percent,
                        'GEN5_MARKUP_AMOUNT' => $request->gen5_markup_amount,
                        'GEN5_FEE_PERCENT' => $request->gen5_fee_percent,
                        'GEN5_FEE_AMOUNT' => $request->gen5_fee_amount,
                        'GEN6_STDPKG' => $request->gen6_stdpkg,
                        'GEN6_SOURCE' => $request->gen6_source,
                        'GEN6_TYPE' => $request->gen6_type,
                        'GEN6_MARKUP_PERCENT' => $request->gen6_markup_percent,
                        'GEN6_MARKUP_AMOUNT' => $request->gen6_markup_amount,
                        'GEN6_FEE_PERCENT' => $request->gen6_fee_percent,
                        'GEN6_FEE_AMOUNT' => $request->gen6_fee_amount,
                    ]);

                $price_schedule = DB::table('price_schedule')
                    ->where('price_schedule', $request->price_schedule)
                    ->first();
                return $this->respondWithToken($this->token(), 'Added successfully!', $add_price_schedule);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'price_schedule' => ['required', 'max:10'],
                'price_schedule_name' => ['required', 'max:35'],
                'bng1_stdpkg' => ['required', 'max:1'],
                'bng2_stdpkg' => ['required', 'max:1'],
                'bng3_stdpkg' => ['required', 'max:1'],
                'bng4_stdpkg' => ['required', 'max:1'],
                'bng5_stdpkg' => ['required', 'max:1'],
                'bng6_stdpkg' => ['required', 'max:1'],
                'tax_flag' => ['required', 'max:1'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $update_price_schedule = DB::table('price_schedule')
                    ->where('price_schedule', $request->price_schedule)
                    ->update([
                        'PRICE_SCHEDULE_NAME' => $request->price_schedule_name,
                        'COPAY_SCHEDULE' => $request->copay_schedule,
                        'tax_flag' => $request->tax_flag,
                        'BNG1_STDPKG' => $request->bng1_stdpkg,
                        'BNG1_SOURCE' => $request->bng1_source,
                        'BNG1_TYPE' => $request->bng1_type,
                        'BNG1_MARKUP_PERCENT' => $request->bng1_markup_percent,
                        'BNG1_MARKUP_AMOUNT' => $request->bng1_markup_amount,
                        'BNG1_FEE_PERCENT' => $request->bng1_fee_percent,
                        'BNG1_FEE_AMOUNT' => $request->bng1_fee_amount,
                        'BNG2_STDPKG' => $request->bng2_stdpkg,
                        'BNG2_SOURCE' => $request->bng2_source,
                        'BNG2_TYPE' => $request->bng2_type,
                        'BNG2_MARKUP_PERCENT' => $request->bng2_markup_percent,
                        'BNG2_MARKUP_AMOUNT' => $request->bng2_markup_amount,
                        'BNG2_FEE_PERCENT' => $request->bng2_fee_percent,
                        'BNG2_FEE_AMOUNT' => $request->bng2_fee_amount,
                        'BNG3_STDPKG' => $request->bng3_stdpkg,
                        'BNG3_SOURCE' => $request->bng3_source,
                        'BNG3_TYPE' => $request->bng3_type,
                        'BNG3_MARKUP_PERCENT' => $request->bng3_markup_percent,
                        'BNG3_MARKUP_AMOUNT' => $request->bng3_markup_amount,
                        'BNG3_FEE_PERCENT' => $request->bng3_fee_percent,
                        'BNG3_FEE_AMOUNT' => $request->bng3_fee_amount,
                        'BNG4_STDPKG' => $request->bng4_stdpkg,
                        'BNG4_SOURCE' => $request->bng4_source,
                        'BNG4_TYPE' => $request->bng4_type,
                        'BNG4_MARKUP_PERCENT' => $request->bng4_markup_percent,
                        'BNG4_MARKUP_AMOUNT' => $request->bng4_markup_amount,
                        'BNG4_FEE_PERCENT' => $request->bng4_fee_percent,
                        'BNG4_FEE_AMOUNT' => $request->bng4_fee_amount,
                        'BNG5_STDPKG' => $request->bng5_stdpkg,
                        'BNG5_SOURCE' => $request->bng5_source,
                        'BNG5_TYPE' => $request->bng5_type,
                        'BNG5_MARKUP_PERCENT' => $request->bng5_markup_percent,
                        'BNG5_MARKUP_AMOUNT' => $request->bng5_markup_amount,
                        'BNG5_FEE_PERCENT' => $request->bng5_fee_percent,
                        'BNG5_FEE_AMOUNT' => $request->bng5_fee_amount,
                        'BNG6_STDPKG' => $request->bng6_stdpkg,
                        'BNG6_SOURCE' => $request->bng6_source,
                        'BNG6_TYPE' => $request->bng6_type,
                        'BNG6_MARKUP_PERCENT' => $request->bng6_markup_percent,
                        'BNG6_MARKUP_AMOUNT' => $request->bng6_markup_amount,
                        'BNG6_FEE_PERCENT' => $request->bng6_fee_percent,
                        'BNG6_FEE_AMOUNT' => $request->bng6_fee_amount,

                        'BGA1_STDPKG' => $request->bga1_stdpkg,
                        'BGA1_SOURCE' => $request->bga1_source,
                        'BGA1_TYPE' => $request->bga1_type,
                        'BGA1_MARKUP_PERCENT' => $request->bga1_markup_percent,
                        'BGA1_MARKUP_AMOUNT' => $request->bga1_markup_amount,
                        'BGA1_FEE_PERCENT' => $request->bga1_fee_percent,
                        'BGA1_FEE_AMOUNT' => $request->bga1_fee_amount,
                        'BGA2_STDPKG' => $request->bga2_stdpkg,
                        'BGA2_SOURCE' => $request->bga2_source,
                        'BGA2_TYPE' => $request->bga2_type,
                        'BGA2_MARKUP_PERCENT' => $request->bga2_markup_percent,
                        'BGA2_MARKUP_AMOUNT' => $request->bga2_markup_amount,
                        'BGA2_FEE_PERCENT' => $request->bga2_fee_percent,
                        'BGA2_FEE_AMOUNT' => $request->bga2_fee_amount,
                        'BGA3_STDPKG' => $request->bga3_stdpkg,
                        'BGA3_SOURCE' => $request->bga3_source,
                        'BGA3_TYPE' => $request->bga3_type,
                        'BGA3_MARKUP_PERCENT' => $request->bga3_markup_percent,
                        'BGA3_MARKUP_AMOUNT' => $request->bga3_markup_amount,
                        'BGA3_FEE_PERCENT' => $request->bga3_fee_percent,
                        'BGA3_FEE_AMOUNT' => $request->bga3_fee_amount,
                        'BGA4_STDPKG' => $request->bga4_stdpkg,
                        'BGA4_SOURCE' => $request->bga4_source,
                        'BGA4_TYPE' => $request->bga4_type,
                        'BGA4_MARKUP_PERCENT' => $request->bga4_markup_percent,
                        'BGA4_MARKUP_AMOUNT' => $request->bga4_markup_amount,
                        'BGA4_FEE_PERCENT' => $request->bga4_fee_percent,
                        'BGA4_FEE_AMOUNT' => $request->bga4_fee_amount,
                        'BGA5_STDPKG' => $request->bga5_stdpkg,
                        'BGA5_SOURCE' => $request->bga5_source,
                        'BGA5_TYPE' => $request->bga5_type,
                        'BGA5_MARKUP_PERCENT' => $request->bga5_markup_percent,
                        'BGA5_MARKUP_AMOUNT' => $request->bga5_markup_amount,
                        'BGA5_FEE_PERCENT' => $request->bga5_fee_percent,
                        'BGA5_FEE_AMOUNT' => $request->bga5_fee_amount,
                        'BGA6_STDPKG' => $request->bga6_stdpkg,
                        'BGA6_SOURCE' => $request->bga6_source,
                        'BGA6_TYPE' => $request->bga6_type,
                        'BGA6_MARKUP_PERCENT' => $request->bga6_markup_percent,
                        'BGA6_MARKUP_AMOUNT' => $request->bga6_markup_amount,
                        'BGA6_FEE_PERCENT' => $request->bga6_fee_percent,
                        'BGA6_FEE_AMOUNT' => $request->bga6_fee_amount,

                        'GEN1_STDPKG' => $request->gen1_stdpkg,
                        'GEN1_SOURCE' => $request->gen1_source,
                        'GEN1_TYPE' => $request->gen1_type,
                        'GEN1_MARKUP_PERCENT' => $request->gen1_markup_percent,
                        'GEN1_MARKUP_AMOUNT' => $request->gen1_markup_amount,
                        'GEN1_FEE_PERCENT' => $request->gen1_fee_percent,
                        'GEN1_FEE_AMOUNT' => $request->gen1_fee_amount,
                        'GEN2_STDPKG' => $request->gen2_stdpkg,
                        'GEN2_SOURCE' => $request->gen2_source,
                        'GEN2_TYPE' => $request->gen2_type,
                        'GEN2_MARKUP_PERCENT' => $request->gen2_markup_percent,
                        'GEN2_MARKUP_AMOUNT' => $request->gen2_markup_amount,
                        'GEN2_FEE_PERCENT' => $request->gen2_fee_percent,
                        'GEN2_FEE_AMOUNT' => $request->gen2_fee_amount,
                        'GEN3_STDPKG' => $request->gen3_stdpkg,
                        'GEN3_SOURCE' => $request->gen3_source,
                        'GEN3_TYPE' => $request->gen3_type,
                        'GEN3_MARKUP_PERCENT' => $request->gen3_markup_percent,
                        'GEN3_MARKUP_AMOUNT' => $request->gen3_markup_amount,
                        'GEN3_FEE_PERCENT' => $request->gen3_fee_percent,
                        'GEN3_FEE_AMOUNT' => $request->gen3_fee_amount,
                        'GEN4_STDPKG' => $request->gen4_stdpkg,
                        'GEN4_SOURCE' => $request->gen4_source,
                        'GEN4_TYPE' => $request->gen4_type,
                        'GEN4_MARKUP_PERCENT' => $request->gen4_markup_percent,
                        'GEN4_MARKUP_AMOUNT' => $request->gen4_markup_amount,
                        'GEN4_FEE_PERCENT' => $request->gen4_fee_percent,
                        'GEN4_FEE_AMOUNT' => $request->gen4_fee_amount,
                        'GEN5_STDPKG' => $request->gen5_stdpkg,
                        'GEN5_SOURCE' => $request->gen5_source,
                        'GEN5_TYPE' => $request->gen5_type,
                        'GEN5_MARKUP_PERCENT' => $request->gen5_markup_percent,
                        'GEN5_MARKUP_AMOUNT' => $request->gen5_markup_amount,
                        'GEN5_FEE_PERCENT' => $request->gen5_fee_percent,
                        'GEN5_FEE_AMOUNT' => $request->gen5_fee_amount,
                        'GEN6_STDPKG' => $request->gen6_stdpkg,
                        'GEN6_SOURCE' => $request->gen6_source,
                        'GEN6_TYPE' => $request->gen6_type,
                        'GEN6_MARKUP_PERCENT' => $request->gen6_markup_percent,
                        'GEN6_MARKUP_AMOUNT' => $request->gen6_markup_amount,
                        'GEN6_FEE_PERCENT' => $request->gen6_fee_percent,
                        'GEN6_FEE_AMOUNT' => $request->gen6_fee_amount,
                    ]);

                $price_schedule = DB::table('price_schedule')
                    ->where('price_schedule', $request->price_schedule)
                    ->first();
                return $this->respondWithToken($this->token(), 'Updated successfully!', $price_schedule);
            }
        }
    }
}
