<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CopayStrategyController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $ndc = DB::table('COPAY_STRATEGY')
                ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                ->select('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $ndc);
        }
    }

    public function getList($ndcid)
    {
        $ndclist = DB::table('COPAY_STRATEGY')
        ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            // ->select( 'DIAGNOSIS_LIST', 'DIAGNOSIS_ID', 'PRIORITY' )
            ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' . strtoupper($ndcid) . '%')
            // ->orWhere( 'EXCEPTION_NAME', 'like', '%' . strtoupper( $ndcid ) . '%' )
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDetails($ndcid)
    {
        $ndc = DB::table('COPAY_STRATEGY')
            ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        if ($request->has('new')) {

            $validator = Validator::make($request->all(), [
                "copay_strategy_id" => ['required', 'max:10', Rule::unique('COPAY_STRATEGY_NAMES')->where(function ($q) {
                    $q->whereNotNull('copay_strategy_id');
                })],
                "effective_date" => ['required', 'max:10'],
                "copay_strategy_name" => ['max:35'],
                "pharm_type_variation_ind" => ['max:1'],
                "network_part_variation_ind" => ['max:1'],
                "claim_type_variation_ind" => ['max:1'],
                "formulary_variation_ind" => ['max:1'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $accum_benfit_stat_names = DB::table('COPAY_STRATEGY_NAMES')->insert(
                    [
                        'copay_strategy_id' => $request->copay_strategy_id,
                        'copay_strategy_name' => $request->copay_strategy_name,
                    ]
                );
                $accum_benfit_stat = DB::table('COPAY_STRATEGY')->insert(
                    [
                        'copay_strategy_id' => $request->copay_strategy_id,
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'formulary_variation_ind' => $request->formulary_variation_ind,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'date_time_created' => $createddate,
                        'user_id' => '',
                        'date_time_modified' => '',
                        'form_id' => '',
                        'user_id_created' => '',
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'module_exit'=>$request->module_exit,
                        'copay_schedule'=>$request->copay_schedule,
                    ]
                );
                $benefitcode = DB::table('COPAY_STRATEGY')->where('copay_strategy_id', 'like', $request->copay_strategy_id)->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $benefitcode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "copay_strategy_id" => ['required', 'max:10'],
                "effective_date" => ['required', 'max:10'],
                "copay_strategy_name" => ['max:35'],
                "pharm_type_variation_ind" => ['max:1'],
                "network_part_variation_ind" => ['max:1'],
                "claim_type_variation_ind" => ['max:1'],
                "formulary_variation_ind" => ['max:1'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                $benefitcode = DB::table('COPAY_STRATEGY_NAMES')
                    ->where('copay_strategy_id', $request->copay_strategy_id)
                    ->update(
                        [
                            'copay_strategy_id' => strtoupper($request->copay_strategy_id),
                            'copay_strategy_name' => $request->copay_strategy_name,


                        ]
                    );

                $accum_benfit_stat = DB::table('COPAY_STRATEGY')
                    ->where('copay_strategy_id', $request->copay_strategy_id)
                    ->update(
                        [
                            'copay_strategy_id' => strtoupper($request->copay_strategy_id),
                            'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                            'formulary_variation_ind' => $request->formulary_variation_ind,
                            'network_part_variation_ind' => $request->network_part_variation_ind,
                            'claim_type_variation_ind' => $request->claim_type_variation_ind,
                            'date_time_created' => $createddate,
                            'user_id' => '',
                            'date_time_modified' => '',
                            'form_id' => '',
                            'user_id_created' => '',
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'module_exit'=>$request->module_exit,
                            'copay_schedule'=>$request->copay_schedule,

                        ]
                    );
            }
        }
        $benefitcode = DB::table('COPAY_STRATEGY')->where('copay_strategy_id', 'like', $request->copay_strategy_id)->first();
        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
    }


    public function CopayDropDown(Request $request){

        $ndc = DB::table('COPAY_STRATEGY_NAMES')
            ->get();

        return $this->respondWithToken($this->token(), 'Data Fetched Suceefully', $ndc);

    }
}
