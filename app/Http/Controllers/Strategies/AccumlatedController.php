<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccumlatedController extends Controller
{

    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $existdata = DB::table('accum_bene_strategy_names')
            ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
            ->first();

        if ($request->new) {

            if (!$request->updateForm) {
                $ifExist = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                    ->get();
                if (count($ifExist) >= 1) {
                    return $this->respondWithToken($this->token(), [["Duplicate Parent Record"]], '', false);
                }
            } else {
            }

            if ($request->accum_bene_strategy_id && $request->effective_date) {
                $count = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                    ->get()
                    ->count();
                if ($count <= 0) {
                    // return date('d-M-y');
                    $add_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                        ->insert(
                            [
                                'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                                'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'form_id' => ''
                            ]
                        );
                    $add = DB::table('ACCUM_BENEFIT_STRATEGY')
                        ->insert([
                            'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                            'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                            'formulary_variation_ind' => $request->formulary_variation_ind,
                            'network_part_variation_ind' => $request->network_part_variation_ind,
                            'claim_type_variation_ind' => $request->claim_type_variation_ind,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'user_id' => Cache::get('userId'),
                            'DATE_TIME_MODIFIED' => date('d-M-y'),
                            'form_id' => '',
                            'user_id_created' => '',
                            'accum_exclusion_flag' => $request->accum_exclusion_flag,
                            'effective_date' => date('Ymd', strtotime($request->effective_date)),
                            'module_exit' => $request->module_exit,
                            'plan_accum_deduct_id' => $request->plan_accum_deduct_id,

                        ]);

                    $add = DB::table('ACCUM_BENEFIT_STRATEGY')->where('accum_bene_strategy_id', 'like', '%' . $request->accum_bene_strategy_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
                } else {
                    $updateProviderExceptionData = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                        ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                        ->update([
                            'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
                            'user_id' => Cache::get('userId'),
                            'date_time_modified' => date('d-M-y'),
                            'form_id' => ''
                        ]);

                    $eff_date = $request->effective_date;
                    $countValidation = DB::table('ACCUM_BENEFIT_STRATEGY')
                        // ->select('effective_date')
                        ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                        ->whereBetween('effective_date', [$eff_date, $eff_date])
                        ->get();

                    if (count($countValidation) >= 1) {
                        return $this->respondWithToken(
                            $this->token(),
                            [['Duplicate Child Record']],
                            [['Duplicate Child Record']],
                            false
                        );
                    } else {

                        $addProviderValidationData = DB::table('ACCUM_BENEFIT_STRATEGY')
                            ->insert([
                                'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                                'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                                'formulary_variation_ind' => $request->formulary_variation_ind,
                                'network_part_variation_ind' => $request->network_part_variation_ind,
                                'claim_type_variation_ind' => $request->claim_type_variation_ind,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'form_id' => '',
                                'user_id_created' => '',
                                'accum_exclusion_flag' => $request->accum_exclusion_flag,
                                'effective_date' => date('Ymd', strtotime($request->effective_date)),
                                'module_exit' => $request->module_exit,
                                'plan_accum_deduct_id' => $request->plan_accum_deduct_id,

                            ]);
                        $reecord = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                            ->join('ACCUM_BENEFIT_STRATEGY', 'ACCUM_BENE_STRATEGY_NAMES.accum_bene_strategy_id', '=', 'ACCUM_BENEFIT_STRATEGY.accum_bene_strategy_id')
                            ->where('ACCUM_BENEFIT_STRATEGY.accum_bene_strategy_id', $request->accum_bene_strategy_id)
                            ->where('ACCUM_BENEFIT_STRATEGY.plan_accum_deduct_id', $request->plan_accum_deduct_id)
                            ->first();
                        return $this->respondWithToken(
                            $this->token(),
                            'Record Added successfully',
                            $reecord,
                        );
                    }
                }
            }
        } else {
            $updateProviderExceptionData = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                ->update([
                    'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $countValidation = DB::table('ACCUM_BENEFIT_STRATEGY')
                ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                ->where('effective_date', $request->effective_date)
                // ->where('pharmacy_status', $request->pharmacy_status)
                ->update([
                    'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                    'formulary_variation_ind' => $request->formulary_variation_ind,
                    'network_part_variation_ind' => $request->network_part_variation_ind,
                    'claim_type_variation_ind' => $request->claim_type_variation_ind,
                    'DATE_TIME_CREATED' => date('d-M-y'),
                    'user_id' => Cache::get('userId'),
                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                    'form_id' => '',
                    'user_id_created' => '',
                    'accum_exclusion_flag' => $request->accum_exclusion_flag,
                    'effective_date' => date('Ymd', strtotime($request->effective_date)),
                    'module_exit' => $request->module_exit,
                    'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                ]);

            return $this->respondWithToken(
                $this->token(),
                'Record Updated successfully',
                $countValidation,
            );
        }
    }

    public function search(Request $request)
    {
        $ndc = DB::table('ACCUM_BENEFIT_STRATEGY')
            ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID')
            ->select('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
            ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->distinct()
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getList($ndcid)
    {
        // $ndclist = DB::table('ACCUM_BENEFIT_STRATEGY')
        //     // ->select( 'DIAGNOSIS_LIST', 'DIAGNOSIS_ID', 'PRIORITY' )
        //     // ->where('ACCUM_BENE_STRATEGY_ID', 'like', '%' . strtoupper($ndcid) . '%')
        //     ->where('ACCUM_BENE_STRATEGY_ID', $ndcid)
        //     // ->orWhere( 'accum_bene_strategy_name', 'like', '%' . strtoupper( $ndcid ) . '%' )
        //     ->get();

        $ndclist = DB::table('ACCUM_BENEFIT_STRATEGY')
            ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
            ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $ndcid)
            ->get();

        // $ndclist = DB::table('ACCUM_BENE_STRATEGY_NAMES')
        //     ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID)'), strtoupper($ndcid))
        //     ->get();

        // $arr = [$ndclist, $ndc];

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDetails($ndcid, $effective_date)
    {
        $ndc = DB::table('ACCUM_BENEFIT_STRATEGY')
            ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID')
            ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', 'like', '%' . $ndcid . '%')
            ->where('effective_date', $effective_date)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function AccumlatedDropDown(Request $request)
    {
        $ndc = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            ->get();
        return $this->respondWithToken($this->token(), 'Data Fetched Successfully', $ndc);
    }
}
