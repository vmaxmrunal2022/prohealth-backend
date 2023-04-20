<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopayStepScheduleController extends Controller
{
    public function get(Request $request)
    {
        if ($request->search == 'days_supply' && $request->id ) {
            $copayStepData = DB::table('COPAY_MATRIX')
            ->where('DAYS_SUPPLY', $request->id)
                ->get();
        } else {
            $copayStepData = DB::table('COPAY_MATRIX')
            ->where('COST_MAX',$request->id)
                // ->where('COST_MAX', '!=', 0)
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
            'copay_list' => ['required'],
        ]);
        $recordcheck = DB::table('COPAY_LIST')->where('copay_list', $request->copay_list)->first();
        if ($validate) {

            if ($request->add_new == '1') {

                if ($recordcheck) {

                    return $this->respondWithToken($this->token(), 'Record Alredy Exists',  $recordcheck, true, 200, 1);

                }else{


                    $addCopaylist1 =  DB::table('COPAY_LIST')
                        ->insert([
                        'COPAY_LIST' => $request->copay_list,
                        'COPAY_DESC' => $request->copay_desc,
                        'DATE_TIME_CREATED' => '',
                        'DATE_TIME_MODIFIED' => ''
                          ]);

                      
                    if(!empty($request->cost_max_form)) {

                        $days_supply = "0";
                        $step_schedule_indicator = "m";
    
                        
    
    
                          $matrix_list_obj = json_decode(json_encode($request->cost_max_form, true));
                          // $effective_date   = $limitation_list->effective_date;
                          // $termination_date = $limitation_list->termination_date;
                          // $limitations_list = $limitation_list->limitations_list;
                          if(!empty($request->cost_max_form)){
      
                              $matrix_list = $matrix_list_obj[0];
      
                              foreach ($matrix_list_obj as $key => $matrix_list) {
      
                              $addCopaymatrix1 =  DB::table('COPAY_MATRIX')
                              ->insert([
                                'copay_list' => $request->copay_list,
                                'cost_max' => $matrix_list->cost_max,
                                // 'copay_amount' => $matrix_list->copay_amount,
                                // 'copay_percentage' => $matrix_list->copay_percentage,
                            //   'days_supply' => $days_supply,
                            //   'step_schedule_indicator' => $step_schedule_indicator
                                ]);
                                 
                              }

            
      
                          }

                         
                          

                    } 

                  

                    if ($addCopaylist1) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $addCopaylist1);
                    }



                }

              

              
            } else if ($request->add_new == 0) {
                if ($recordcheck) {
                    return $this->respondWithToken($this->token(), 'Record Not Found',  [], false, 404, 0);
                }
                if ($request->cost_max) {
                    $days_supply = "0";
                    $cost_max = $request->cost_max;
                    $step_schedule_indicator = "m";


                    $data=DB::table('COPAY_MATRIX')->where('copay_list',$request->copay_list)->delete(); 



                    $matrix_list_obj = json_decode(json_encode($request->cost_max_form, true));
                    // $effective_date   = $limitation_list->effective_date;
                    // $termination_date = $limitation_list->termination_date;
                    // $limitations_list = $limitation_list->limitations_list;
                    if(!empty($request->cost_max_form)){

                        $matrix_list = $matrix_list_obj[0];


                        foreach ($matrix_list_obj as $key => $matrix_list) {

                        $addCopaymatrix2 =  DB::table('COPAY_MATRIX')
                        ->insert([
                        'copay_amount' => $request->copay_amount,
                        'copay_list' => $request->copay_list,
                        'copay_percentage' => $request->copay_percentage,
                        'days_supply' => $days_supply,
                        'cost_max' => $cost_max,
                        'step_schedule_indicator' => $step_schedule_indicator
                          ]);
                           
                        }
                                        return $this->respondWithToken($this->token(), 'Record Updated Successfully !!!', $addCopaymatrix2);


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
