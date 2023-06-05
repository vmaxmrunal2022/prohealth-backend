<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class PricingStrategyController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('PRICING_STRATEGY_NAMES')
            ->select('PRICING_STRATEGY_NAMES.pricing_strategy_id', 'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_NAME as pricing_strategy_name')
            ->where(DB::raw('UPPER(PRICING_STRATEGY_NAMES.pricing_strategy_id)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere(DB::raw('UPPER(PRICING_STRATEGY_NAMES.PRICING_STRATEGY_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function get_all(Request $request)
    {

        $pricing_strategies = DB::table('PRICING_STRATEGY_NAMES')->get();

        if ($pricing_strategies) {

            return $this->respondWithToken($this->token(), 'Data Fetched Successfully', $pricing_strategies);
        } else {

            return $this->respondWithToken($this->token(), 'something Went Wrong', $pricing_strategies);
        }
    }



    public function getProviderList($ndcid)
    {
        $ndclist = DB::table('PRICING_STRATEGY')
            // ->select('DIAGNOSIS_LIST', 'DIAGNOSIS_ID','PRIORITY')
            // ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
            ->where('PRICING_STRATEGY.pricing_strategy_id', $ndcid)
            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($pricing_strategy_id, $effective_date, $price_schedule)
    {
        $ndc = DB::table('PRICING_STRATEGY')
            ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
            ->where('PRICING_STRATEGY.pricing_strategy_id', $pricing_strategy_id)
            ->where('PRICING_STRATEGY.EFFECTIVE_DATE', date('Ymd', strtotime($effective_date)))
            ->where('PRICING_STRATEGY.PRICE_SCHEDULE', $price_schedule)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function add(Request $request)
    {
        $createddate = date('Ymd');
        $checkPricingStrategyNamesRecord  = DB::table('PRICING_STRATEGY_NAMES')
            ->where(DB::raw('UPPER(pricing_strategy_id)'), strtoupper($request->pricing_strategy_id))->count();

        if ($request->new == 1) {
            $validator = Validator::make($request->all(), [
                // "pricing_strategy_id" => ['required', 'max:10', Rule::unique('PRICING_STRATEGY_NAMES')->where(function ($q) {
                //     $q->whereNotNull('pricing_strategy_id');
                // })],
                // "pricing_strategy_name" => ['max:35'],
                // "pharm_type_variation_ind" => ['max:1'],
                // "effective_date" => ['required', 'max:10'],
                // "network_part_variation_ind" => ['max:1'],
                // "claim_type_variation_ind" => ['max:1'],
                // "formulary_variation_ind" => ['max:1'],
                // "price_schedule" => ['max:10'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($checkPricingStrategyNamesRecord) {
                    return $this->respondWithToken($this->token(), 'Pricing Strategy ID already exists', $checkPricingStrategyNamesRecord, false);
                } else {
                    $accum_benfit_stat_names = DB::table('PRICING_STRATEGY_NAMES')->insert(
                        [
                            'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                            'pricing_strategy_name' => $request->pricing_strategy_name,
                            'DATE_TIME_CREATED' => date('Ymd'),
                            'user_id' => Cache::get('userId'),
                            'DATE_TIME_MODIFIED' => date('Ymd'),
                            'form_id' => '',
                            'USER_ID_CREATED' => Cache::get('userId'),
                        ]
                    );

                    $accum_benfit_stat = DB::table('PRICING_STRATEGY')->insert(
                        [
                            'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                            'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                            'formulary_variation_ind' => $request->formulary_variation_ind,
                            'network_part_variation_ind' => $request->network_part_variation_ind,
                            'claim_type_variation_ind' => $request->claim_type_variation_ind,
                            'DATE_TIME_CREATED' => $createddate,
                            'user_id' => Cache::get('userId'),
                            'DATE_TIME_MODIFIED' => date('Ymd'),
                            'form_id' => '',
                            'user_id_created' => Cache::get('userId'),
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'module_exit' => $request->module_exit,
                            'price_schedule' => $request->price_schedule,
                            'mac_list' => $request->mac_list,
                        ]
                    );
                    $val = DB::table('PRICING_STRATEGY')
                        ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
                        ->where('PRICING_STRATEGY.pricing_strategy_id', $request->pricing_strategy_id)
                        ->get();

                    $exp = DB::table('PRICING_STRATEGY_NAMES')
                        ->where(DB::raw('UPPER(PRICING_STRATEGY_NAMES.pricing_strategy_id)'), 'like', '%' . strtoupper($request->pricing_strategy_id) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', [$val, $exp]);
                }
            }
        } else {
            $checkPricingStrategyRecord  = DB::table('PRICING_STRATEGY')
                ->where(DB::raw('UPPER(PRICING_STRATEGY.pricing_strategy_id)'), strtoupper($request->pricing_strategy_id))
                ->where('EFFECTIVE_DATE', $request->effective_date)
                ->where('PRICE_SCHEDULE', $request->price_schedule)
                ->count();

            // return $checkPricingStrategyRecord;
            $validator = Validator::make($request->all(), [
                // "pricing_strategy_id" => ['required', 'max:10'],
                // "pricing_strategy_name" => ['max:35'],
                // "pharm_type_variation_ind" => ['max:1'],
                // "effective_date" => ['required', 'max:10'],
                // "network_part_variation_ind" => ['max:1'],
                // "claim_type_variation_ind" => ['max:1'],
                // "formulary_variation_ind" => ['max:1'],
                // "price_schedule" => ['max:10'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($checkPricingStrategyRecord) {
                    if ($request->addUpdate == 0) {
                        $val = DB::table('PRICING_STRATEGY')
                            ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
                            ->where('PRICING_STRATEGY.pricing_strategy_id', $request->pricing_strategy_id)
                            ->get();

                        $exp = DB::table('PRICING_STRATEGY_NAMES')
                            ->where(DB::raw('UPPER(PRICING_STRATEGY_NAMES.pricing_strategy_id)'), 'like', '%' . strtoupper($request->pricing_strategy_id) . '%')
                            ->get();
                        return $this->respondWithToken($this->token(), 'Pricing Schedule ID already exists', [$val, $exp], false);
                    }
                    $benefitcode = DB::table('PRICING_STRATEGY_NAMES')
                        ->where(DB::raw('UPPER(pricing_strategy_id)'), strtoupper($request->pricing_strategy_id))
                        ->update(
                            ['pricing_strategy_name' => $request->pricing_strategy_name, 'user_id' => Cache::get('userId'), 'DATE_TIME_MODIFIED' => date('Ymd')]
                        );

                    $accum_benfit_stat = DB::table('PRICING_STRATEGY')
                        ->where('pricing_strategy_id', $request->pricing_strategy_id)
                        ->where('EFFECTIVE_DATE', $request->effective_date)
                        ->where('PRICE_SCHEDULE', $request->price_schedule)
                        ->update(
                            [
                                // 'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                                'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                                'formulary_variation_ind' => $request->formulary_variation_ind,
                                'network_part_variation_ind' => $request->network_part_variation_ind,
                                'claim_type_variation_ind' => $request->claim_type_variation_ind,
                                // 'date_time_created' => $createddate,
                                'user_id' =>  Cache::get('userId'),
                                'date_time_modified' => date('Ymd'),
                                'form_id' => '',
                                'effective_date' => $request->effective_date,
                                'module_exit' => $request->module_exit,
                                'price_schedule' => $request->price_schedule,
                                'mac_list' => $request->mac_list,
                            ]
                        );
                    $val = DB::table('PRICING_STRATEGY')
                        ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
                        ->where('PRICING_STRATEGY.pricing_strategy_id', $request->pricing_strategy_id)
                        ->get();

                    $exp = DB::table('PRICING_STRATEGY_NAMES')
                        ->where(DB::raw('UPPER(PRICING_STRATEGY_NAMES.pricing_strategy_id)'), 'like', '%' . strtoupper($request->pricing_strategy_id) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', [$val, $exp]);
                } else {

                    $benefitcode = DB::table('PRICING_STRATEGY_NAMES')
                        ->where('pricing_strategy_id', strtoupper($request->pricing_strategy_id))
                        ->update(
                            [
                                'pricing_strategy_name' => $request->pricing_strategy_name,
                                'user_id' => Cache::get('userId'),
                                'DATE_TIME_MODIFIED' => date('Ymd'),
                            ]
                        );

                    $accum_benfit_stat = DB::table('PRICING_STRATEGY')->insert(
                        [
                            'pricing_strategy_id' => strtoupper($request->pricing_strategy_id),
                            'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                            'formulary_variation_ind' => $request->formulary_variation_ind,
                            'network_part_variation_ind' => $request->network_part_variation_ind,
                            'claim_type_variation_ind' => $request->claim_type_variation_ind,
                            'DATE_TIME_CREATED' => $createddate,
                            'user_id' => Cache::get('userId'),
                            'DATE_TIME_MODIFIED' => date('Ymd'),
                            'form_id' => '',
                            'user_id_created' => Cache::get('userId'),
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'module_exit' => $request->module_exit,
                            'price_schedule' => $request->price_schedule,
                            'mac_list' => $request->mac_list,
                        ]
                    );
                    $val = DB::table('PRICING_STRATEGY')
                        ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
                        ->where('PRICING_STRATEGY.pricing_strategy_id', $request->pricing_strategy_id)
                        ->get();

                    $exp = DB::table('PRICING_STRATEGY_NAMES')
                        ->where(DB::raw('UPPER(PRICING_STRATEGY_NAMES.pricing_strategy_id)'), 'like', '%' . strtoupper($request->pricing_strategy_id) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', [$val, $exp]);
                }
            }
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->pricing_strategy_id) && isset($request->effective_date) && isset($request->price_schedule)) {
            $all_pricing_strategy = DB::table('PRICING_STRATEGY')
                ->where('pricing_strategy_id', $request->pricing_strategy_id)
                ->count();
            if ($all_pricing_strategy == 1) {
                $pricing_strategy_names = DB::table('PRICING_STRATEGY_NAMES')
                    ->where('pricing_strategy_id', $request->pricing_strategy_id)
                    ->delete();

                $pricing_strategy = DB::table('PRICING_STRATEGY')
                    ->where('pricing_strategy_id', $request->pricing_strategy_id)
                    ->delete();
            } else {
                $pricing_strategy = DB::table('PRICING_STRATEGY')
                    ->where('pricing_strategy_id', $request->pricing_strategy_id)
                    ->where('PRICE_SCHEDULE', $request->price_schedule)
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->delete();
            }

            if ($pricing_strategy) {
                $val = DB::table('PRICING_STRATEGY')
                    ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
                    ->where('PRICING_STRATEGY.pricing_strategy_id', $request->pricing_strategy_id)
                    ->get();

                $exp = DB::table('PRICING_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(PRICING_STRATEGY_NAMES.pricing_strategy_id)'), 'like', '%' . strtoupper($request->pricing_strategy_id) . '%')
                    ->get();
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully ', [$val, $exp]);
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
            return $this->respondWithToken($this->token(), 'Record deleted Successfully ', $pricing_strategy);
        } else {
            if (isset($request->pricing_strategy_id)) {
                $all_accum_bene_strategy_names = DB::table('PRICING_STRATEGY_NAMES')
                    ->where('pricing_strategy_id', $request->pricing_strategy_id)
                    ->delete();
                $val = DB::table('PRICING_STRATEGY')
                    ->join('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY.pricing_strategy_id', '=', 'PRICING_STRATEGY_NAMES.pricing_strategy_id')
                    ->where('PRICING_STRATEGY.pricing_strategy_id', $request->pricing_strategy_id)
                    ->get();

                $exp = DB::table('PRICING_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(PRICING_STRATEGY_NAMES.pricing_strategy_id)'), 'like', '%' . strtoupper($request->pricing_strategy_id) . '%')
                    ->get();

                return $this->respondWithToken($this->token(), 'Record Deleted Successfully ', [$val, $exp], false);
            }
            return $this->respondWithToken($this->token(), 'Record Not found', 'false');
        }
    }
}
