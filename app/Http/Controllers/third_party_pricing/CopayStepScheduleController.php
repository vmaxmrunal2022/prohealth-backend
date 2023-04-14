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

    public function getDaysSupply(Request $requeest)
    {
        $copayStepData = DB::table('COPAY_MATRIX')
            ->where('DAYS_SUPPLY', '!=', 0)
            ->get();

        return $this->respondWithToken($this->token(), 'for days supply', $copayStepData);
    }

    public function getMaxCost(Request $requeest)
    {
        $copayStepData = DB::table('COPAY_MATRIX')
            ->where('COST_MAX', '!=', 0)
            ->get();

        return $this->respondWithToken($this->token(), 'for days supply', $copayStepData);
    }

    public function submit(Request $request)
    {
        $validate = $request->validate([
            'copay_list' => ['required', 'unique:copay_list'],
        ]);
        $copayStepSchedule = DB::table('COPAY_MATRIX')->where('copay_list',  $request->copay_list)->first();
        if ($validate) {
            if ($request->add_new == 1) {

                if ($copayStepSchedule->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Record Alredy Exists',  $copayStepSchedule, true, 200, 1);
                }
                if ($request->cost_max) {
                    $days_supply = "0";
                    $cost_max = $request->cost_max;
                    $step_schedule_indicator = "m";
                } else {
                    $days_supply =  $request->days_supply;
                    $cost_max = "0";
                    $step_schedule_indicator = "d";



                    $addCopaylist =  DB::table('COPAY_LIST')
                    ->insert([
                    'COPAY_LIST' => $request->copay_list,
                    'COPAY_DESC' => $request->copay_desc,
                    'DATE_TIME_CREATED' => '',
                    'USER_ID' => $cost_max,
                    'DATE_TIME_MODIFIED' => ''
                      ]);



                    
                    $matrix_list_obj = json_decode(json_encode($request->matrix_form, true));
                    // $effective_date   = $limitation_list->effective_date;
                    // $termination_date = $limitation_list->termination_date;
                    // $limitations_list = $limitation_list->limitations_list;
                    if(!empty($request->matrix_form)){

                        $matrix_list = $matrix_list_obj[0];


                        foreach ($matrix_list_obj as $key => $matrix_list) {

                        $addCopaymatrix =  DB::table('COPAY_MATRIX')
                        ->insert([
                        'copay_amount' => $request->copay_amount,
                        'copay_list' => $request->copay_list,
                        'copay_percentage' => $request->copay_percentage,
                        'days_supply' => $days_supply,
                        'cost_max' => $cost_max,
                        'step_schedule_indicator' => $step_schedule_indicator
                          ]);
                           
                        }

                    }

                    


            
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $addCopaymatrix);
                }

              
            } else if ($request->add_new == 0) {
                if ($copayStepSchedule->count() < 1) {
                    return $this->respondWithToken($this->token(), 'Record Not Found',  [], false, 404, 0);
                }
                if ($request->cost_max) {
                    $days_supply = "0";
                    $cost_max = $request->cost_max;
                    $step_schedule_indicator = "m";
                } else {
                    $days_supply =  $request->days_supply;
                    $cost_max = "0";
                    $step_schedule_indicator = "d";
                }
                $updateCopayMatrix =  DB::table('COPAY_MATRIX')
                    ->where('copay_list', 'like', '%' . $request->copay_list . '%')
                    ->update([
                        'copay_amount' => $request->copay_amount,
                        // 'copay_list' => $request->copay_list,
                        'copay_percentage' => $request->copay_percentage,
                        'days_supply' => $days_supply,
                        'cost_max' => $cost_max,
                        'step_schedule_indicator' => $step_schedule_indicator
                    ]);
                return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateCopayMatrix);
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
