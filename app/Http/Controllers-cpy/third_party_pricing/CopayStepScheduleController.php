<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopayStepScheduleController extends Controller
{
    public function get(Request $request)
    {
        if ($request->search == 'days_supply' && $request->copay_list) {

            // $copayStepData = DB::table('COPAY_MATRIX')
            // ->join('COPAY_LIST','COPAY_LIST.COPAY_LIST','=','COPAY_MATRIX.COPAY_LIST')
            // ->where('COPAY_MATRIX.COPAY_LIST', 'like', '%'.$request->copay_list.'%')
            // ->where('COPAY_MATRIX.COST_MAX', '=', 0)
            // ->get();

            $copayStepData = DB::table('COPAY_LIST')
                // ->where('COPAY_LIST', 'like', '%' . $request->copay_list . '%')
                ->select('COPAY_LIST.*')
                ->join('COPAY_MATRIX','COPAY_LIST.COPAY_LIST','=','COPAY_MATRIX.COPAY_LIST')
                ->where('COPAY_LIST.COPAY_LIST', 'like', '%'.$request->copay_list.'%')
                 ->where('COPAY_MATRIX.DAYS_SUPPLY', '!=', 0)
                 ->distinct()
                 ->get();

                // $data= array_unique($copayStepData);
                //  ->groupby('COPAY_LIST.COPAY_LIST')->get();
                //  ->get()->unique('COPAY_MATRIX.COPAY_LIST');
            return $this->respondWithToken($this->token(), '', $copayStepData);


        }
         else if ($request->search == 'cost_max' && $request->copay_list) {

            
            $copayStepData = DB::table('COPAY_LIST')
                // ->where('COPAY_LIST', 'like', '%' . $request->copay_list . '%')
                ->select('COPAY_LIST.*')
                ->join('COPAY_MATRIX','COPAY_LIST.COPAY_LIST','=','COPAY_MATRIX.COPAY_LIST')
                ->where('COPAY_LIST.COPAY_LIST', 'like', '%'.$request->copay_list.'%')
                 ->where('COPAY_MATRIX.COST_MAX', '!=', 0)
                 ->distinct()
                 ->get();

            // $copayStepData = DB::table('COPAY_LIST')
            //     // ->where('COPAY_LIST', 'like', '%' . $request->copay_list . '%')
            //     ->whereRaw('LOWER(COPAY_LIST) LIKE ?', ['%' . strtolower($request->copay_list) . '%'])
            //                 ->where('COPAY_MATRIX.DAYS_SUPPLY', '=', 0)

            //     ->get();
            return $this->respondWithToken($this->token(), '', $copayStepData);

        }
    }


    public function getList(Request $request)
    {

        if ($request->search == 'days_supply') {

            $copayStepData = DB::table('COPAY_MATRIX')
                ->join('COPAY_LIST', 'COPAY_LIST.COPAY_LIST', '=', 'COPAY_MATRIX.COPAY_LIST')
                ->where('COPAY_MATRIX.COPAY_LIST', $request->copay_list_id)
                ->where('COPAY_MATRIX.COST_MAX', '=', 0)
                ->get();

            return $this->respondWithToken($this->token(), '', $copayStepData);


        } else if ($request->search == 'cost_max') {

            $copayStepData = DB::table('COPAY_MATRIX')
                ->join('COPAY_LIST', 'COPAY_LIST.COPAY_LIST', '=', 'COPAY_MATRIX.COPAY_LIST')
                ->where('COPAY_MATRIX.COPAY_LIST', $request->copay_list_id)
                ->where('COPAY_MATRIX.DAYS_SUPPLY', '=', 0)
                ->get();
            return $this->respondWithToken($this->token(), '', $copayStepData);


        }


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

    public function getmaxList($id)
    {

        $copayStepData = DB::table('COPAY_MATRIX')
            ->where('COST_MAX', $id)
            ->get();

        return $this->respondWithToken($this->token(), '', $copayStepData);

    }

    public function submit(Request $request)
    {
        $validate = $request->validate([
            'copay_list' => ['required'],
        ]);
        $recordcheck = DB::table('COPAY_LIST')->where('copay_list', $request->copay_list)->first();
        if ($validate) {

            if ($request->add_new == '1') {

                if ($recordcheck) {

                    return $this->respondWithToken($this->token(), 'Copay List ID Alredy Exists', $recordcheck, true, 200, 1);

                } else {


                    $addCopaylist1 = DB::table('COPAY_LIST')
                        ->insert([
                            'COPAY_LIST' => $request->copay_list,
                            'COPAY_DESC' => $request->copay_desc,
                            'DATE_TIME_CREATED' => '',
                            'DATE_TIME_MODIFIED' => ''
                        ]);


                    if (!empty($request->cost_max_form)) {

                        $days_supply = "0";
                        $step_schedule_indicator = "m";




                        $matrix_list_obj = json_decode(json_encode($request->cost_max_form, true));
                        // $effective_date   = $limitation_list->effective_date;
                        // $termination_date = $limitation_list->termination_date;
                        // $limitations_list = $limitation_list->limitations_list;
                        if (!empty($request->cost_max_form)) {

                            $matrix_list = $matrix_list_obj[0];

                            foreach ($matrix_list_obj as $key => $matrix_list) {

                                $addCopaymatrix1 = DB::table('COPAY_MATRIX')
                                    ->insert([
                                        'copay_list' => $request->copay_list,
                                        'cost_max' => $matrix_list->cost_max,
                                        'copay_amount' => $matrix_list->copay_amount,
                                        'copay_percentage' => $matrix_list->copay_percentage,
                                        'days_supply' => $days_supply,
                                        'step_schedule_indicator' => $step_schedule_indicator
                                    ]);

                            }



                        }




                    }


                    if (!empty($request->days_supply_form)) {

                        $cost_max = "0";
                        $step_schedule_indicator = "m";




                        $matrix_list_obj = json_decode(json_encode($request->days_supply_form, true));
                        // $effective_date   = $limitation_list->effective_date;
                        // $termination_date = $limitation_list->termination_date;
                        // $limitations_list = $limitation_list->limitations_list;
                        if (!empty($request->days_supply_form)) {

                            $matrix_list = $matrix_list_obj[0];

                            foreach ($matrix_list_obj as $key => $matrix_list) {

                                $addCopaymatrix1 = DB::table('COPAY_MATRIX')
                                    ->insert([
                                        'copay_list' => $request->copay_list,
                                        'cost_max' => $cost_max,
                                        'copay_amount' => $matrix_list->copay_amount,
                                        'copay_percentage' => $matrix_list->copay_percentage,
                                        'days_supply' => $matrix_list->days_supply,
                                        'step_schedule_indicator' => $step_schedule_indicator
                                    ]);

                            }



                        }




                    }



                    if ($addCopaylist1) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $addCopaylist1);
                    }



                }




            } else if ($request->add_new == 0) {


                $addCopaylist1 = DB::table('COPAY_LIST')
                ->where('COPAY_LIST',$request->copay_list)
                ->update([
                    'COPAY_DESC' => $request->copay_desc,
                    'DATE_TIME_CREATED' => '',
                    'DATE_TIME_MODIFIED' => ''
                ]);



                if (!empty($request->cost_max_form)) {

                    $days_supply = "0";
                    $step_schedule_indicator = "m";

                    $data = DB::table('COPAY_MATRIX')->where('copay_list', $request->copay_list)->delete();



                    $matrix_list_obj = json_decode(json_encode($request->cost_max_form, true));

                    if (!empty($request->cost_max_form)) {

                        $matrix_list = $matrix_list_obj[0];

                        foreach ($matrix_list_obj as $key => $matrix_list) {

                            $addCopaymatrix1 = DB::table('COPAY_MATRIX')
                                ->insert([
                                    'copay_list' => $request->copay_list,
                                    'cost_max' => $matrix_list->cost_max,
                                    'copay_amount' => $matrix_list->copay_amount,
                                    'copay_percentage' => $matrix_list->copay_percentage,
                                    'days_supply' => $days_supply,
                                    'step_schedule_indicator' => $step_schedule_indicator
                                ]);

                        }



                    }

                    if ($addCopaymatrix1) {
                        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $addCopaymatrix1);
                    }




                }


                if (!empty($request->days_supply_form)) {

                    $cost_max = "0";
                    $step_schedule_indicator = "m";


                    $data = DB::table('COPAY_MATRIX')->where('copay_list', $request->copay_list)->delete();


                    $matrix_list_obj = json_decode(json_encode($request->days_supply_form, true));

                    if (!empty($request->days_supply_form)) {

                        $matrix_list = $matrix_list_obj[0];

                        foreach ($matrix_list_obj as $key => $matrix_list) {

                            $addCopaymatrix1 = DB::table('COPAY_MATRIX')
                                ->insert([
                                    'copay_list' => $request->copay_list,
                                    'cost_max' => $cost_max,
                                    'copay_amount' => $matrix_list->copay_amount,
                                    'copay_percentage' => $matrix_list->copay_percentage,
                                    'days_supply' => $matrix_list->days_supply,
                                    'step_schedule_indicator' => $step_schedule_indicator
                                ]);

                        }



                    }



                   





                    if ($addCopaymatrix1) {
                        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $addCopaymatrix1);
                    }




                }





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