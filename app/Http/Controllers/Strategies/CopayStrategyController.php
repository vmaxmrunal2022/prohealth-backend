<?php

namespace App\Http\Controllers\Strategies;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class CopayStrategyController extends Controller
{

    use AuditTrait;

    public function add(Request $request)
    {
        $createddate = date('Ymd');
        $checkRecordExits = DB::table('COPAY_STRATEGY_NAMES')
            ->where(DB::raw('UPPER(COPAY_STRATEGY_ID)'), strtoupper($request->copay_strategy_id))
            ->count();
        if ($request->has('new')) {
            if ($checkRecordExits) {
                return $this->respondWithToken($this->token(), [['Copay Strategy Id already exists']], $checkRecordExits, false);
                return $this->respondWithToken($this->token(), [['Copay Strategy Id already exists']], $checkRecordExits, false);
            } else {
                $create_copay_strategy_names = DB::table('COPAY_STRATEGY_NAMES')
                    ->insert(
                        [
                            'copay_strategy_id' => $request->copay_strategy_id,
                            'copay_strategy_name' => $request->copay_strategy_name,
                            'DATE_TIME_CREATED' => $createddate,
                            'DATE_TIME_MODIFIED' => $createddate,
                            'USER_ID' => Cache::get('userId'),
                            'USER_ID_CREATED' => $request->copay_strategy_name,
                        ]
                    );
                $create_copay_strategy = DB::table('COPAY_STRATEGY')->insert(
                    [
                        'copay_strategy_id' => $request->copay_strategy_id,
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'formulary_variation_ind' => $request->formulary_variation_ind,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'date_time_created' => $createddate,
                        'DATE_TIME_MODIFIED' => $createddate,
                        'USER_ID' => Cache::get('userId'),
                        'form_id' => '',
                        'user_id_created' =>  Cache::get('userId'),
                        'effective_date' => $request->effective_date,
                        'copay_schedule' => $request->copay_schedule,
                        'MODULE_EXIT' => $request->module_exit
                    ]
                );

                $get_parent = DB::table('COPAY_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(COPAY_STRATEGY_ID)'), strtoupper($request->copay_strategy_id))
                    ->first();

                $save_audit_parent = $this->auditMethod('IN', json_encode($get_parent), 'COPAY_STRATEGY_NAMES');

                $get_child = DB::table('COPAY_STRATEGY')
                    ->where(DB::raw('UPPER(copay_strategy_id)'), strtoupper($request->copay_strategy_id))
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->where('COPAY_SCHEDULE', $request->copay_schedule)
                    ->first();
                $to_audit_child  = $this->auditMethod('IN', json_encode($get_child), 'COPAY_STRATEGY');

                if ($create_copay_strategy) {
                    $val = DB::table('COPAY_STRATEGY')
                        ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                        ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $request->copay_strategy_id)
                        ->get();

                    $exp = DB::table('COPAY_STRATEGY_NAMES')
                        ->select('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                        ->where(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID)'), 'like', '%' . strtoupper($request->copay_strategy_id) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully ',  '');
                }
            }
        } else {

            $checkRecordListsExits = DB::table('COPAY_STRATEGY')
                ->where(DB::raw('UPPER(COPAY_STRATEGY_ID)'), strtoupper($request->copay_strategy_id))
                ->where('effective_date', date('Ymd', strtotime($request->effective_date)))
                ->where('COPAY_SCHEDULE', $request->copay_schedule)
                ->count();
            if ($checkRecordListsExits) {
                if ($request->addUpdate == 0) {
                    $val = DB::table('COPAY_STRATEGY')
                        ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                        ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $request->copay_strategy_id)
                        ->get();

                    $exp = DB::table('COPAY_STRATEGY_NAMES')
                        ->select('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                        ->where(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID)'), 'like', '%' . strtoupper($request->copay_strategy_id) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), [['Copay Schedule ID already exists']], [$val, $exp], false);
                    return $this->respondWithToken($this->token(), [['Copay Schedule ID already exists']], [$val, $exp], false);
                }
                $update_copay_strategy_names = DB::table('COPAY_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(copay_strategy_id)'), strtoupper($request->copay_strategy_id))
                    ->update(
                        [
                            'copay_strategy_name' => $request->copay_strategy_name,
                        ]
                    );

                $update_copay_strategy = DB::table('COPAY_STRATEGY')
                    ->where(DB::raw('UPPER(copay_strategy_id)'), strtoupper($request->copay_strategy_id))
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->where('COPAY_SCHEDULE', $request->copay_schedule)
                    ->update(
                        [
                            'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                            'formulary_variation_ind' => $request->formulary_variation_ind,
                            'network_part_variation_ind' => $request->network_part_variation_ind,
                            'claim_type_variation_ind' => $request->claim_type_variation_ind,
                            'DATE_TIME_MODIFIED' => $createddate,
                            'USER_ID' => Cache::get('userId'),
                            'form_id' => '',
                            'effective_date' => $request->effective_date,
                            'copay_schedule' => $request->copay_schedule,
                            'MODULE_EXIT' => $request->module_exit
                        ]
                    );
                $get_parent = DB::table('COPAY_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(COPAY_STRATEGY_ID)'), strtoupper($request->copay_strategy_id))
                    ->first();

                $save_audit_parent = $this->auditMethod('UP', json_encode($get_parent), 'COPAY_STRATEGY_NAMES');

                $get_child = DB::table('COPAY_STRATEGY')
                    ->where(DB::raw('UPPER(copay_strategy_id)'), strtoupper($request->copay_strategy_id))
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->where('COPAY_SCHEDULE', $request->copay_schedule)
                    ->first();
                $to_audit_child  = $this->auditMethod('UP', json_encode($get_child), 'COPAY_STRATEGY');
                if ($update_copay_strategy) {
                    $val = DB::table('COPAY_STRATEGY')
                        ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                        ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $request->copay_strategy_id)
                        ->get();

                    $exp = DB::table('COPAY_STRATEGY_NAMES')
                        ->select('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                        ->where(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID)'), 'like', '%' . strtoupper($request->copay_strategy_id) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully ', [$val, $exp]);
                }
            } else {
                $update_copay_strategy_names = DB::table('COPAY_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(copay_strategy_id)'), strtoupper($request->copay_strategy_id))
                    ->update(
                        [
                            'copay_strategy_name' => $request->copay_strategy_name,
                        ]
                    );

                $create_copay_strategy = DB::table('COPAY_STRATEGY')->insert(
                    [
                        'copay_strategy_id' => $request->copay_strategy_id,
                        'pharm_type_variation_ind' => $request->pharm_type_variation_ind,
                        'formulary_variation_ind' => $request->formulary_variation_ind,
                        'network_part_variation_ind' => $request->network_part_variation_ind,
                        'claim_type_variation_ind' => $request->claim_type_variation_ind,
                        'date_time_created' => $createddate,
                        'DATE_TIME_MODIFIED' => $createddate,
                        'USER_ID' => Cache::get('userId'),
                        'form_id' => '',
                        'user_id_created' =>  Cache::get('userId'),
                        'effective_date' => $request->effective_date,
                        'copay_schedule' => $request->copay_schedule,
                        'MODULE_EXIT' => $request->module_exit
                    ]
                );
                $get_parent = DB::table('COPAY_STRATEGY_NAMES')
                    ->where(DB::raw('UPPER(COPAY_STRATEGY_ID)'), strtoupper($request->copay_strategy_id))
                    ->first();
                $save_audit_parent = $this->auditMethod('UP', json_encode($get_parent), 'COPAY_STRATEGY_NAMES');
                $get_child = DB::table('COPAY_STRATEGY')
                    ->where(DB::raw('UPPER(copay_strategy_id)'), strtoupper($request->copay_strategy_id))
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->where('COPAY_SCHEDULE', $request->copay_schedule)
                    ->first();
                $to_audit_child  = $this->auditMethod('IN', json_encode($get_child), 'COPAY_STRATEGY');

                if ($create_copay_strategy) {
                    $val = DB::table('COPAY_STRATEGY')
                        ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                        ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $request->copay_strategy_id)
                        ->get();

                    $exp = DB::table('COPAY_STRATEGY_NAMES')
                        ->select('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                        ->where(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID)'), 'like', '%' . strtoupper($request->copay_strategy_id) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', [$val, $exp]);
                }
            }
        }
    }



    public function search(Request $request)
    {

        $ndc = DB::table('COPAY_STRATEGY_NAMES')
            ->select('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
            ->where(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getList($copay_strategy_id)
    {
        $ndclist = DB::table('COPAY_STRATEGY')
            ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $copay_strategy_id)
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getDetails($copay_strategy_id, $effective_date, $copay_schedule)
    {
        $ndc = DB::table('COPAY_STRATEGY')
            ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
            ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $copay_strategy_id)
            ->where('COPAY_SCHEDULE', $copay_schedule)
            ->where('COPAY_STRATEGY.effective_date', date('Ymd', strtotime($effective_date)))
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function CopayDropDown(Request $request)
    {
        $ndc = DB::table('COPAY_STRATEGY_NAMES')
            ->get();

        return $this->respondWithToken($this->token(), 'Data Fetched Successfully', $ndc);
    }
    public function CopayDropDownNew(Request $request)
    {
        $ndc = DB::table('COPAY_STRATEGY_NAMES')
            ->paginate(100);

        return $this->respondWithToken($this->token(), 'Data Fetched Successfully', $ndc);
    }

    public function deleteold(Request $request)
    {
        if (isset($request->copay_strategy_id) && isset($request->effective_date) && isset($request->copay_schedule)) {
            $all_copay_strategy = DB::table('COPAY_STRATEGY')
                ->where('COPAY_STRATEGY_ID', $request->copay_strategy_id)
                ->count();

            if ($all_copay_strategy == 1) {
                $copay_strategy = DB::table('COPAY_STRATEGY')
                    ->where('COPAY_STRATEGY_ID', $request->copay_strategy_id)
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->where('COPAY_SCHEDULE', $request->copay_schedule)
                    ->delete();

                $copay_strategy_name = DB::table('COPAY_STRATEGY_NAMES')
                    ->where('COPAY_STRATEGY_ID', $request->copay_strategy_id)
                    ->delete();
            } else {
                $copay_strategy = DB::table('COPAY_STRATEGY')
                    ->where('COPAY_STRATEGY_ID', $request->copay_strategy_id)
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->where('COPAY_SCHEDULE', $request->copay_schedule)
                    ->delete();
            }

            if ($copay_strategy) {
                $val = DB::table('COPAY_STRATEGY')
                    ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                    ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $request->copay_strategy_id)
                    ->get();

                $exp = DB::table('COPAY_STRATEGY_NAMES')
                    ->select('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                    ->where(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID)'), 'like', '%' . strtoupper($request->copay_strategy_id) . '%')
                    ->get();

                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', [$val, $exp]);
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
            return $this->respondWithToken($this->token(), 'Record deleted Successfully', $copay_strategy);
        } else {
            if (isset($request->copay_strategy_id)) {
                $all_accum_bene_strategy_names = DB::table('COPAY_STRATEGY_NAMES')
                    ->where('copay_strategy_id', $request->copay_strategy_id)
                    ->delete();
                $val = DB::table('COPAY_STRATEGY')
                    ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.COPAY_STRATEGY_ID', '=', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID')
                    ->where('COPAY_STRATEGY.COPAY_STRATEGY_ID', $request->copay_strategy_id)
                    ->get();

                $exp = DB::table('COPAY_STRATEGY_NAMES')
                    ->select('COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name')
                    ->where(DB::raw('UPPER(COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID)'), 'like', '%' . strtoupper($request->copay_strategy_id) . '%')
                    ->get();

                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', [$val, $exp], false);
            }
            return $this->respondWithToken($this->token(), 'Record Not found', 'false');
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->copay_strategy_id) && isset($request->effective_date) && isset($request->copay_schedule)) {
            $all_copay_strategy = DB::table('COPAY_STRATEGY')
                ->where('copay_strategy_id', $request->copay_strategy_id)
                ->where('copay_schedule', $request->copay_schedule)
                ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                ->first();
            if ($all_copay_strategy) {
                $to_audit = DB::table('COPAY_STRATEGY')
                    ->where(DB::raw('UPPER(COPAY_STRATEGY_ID)'), strtoupper($request->copay_strategy_id))
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->where(DB::raw('UPPER(COPAY_SCHEDULE)'), strtoupper($request->copay_schedule))
                    ->first();
                $save_audit = $this->auditMethod('DE', json_encode($to_audit), 'COPAY_STRATEGY');
                $copay_strategy = DB::table('COPAY_STRATEGY')
                    ->where('copay_strategy_id', $request->copay_strategy_id)
                    ->where('copay_schedule', $request->copay_schedule)
                    ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                    ->delete();

                if ($copay_strategy) {
                    $val = DB::table('COPAY_STRATEGY')
                        // ->join('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY.copay_strategy_id', '=', 'COPAY_STRATEGY_NAMES.copay_strategy_id')
                        ->where('COPAY_STRATEGY.copay_strategy_id', $request->copay_strategy_id)
                        ->count();
                    return $this->respondWithToken($this->token(), 'Record Deleted Successfully ', $val);
                }
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } elseif (isset($request->copay_strategy_id)) {
            $to_audit = DB::table('COPAY_STRATEGY')
                ->where(DB::raw('UPPER(COPAY_STRATEGY_ID)'), strtoupper($request->copay_strategy_id))
                ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                ->where(DB::raw('UPPER(COPAY_SCHEDULE)'), strtoupper($request->copay_schedule))
                ->first();
            $save_audit = $this->auditMethod('DE', json_encode($to_audit), 'COPAY_STRATEGY');

            $copay_strategy_names = DB::table('COPAY_STRATEGY_NAMES')
                ->where(DB::raw('UPPER(copay_strategy_id)'), strtoupper($request->copay_strategy_id))
                ->first();
            $to_audit_child = $this->auditMethod('DE', json_encode($copay_strategy_names), 'COPAY_STRATEGY_NAMES');

            $all_accum_bene_strategy_names = DB::table('COPAY_STRATEGY_NAMES')
                ->where('copay_strategy_id', $request->copay_strategy_id)
                ->delete();
            $copay_strategy = DB::table('COPAY_STRATEGY')
                ->where('copay_strategy_id', $request->copay_strategy_id)
                ->delete();
            if ($all_accum_bene_strategy_names) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not found', 'false');
            }
        }
    }
    
}
