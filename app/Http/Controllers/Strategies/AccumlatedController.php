<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccumlatedController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $ndc = DB::table('ACCUM_BENEFIT_STRATEGY')
                ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID')
                ->select('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $ndc);
        }
    }

    public function getList($ndcid)
    {
        $ndclist = DB::table('ACCUM_BENEFIT_STRATEGY')
            // ->select( 'DIAGNOSIS_LIST', 'DIAGNOSIS_ID', 'PRIORITY' )
            ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID')

            ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'like', '%' . strtoupper($ndcid) . '%')
            // ->orWhere( 'EXCEPTION_NAME', 'like', '%' . strtoupper( $ndcid ) . '%' )
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDetails($ndcid)
    {
        $ndc = DB::table('ACCUM_BENEFIT_STRATEGY')
            ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID')
            ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $existdata = DB::table('accum_bene_strategy_names')
            ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
            ->first();

        if ($request->new) {
            $validator = Validator::make($request->all(), [
                "accum_bene_strategy_id" => ['required', 'max:10', Rule::unique('accum_bene_strategy_names')->where(function ($q) {
                    $q->whereNotNull('accum_bene_strategy_id');
                })],
                "effective_date" => ['required', 'max:10'],
                "accum_bene_strategy_name" => ['max:40'],
                "pharm_type_variation_ind" => ['max:1'],
                "network_part_variation_ind" => ['max:1'],
                "claim_type_variation_ind" => ['max:1'],
                "formulary_variation_ind" => ['max:1'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $accum_benfit_stat_names = DB::table('accum_bene_strategy_names')->insert(
                    [
                        'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                        'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
                    ]
                );

                $accum_benfit_stat = DB::table('ACCUM_BENEFIT_STRATEGY')->insert(
                    [
                        'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'formulary_variation_ind' => $request->formulary_variation_ind,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'date_time_created' => $createddate,
                        'user_id' => '',
                        'date_time_modified' => '',
                        'form_id' => '',
                        'user_id_created' => '',
                        'accum_exclusion_flag' => $request->accum_exclusion_flag,
                        //'effective_date' => strtotime($request->effective_date),
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'module_exit' => $request->module_exit,
                        'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                    ]
                );
                $accum_benfit_stat = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->join('accum_bene_strategy_names', 'ACCUM_BENEFIT_STRATEGY.accum_bene_strategy_id', '=', 'accum_bene_strategy_names.accum_bene_strategy_id')
                    ->where('accum_bene_strategy_names.accum_bene_strategy_id', $request->accum_bene_strategy_id)
                    ->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully ', $accum_benfit_stat);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "accum_bene_strategy_id" => ['required', 'max:10'],
                "effective_date" => ['required', 'max:10'],
                "accum_bene_strategy_name" => ['max:40'],
                "pharm_type_variation_ind" => ['max:1'],
                "network_part_variation_ind" => ['max:1'],
                "claim_type_variation_ind" => ['max:1'],
                "formulary_variation_ind" => ['max:1'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $update = DB::table('accum_bene_strategy_names')
                    ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                    ->update(
                        [
                            'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                            'accum_bene_strategy_name' => $request->accum_bene_strategy_name,

                        ]
                    );

                $accum_benfit_stat = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                    ->update(
                        [
                            'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                            'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                            'formulary_variation_ind' => $request->formulary_variation_ind,
                            'network_part_variation_ind' => $request->network_part_variation_ind,
                            'claim_type_variation_ind' => $request->claim_type_variation_ind,
                            'date_time_created' => $createddate,
                            'user_id' => '',
                            'date_time_modified' => '',
                            'form_id' => '',
                            'user_id_created' => '',
                            'accum_exclusion_flag' => $request->accum_exclusion_flag,
                            //'effective_date' => $request->effective_date,
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                            'module_exit' => $request->module_exit,
                            ]
                    );

                $accum_benfit_stat = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->join('accum_bene_strategy_names', 'ACCUM_BENEFIT_STRATEGY.accum_bene_strategy_id', '=', 'accum_bene_strategy_names.accum_bene_strategy_id')
                    ->where('accum_bene_strategy_names.accum_bene_strategy_id', $request->accum_bene_strategy_id)
                    ->first();
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $accum_benfit_stat);
            }
        }
    }

    public function AccumlatedDropDown(Request $request){

        $ndc = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            ->get();

        return $this->respondWithToken($this->token(), 'Data Fetched Suceefully', $ndc);

    }
}
