<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopayStepScheduleController extends Controller
{
    public function get(Request $request)
    {
        if ($request->search == 'days_supply') {
            $copayStepData = DB::table('COPAY_MATRIX')
                ->where('DAYS_SUPPLY', '!=', 0)
                ->get();
        } else {
            $copayStepData = DB::table('COPAY_MATRIX')
                ->where('COST_MAX', '!=', 0)
                ->get();
        }
        return $this->respondWithToken($this->token(), '', $copayStepData);
    }

    public function submit(Request $request)
    {

        $validate = $request->validate([
            'copay_list' => ['required', 'unique:copay_list'],
        ]);
        if ($validate) {
            if ($request->has('new')) {
                if ($request->cost_max) {
                    $days_supply = "0";
                    $cost_max = $request->cost_max;
                    $step_schedule_indicator = "m";
                } else {
                    $days_supply =  $request->days_supply;
                    $cost_max = "0";
                    $step_schedule_indicator = "d";
                }

                $addCopay =  DB::table('COPAY_MATRIX')
                    ->insert([
                        'copay_amount' => $request->copay_amount,
                        'copay_list' => $request->copay_list,
                        'copay_percentage' => $request->copay_percentage,
                        'days_supply' => $days_supply,
                        'cost_max' => $cost_max,
                        'step_schedule_indicator' => $step_schedule_indicator
                    ]);
                return $this->respondWithToken($this->token(), 'Added Successfully !!!', $addCopay);
            } else {
                if ($request->cost_max) {
                    $days_supply = "0";
                    $cost_max = $request->cost_max;
                    $step_schedule_indicator = "m";
                } else {
                    $days_supply =  $request->days_supply;
                    $cost_max = "0";
                    $step_schedule_indicator = "d";
                }
                $addCopay =  DB::table('COPAY_MATRIX')
                    ->where('copay_list', 'like', '%' . $request->copay_list . '%')
                    ->update([
                        'copay_amount' => $request->copay_amount,
                        // 'copay_list' => $request->copay_list,
                        'copay_percentage' => $request->copay_percentage,
                        'days_supply' => $days_supply,
                        'cost_max' => $cost_max,
                        'step_schedule_indicator' => $step_schedule_indicator
                    ]);
                return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $addCopay);
            }
        }
    }

    public function checkCopayListExist(Request $request)
    {
        $exist = DB::table('copay_matrix')
                 ->where('copay_list', $request->copay_list)
                 ->count();
        return $this->respondWithToken($this->token(), '', $exist);
    }
}
