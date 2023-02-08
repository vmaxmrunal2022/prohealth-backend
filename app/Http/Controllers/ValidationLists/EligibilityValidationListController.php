<?php

namespace App\Http\Controllers\validationlists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EligibilityValidationListController extends Controller
{

    public function search(Request $request)
    {
        $data = DB::table('ELIG_VALIDATION_LISTS')
                ->where(DB::raw('UPPER(ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID)'), 'like', '%' .strtoupper($request->search). '%')
                ->orWhere(DB::raw('UPPER(ELIG_VALIDATION_LISTS.ELIG_VALIDATION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();

                return $this->respondWithToken($this->token(), '', $data);
    }




    public function getEligibilityDetails($elig_list_id)
    {
        $elig_list_data = DB::table('ELIG_VALIDATION_LISTS')
                    ->where('ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID', $elig_list_id)
                    ->first();

                $elig_list_data->agelimit_month='1';
                $elig_list_data->age_limit_day='2';


                if($elig_list_data->cardholder_covd == '1'){
                    $elig_list_data->cardholder_covd = true;
                }else{
                    $elig_list_data->cardholder_covd = false;
                }

                if($elig_list_data->child_covd == '1'){
                    $elig_list_data->child_covd = true;
                }else{
                    $elig_list_data->child_covd = false;
                }

                if($elig_list_data->disabled_dep_covd == '1'){
                    $elig_list_data->disabled_dep_covd = true;
                }else{
                    $elig_list_data->disabled_dep_covd = false;
                }

                if($elig_list_data->sig_other_covd == '1'){
                    $elig_list_data->sig_other_covd = true;
                }else{
                    $elig_list_data->sig_other_covd = false;
                }

                if($elig_list_data->spouse_covd == '1'){
                    $elig_list_data->spouse_covd = true;
                }else{
                    $elig_list_data->spouse_covd = false;
                }

                if($elig_list_data->student_covd == '1'){
                    $elig_list_data->student_covd = true;
                }else{
                    $elig_list_data->student_covd = false;
                }


        return $this->respondWithToken($this->token(), '', $elig_list_data);
    }

    public function addEligiblityData(Request $request){
        $getEligibilityData = DB::table('ELIG_VALIDATION_LISTS')
        ->where(DB::raw('UPPER(ELIG_VALIDATION_ID)'),strtoupper($request->elig_validation_id))
        ->first();
        if($request->has('new')){
            if(!$getEligibilityData){
                $addData = DB::table('ELIG_VALIDATION_LISTS')
                ->insert([
                    'ELIG_VALIDATION_ID'=>$request->elig_validation_id,
                    'ELIG_VALIDATION_NAME'=>$request->elig_validation_name,
                    'CARDHOLDER_COVD'=>$request->cardholder_covd,
                    'SPOUSE_COVD'=>$request->spouse_covd,
                    'CHILD_COVD'=>$request->child_covd,
                    'STUDENT_COVD'=>$request->student_covd,
                    'DISABLED_DEP_COVD'=>$request->disabled_dep_covd,
                    'ADULT_DEP_COVD'=>$request->adult_dep_covd,
                    'SIG_OTHER_COVD'=>$request->sig_other_covd,
                    'STUDENT_AGE_LIMIT'=>$request->student_age_limit,
                    'CHILD_AGE_LIMIT'=>$request->child_age_limit,
                    'DIS_DEP_AGE_LIMIT'=>$request->dis_dep_age_limit,
                    'DATE_TIME_CREATED'=>date('d-M-y'),
                    'USER_ID'=>$request->user_name,
                    'AGE_LIMIT_OPT'=>$request->age_limit_opt,
                    'AGE_LIMIT_MMDD'=>$request->agelimit_month
                ]);
                return $this->respondWithToken($this->token(),'Added Successfully...!!!', $addData);
            }else{
                return $this->respondWithToken($this->token(),'This record is already exists ..!!!');
            }

        }else{
            $updateData = DB::table('ELIG_VALIDATION_LISTS')
            ->where('ELIG_VALIDATION_ID',$request->elig_validation_id)
            ->update([
                'ELIG_VALIDATION_NAME'=>$request->elig_validation_name,
                'CARDHOLDER_COVD'=>$request->cardholder_covd,
                'SPOUSE_COVD'=>$request->spouse_covd,
                'CHILD_COVD'=>$request->child_covd,
                'STUDENT_COVD'=>$request->student_covd,
                'DISABLED_DEP_COVD'=>$request->disabled_dep_covd,
                'ADULT_DEP_COVD'=>$request->adult_dep_covd,
                'SIG_OTHER_COVD'=>$request->sig_other_covd,
                'STUDENT_AGE_LIMIT'=>$request->student_age_limit,
                'CHILD_AGE_LIMIT'=>$request->child_age_limit,
                'DIS_DEP_AGE_LIMIT'=>$request->dis_dep_age_limit,
                'DATE_TIME_MODIFIED'=>date('d-M-y'),
                'AGE_LIMIT_OPT'=>$request->age_limit_opt,
                'AGE_LIMIT_MMDD'=>$request->agelimit_month
            ]);
            return $this->respondWithToken($this->token(),'Updated Successfully...!!!', $updateData);
        }
    }
}
