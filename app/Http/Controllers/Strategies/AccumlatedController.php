<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccumlatedController extends Controller
{
    use AuditTrait;
    public function add(Request $request)
    {
        $createddate = date('Ymd');

        $existdata = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
            ->first();

        if ($request->new) {
            if ($existdata) {
                return $this->respondWithToken($this->token(), "Accumulate Benefit Strategy ID already exists", '', false);
            } else {
                $add_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->insert(
                        [
                            'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                            'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
                            'DATE_TIME_CREATED' => date('Ymd'),
                            'user_id' => Cache::get('userId'),
                            'USER_ID_CREATED' => Cache::get('userId'),
                            'DATE_TIME_MODIFIED' => date('Ymd'),
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
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'user_id' => Cache::get('userId'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                        'form_id' => '',
                        'user_id_created' => Cache::get('userId'),
                        'accum_exclusion_flag' => $request->accum_exclusion_flag,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'module_exit' => $request->module_exit,
                        'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                    ]);

                $acc_beneffit = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                    ->where('effective_date', $request->effective_date)
                    ->where('PLAN_ACCUM_DEDUCT_ID', $request->plan_accum_deduct_id)
                    ->first();
                $record_snap = json_encode($acc_beneffit);
                $save_audit = $this->auditMethod('IN', $record_snap, 'ACCUM_BENEFIT_STRATEGY');
                if ($add) {
                    $val = DB::table('ACCUM_BENEFIT_STRATEGY')
                        ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
                        ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                        ->get();
                    $exp = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                        ->select('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                        ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', '');
                }
            }



            // if ($request->accum_bene_strategy_id && $request->effective_date && $request->plan_accum_deduct_id) {
            //     $count = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            //         ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
            //         ->count();
            //     if ($count <= 0) {
            //         $add_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            //             ->insert(
            //                 [
            //                     'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
            //                     'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
            //                     'DATE_TIME_CREATED' => date('Ymd'),
            //                     'user_id' => Cache::get('userId'),
            //                     'DATE_TIME_MODIFIED' => date('Ymd'),
            //                     'form_id' => ''
            //                 ]
            //             );
            //         $add = DB::table('ACCUM_BENEFIT_STRATEGY')
            //             ->insert([
            //                 'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
            //                 'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
            //                 'formulary_variation_ind' => $request->formulary_variation_ind,
            //                 'network_part_variation_ind' => $request->network_part_variation_ind,
            //                 'claim_type_variation_ind' => $request->claim_type_variation_ind,
            //                 'DATE_TIME_CREATED' => date('Ymd'),
            //                 'user_id' => Cache::get('userId'),
            //                 'DATE_TIME_MODIFIED' => date('Ymd'),
            //                 'form_id' => '',
            //                 'user_id_created' => Cache::get('userId'),
            //                 'accum_exclusion_flag' => $request->accum_exclusion_flag,
            //                 'effective_date' => date('Ymd', strtotime($request->effective_date)),
            //                 'module_exit' => $request->module_exit,
            //                 'plan_accum_deduct_id' => $request->plan_accum_deduct_id,

            //             ]);

            //         $add = DB::table('ACCUM_BENEFIT_STRATEGY')->where('accum_bene_strategy_id', 'like', '%' . $request->accum_bene_strategy_id . '%')->first();
            //         return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
            //     } else {
            //         $updateProviderExceptionData = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            //             ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
            //             ->update([
            //                 'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
            //                 'user_id' => Cache::get('userId'),
            //                 'date_time_modified' => date('Ymd'),
            //                 'form_id' => ''
            //             ]);

            //         $eff_date = $request->effective_date;
            //         $countValidation = DB::table('ACCUM_BENEFIT_STRATEGY')
            //             // ->select('effective_date')
            //             ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
            //             ->whereBetween('effective_date', [$eff_date, $eff_date])
            //             ->get();

            //         if (count($countValidation) >= 1) {
            //             return $this->respondWithToken(
            //                 $this->token(),
            //                 [['Duplicate Child Record']],
            //                 [['Duplicate Child Record']],
            //                 false
            //             );
            //         } else {

            //             $addProviderValidationData = DB::table('ACCUM_BENEFIT_STRATEGY')
            //                 ->insert([
            //                     'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
            //                     'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
            //                     'formulary_variation_ind' => $request->formulary_variation_ind,
            //                     'network_part_variation_ind' => $request->network_part_variation_ind,
            //                     'claim_type_variation_ind' => $request->claim_type_variation_ind,
            //                     'DATE_TIME_CREATED' => date('d-M-y'),
            //                     'user_id' => Cache::get('userId'),
            //                     'DATE_TIME_MODIFIED' => date('d-M-y'),
            //                     'form_id' => '',
            //                     'user_id_created' => '',
            //                     'accum_exclusion_flag' => $request->accum_exclusion_flag,
            //                     'effective_date' => date('Ymd', strtotime($request->effective_date)),
            //                     'module_exit' => $request->module_exit,
            //                     'plan_accum_deduct_id' => $request->plan_accum_deduct_id,

            //                 ]);
            //             $reecord = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            //                 ->join('ACCUM_BENEFIT_STRATEGY', 'ACCUM_BENE_STRATEGY_NAMES.accum_bene_strategy_id', '=', 'ACCUM_BENEFIT_STRATEGY.accum_bene_strategy_id')
            //                 ->where('ACCUM_BENEFIT_STRATEGY.accum_bene_strategy_id', $request->accum_bene_strategy_id)
            //                 ->where('ACCUM_BENEFIT_STRATEGY.plan_accum_deduct_id', $request->plan_accum_deduct_id)
            //                 ->first();
            //             return $this->respondWithToken(
            //                 $this->token(),
            //                 'Record Added successfully',
            //                 $reecord,
            //             );
            //         }
            //     }
            // }
        } else {

            $existDataStrategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
                ->where('PLAN_ACCUM_DEDUCT_ID', $request->plan_accum_deduct_id)
                ->where('effective_date', date('Ymd', strtotime($request->effective_date)))
                ->first();
            if ($existDataStrategy) {
                if ($request->addUpdate == 0) {
                    $val = DB::table('ACCUM_BENEFIT_STRATEGY')
                        ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
                        ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                        ->get();
                    $exp = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                        ->select('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                        ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
                        ->get();
                    return $this->respondWithToken($this->token(), 'Accumulate Benefit Plan ID already exists', [$val, $exp], false);
                }
                $updateAccstrategyName = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                    ->update([
                        'accum_bene_strategy_name' => $request->accum_bene_strategy_name,
                        'user_id' => Cache::get('userId'),
                        'date_time_modified' => date('Ymd'),
                    ]);

                $updateAccstrategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                    ->where('effective_date', $request->effective_date)
                    ->where('PLAN_ACCUM_DEDUCT_ID', $request->plan_accum_deduct_id)
                    ->update([
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'formulary_variation_ind' => $request->formulary_variation_ind,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'user_id' => Cache::get('userId'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                        'form_id' => '',
                        'user_id_created' => '',
                        'accum_exclusion_flag' => $request->accum_exclusion_flag,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'module_exit' => $request->module_exit,
                        'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                    ]);

                $acc_beneffit = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                    ->where('effective_date', $request->effective_date)
                    ->where('PLAN_ACCUM_DEDUCT_ID', $request->plan_accum_deduct_id)
                    ->first();
                $record_snap = json_encode($acc_beneffit);
                $save_audit = $this->auditMethod('UP', $record_snap, 'ACCUM_BENEFIT_STRATEGY');

                if ($updateAccstrategy) {
                    $val = DB::table('ACCUM_BENEFIT_STRATEGY')
                        ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
                        ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                        ->get();
                    $exp = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                        ->select('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                        ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', [$val, $exp]);
                }
            } else {
                $add = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->insert([
                        'accum_bene_strategy_id' => strtoupper($request->accum_bene_strategy_id),
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'formulary_variation_ind' => $request->formulary_variation_ind,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'user_id' => Cache::get('userId'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                        'form_id' => '',
                        'user_id_created' => Cache::get('userId'),
                        'accum_exclusion_flag' => $request->accum_exclusion_flag,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'module_exit' => $request->module_exit,
                        'plan_accum_deduct_id' => $request->plan_accum_deduct_id,
                    ]);

                $acc_beneffit = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where(DB::raw('UPPER(accum_bene_strategy_id)'), strtoupper($request->accum_bene_strategy_id))
                    ->where('effective_date', $request->effective_date)
                    ->where('PLAN_ACCUM_DEDUCT_ID', $request->plan_accum_deduct_id)
                    ->first();
                $record_snap = json_encode($acc_beneffit);
                $save_audit = $this->auditMethod('UP', $record_snap, 'ACCUM_BENEFIT_STRATEGY');
                if ($add) {
                    $val = DB::table('ACCUM_BENEFIT_STRATEGY')
                        ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
                        ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                        ->get();
                    $exp = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                        ->select('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                        ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', [$val, $exp]);
                }
            }
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

    public function getDetails($accum_bene_strategy_id, $effective_date, $plan_accum_deduct_id)
    {
        $eff_date = date(strtotime('Y-m-d', $effective_date));
        $ndc = DB::table('ACCUM_BENEFIT_STRATEGY')
            ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
            ->where('ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID', $accum_bene_strategy_id)
            ->where('ACCUM_BENEFIT_STRATEGY.effective_date', $effective_date)
            ->where('ACCUM_BENEFIT_STRATEGY.plan_accum_deduct_id', $plan_accum_deduct_id)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function AccumlatedDropDown(Request $request)
    {
        $ndc = DB::table('ACCUM_BENE_STRATEGY_NAMES')
            ->get();
        return $this->respondWithToken($this->token(), 'Data Fetched Successfully', $ndc);
    }

    public function getAllAcuumlatedBenefits(Request $request){

        $existdata = DB::table('accum_bene_strategy_names')
        ->get();

        return $this->respondWithToken($this->token(), 'Successfully added', $existdata);


    }

    public function deleteold(Request $request)
    {
        if (isset($request->accum_bene_strategy_id) && isset($request->effective_date) && isset($request->plan_accum_deduct_id)) {
            $all_accum_bene_strategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                ->count();
            if ($all_accum_bene_strategy == 1) {
                $all_accum_bene_strategy_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->where('ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                    ->delete();

                $all_accum_bene_strategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where('ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                    ->delete();
                $val = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
                    ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                    ->get();
                $exp = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->select('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                    ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
                    ->get();
                // $ndclist = DB::table('ACCUM_BENEFIT_STRATEGY')
                // ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
                // ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $ndcid)
                // ->get();
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', [$exp, $val]);
            } else {
                $all_accum_bene_strategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where('ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                    ->where('PLAN_ACCUM_DEDUCT_ID', $request->plan_accum_deduct_id)
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->delete();
                $val = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'ACCUM_BENEFIT_STRATEGY.ACCUM_BENE_STRATEGY_ID')
                    ->where('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                    ->get();
                $exp = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->select('ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_sat_name')
                    ->where(DB::raw('UPPER(ACCUM_BENE_STRATEGY_ID)'), strtoupper($request->accum_bene_strategy_id))
                    ->get();
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', [$exp, $val]);
            }


            // return $this->respondWithToken($this->token(), 'Record deleted Successfully', $all_accum_bene_strategy);
        } else {
            if (isset($request->accum_bene_strategy_id)) {
                $all_accum_bene_strategy_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                    ->where('ACCUM_BENE_STRATEGY_ID', $request->accum_bene_strategy_id)
                    ->delete();
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', '', false);
            }
            return $this->respondWithToken($this->token(), 'Record Not found', 'false');
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->accum_bene_strategy_id) && isset($request->effective_date) && isset($request->plan_accum_deduct_id)) {
            $all_accum_strategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                ->first();
            if ($all_accum_strategy) {
                $accum_strategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                    ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                    ->where('plan_accum_deduct_id', $request->plan_accum_deduct_id)
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->delete();
                if ($accum_strategy) {
                    $val = DB::table('ACCUM_BENEFIT_STRATEGY')
                        // ->join('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENEFIT_STRATEGY.copay_strategy_id', '=', 'ACCUM_BENE_STRATEGY_NAMES.copay_strategy_id')
                        ->where('ACCUM_BENEFIT_STRATEGY.accum_bene_strategy_id', $request->accum_bene_strategy_id)
                        ->count();
                    return $this->respondWithToken($this->token(), 'Record Deleted Successfully ', $val);
                }
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } elseif (isset($request->copay_strategy_id)) {
            $all_accum_bene_strategy_names = DB::table('ACCUM_BENE_STRATEGY_NAMES')
                ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                ->delete();
            $accum_strategy = DB::table('ACCUM_BENEFIT_STRATEGY')
                ->where('accum_bene_strategy_id', $request->accum_bene_strategy_id)
                ->delete();
            if ($all_accum_bene_strategy_names) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not found', 'false');
            }
        }
    }

}
