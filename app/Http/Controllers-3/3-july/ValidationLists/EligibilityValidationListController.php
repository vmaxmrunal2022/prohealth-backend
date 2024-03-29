<?php

namespace App\Http\Controllers\validationlists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EligibilityValidationListController extends Controller
{

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $data = DB::table('ELIG_VALIDATION_LISTS')
                ->where(DB::raw('UPPER(ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(ELIG_VALIDATION_LISTS.ELIG_VALIDATION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $data);
        }
    }


    public function DropDown(Request $request)
    {

        $elig_list_data = DB::table('ELIG_VALIDATION_LISTS')->get();
        return $this->respondWithToken($this->token(), '', $elig_list_data);
    }
    public function DropDownNew(Request $request){
        $searchQuery = $request->search;
        $elig_list_data = DB::table('ELIG_VALIDATION_LISTS')
        ->when($searchQuery, function ($query) use ($searchQuery) {
            $query->where(DB::raw('UPPER(ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID)'), 'like', '%' . strtoupper($searchQuery) . '%');
            $query->orWhere(DB::raw('UPPER(ELIG_VALIDATION_LISTS.ELIG_VALIDATION_NAME)'), 'like', '%' . strtoupper($searchQuery) . '%');
        })->paginate(100);

        return $this->respondWithToken($this->token(), '', $elig_list_data);
    }




    public function getEligibilityDetails($elig_list_id)
    {
        $elig_list_data = DB::table('ELIG_VALIDATION_LISTS')
            ->where('ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID', $elig_list_id)
            ->first();

        $elig_list_data->agelimit_month = '1';
        $elig_list_data->age_limit_day = '2';


        if ($elig_list_data->cardholder_covd == '1') {
            $elig_list_data->cardholder_covd = true;
        } else {
            $elig_list_data->cardholder_covd = false;
        }

        if ($elig_list_data->child_covd == '1') {
            $elig_list_data->child_covd = true;
        } else {
            $elig_list_data->child_covd = false;
        }

        if ($elig_list_data->disabled_dep_covd == '1') {
            $elig_list_data->disabled_dep_covd = true;
        } else {
            $elig_list_data->disabled_dep_covd = false;
        }

        if ($elig_list_data->sig_other_covd == '1') {
            $elig_list_data->sig_other_covd = true;
        } else {
            $elig_list_data->sig_other_covd = false;
        }

        if ($elig_list_data->spouse_covd == '1') {
            $elig_list_data->spouse_covd = true;
        } else {
            $elig_list_data->spouse_covd = false;
        }

        if ($elig_list_data->student_covd == '1') {
            $elig_list_data->student_covd = true;
        } else {
            $elig_list_data->student_covd = false;
        }

        if ($elig_list_data->adult_dep_covd == '1') {
            $elig_list_data->adult_dep_covd = true;
        } else {
            $elig_list_data->adult_dep_covd = false;
        }


        return $this->respondWithToken($this->token(), '', $elig_list_data);
    }

    public function addEligiblityData(Request $request)
    {
        if ($request->new == 1) {
            $validator = Validator::make($request->all(), [
                "elig_validation_id" => ['required', 'max:10', Rule::unique('ELIG_VALIDATION_LISTS')->where(function ($q) {
                    $q->whereNotNull('elig_validation_id');
                })],
                // "elig_validation_name" => ['required','max:25'],
                // "age_limit_opt" => ['required_if:age_limit_opt,1'],
                // "agelimit_month" => ['max:12'],
                // "age_limit_day" => ['max:99'],
                // "cardholder_covd" => ['max:99'],
                // "spouse_covd" => ['max:3', 'numeric'],
                // "child_covd" => ['max:3', 'numeric'],
                // "student_covd" => ['max:3', 'numeric'],
                // "disabled_dep_covd" => ['max:3', 'numeric'],
                // "sig_other_covd" => ['max:3', 'numeric'],
                // "adult_dep_covd" => ['max:3', 'numeric'],
                // "child_age_limit" => ['max:3', 'numeric'],
                // "student_age_limit" => ['max:3', 'numeric'],
                // "dis_dep_age_limit" => ['max:3', 'numeric'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }
            $getEligibilityData = DB::table('ELIG_VALIDATION_LISTS')
                ->where(DB::raw('UPPER(ELIG_VALIDATION_ID)'), strtoupper($request->elig_validation_id))
                ->first();

            if (!$getEligibilityData) {
                $addData = DB::table('ELIG_VALIDATION_LISTS')
                    ->insert([
                        'ELIG_VALIDATION_ID' => $request->elig_validation_id,
                        'ELIG_VALIDATION_NAME' => $request->elig_validation_name,
                        'CARDHOLDER_COVD' => $request->cardholder_covd,
                        'SPOUSE_COVD' => $request->spouse_covd,
                        'CHILD_COVD' => $request->child_covd,
                        'STUDENT_COVD' => $request->student_covd,
                        'DISABLED_DEP_COVD' => $request->disabled_dep_covd,
                        'ADULT_DEP_COVD' => $request->adult_dep_covd,
                        'SIG_OTHER_COVD' => $request->sig_other_covd,
                        'STUDENT_AGE_LIMIT' => $request->student_age_limit,
                        'CHILD_AGE_LIMIT' => $request->child_age_limit,
                        'DIS_DEP_AGE_LIMIT' => $request->dis_dep_age_limit,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'USER_ID' => $request->user_name,
                        'AGE_LIMIT_OPT' => $request->age_limit_opt,
                        'AGE_LIMIT_MMDD' => $request->age_limit_mmdd
                    ]);
                $updateData_new = DB::table('ELIG_VALIDATION_LISTS')
                    ->get();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $updateData_new);
            } else {
                return $this->respondWithToken($this->token(), 'This record is already exists ..!!!');
            }
            // }
        } else if ($request->new == 0) {
            $validator = Validator::make($request->all(), [
                // "elig_validation_id" => ['required', 'max:10'],
                // "elig_validation_name" => ['required','max:25'],
                // "age_limit_opt" => ['required_if:age_limit_opt,1'],
                // "agelimit_month" => ['max:3', 'numeric'],
                // "age_limit_day" => ['max:3', 'numeric'],
                // "cardholder_covd" => ['max:3', 'numeric'],
                // "spouse_covd" => ['max:3', 'numeric'],
                // "child_covd" => ['max:3', 'numeric'],
                // "student_covd" => ['max:3', 'numeric'],
                // "disabled_dep_covd" => ['max:3', 'numeric'],
                // "sig_other_covd" => ['max:3', 'numeric'],
                // "adult_dep_covd" => ['max:3', 'numeric'],
                // "child_age_limit" => ['max:3', 'numeric'],
                // "student_age_limit" => ['max:3', 'numeric'],
                // "dis_dep_age_limit" => ['max:3', 'numeric'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }
            $updateData = DB::table('ELIG_VALIDATION_LISTS')
                ->where('ELIG_VALIDATION_ID', $request->elig_validation_id)
                ->update([
                    'ELIG_VALIDATION_NAME' => $request->elig_validation_name,
                    'CARDHOLDER_COVD' => $request->cardholder_covd,
                    'SPOUSE_COVD' => $request->spouse_covd,
                    'CHILD_COVD' => $request->child_covd,
                    'STUDENT_COVD' => $request->student_covd,
                    'DISABLED_DEP_COVD' => $request->disabled_dep_covd,
                    'ADULT_DEP_COVD' => $request->adult_dep_covd,
                    'SIG_OTHER_COVD' => $request->sig_other_covd,
                    'STUDENT_AGE_LIMIT' => $request->student_age_limit,
                    'CHILD_AGE_LIMIT' => $request->child_age_limit,
                    'DIS_DEP_AGE_LIMIT' => $request->dis_dep_age_limit,
                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                    'AGE_LIMIT_OPT' => $request->age_limit_opt,
                    'AGE_LIMIT_MMDD' => $request->age_limit_mmdd
                ]);
            $updateData_new = DB::table('ELIG_VALIDATION_LISTS')
                ->get();
            // return $updateData_new;
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData_new);
        }
    }

    public function deleteRecord(Request $request)
    {
        // return $request->all();
        $deleteRecord = DB::table('ELIG_VALIDATION_LISTS')
            ->where(DB::raw('UPPER(ELIG_VALIDATION_ID)'), strtoupper($request->elig_validation_id))
            ->delete();

        $updated_list = DB::table('ELIG_VALIDATION_LISTS')
            ->get();

        return $this->respondWithToken($this->token(), 'Record Deleted Successfully', '');
    }
}

