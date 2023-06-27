<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getEligibilityDetails($elig_list_id)
    {
        $elig_list_data = DB::table('ELIG_VALIDATION_LISTS')
            ->where('ELIG_VALIDATION_LISTS.ELIG_VALIDATION_ID', $elig_list_id)
            ->first();

        $elig_list_data->agelimit_month = '1';
        $elig_list_data->age_limit_day = '2';


        if ($elig_list_data->adult_dep_covd == '1') {
            $elig_list_data->adult_dep_covd = true;
        } else {
            $elig_list_data->adult_dep_covd = false;
        }

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


        return $this->respondWithToken($this->token(), '', $elig_list_data);
    }

    public function addEligiblityData(Request $request)
    {
        if ($request->new) {

            $validator = Validator::make($request->all(), [
                "elig_validation_id" => ['required', 'max:10', Rule::unique('ELIG_VALIDATION_LISTS')->where(function ($q) {
                    $q->whereNotNull('elig_validation_id');
                })],
                "elig_validation_name" => ['max:25'],
                "agelimit_month" => ['required_if:age_limit_opt,1'],
                "student_age_limit" => ['numeric'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } else {
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
                        'AGE_LIMIT_MMDD' => $request->agelimit_month
                    ]);
                $updateData_new = DB::table('ELIG_VALIDATION_LISTS')
                    ->get();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $updateData_new);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "elig_validation_id" => ['required', 'max:10'],
                "elig_validation_name" => ['max:25'],
                "agelimit_month" => ['required_if:age_limit_opt,1'],
                "student_age_limit" => ['numeric'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } else {
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
                        'AGE_LIMIT_MMDD' => $request->agelimit_month
                    ]);

                $updateData_new = DB::table('ELIG_VALIDATION_LISTS')
                    ->get();
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData_new);
            }
        }
    }


    public function respondWithToken($token, $responseMessage, $data = [], $status = true, $code = 200, $record = null)
    {
        $response = [
            "success" => $status,
            "message" => $responseMessage,
            "data" => $data,
            "token" => $token ?? '',
            // "token_type" => "bearer",
            "status_code" => $code,

        ];
        if (!is_null($record)) {
            ($record == 1) ? $response['record'] = 'Exist' : '';
            ($record == 0) ? $response['record'] = 'Not Found' : '';
        }
        return \response()->json($response, $code);
    }

    public function defaultAuthGuard()
    {
        return Auth::guard('api');
    }

    public function token()
    {
        return Auth::check() ? $this->defaultAuthGuard()->user()->token() : 'dfsdvf';
    }

    public function Contries(Request $request)
    {
        //$countries = DB::table('COUNTRY_STATES')->where('country_code', 'Coun')->get();
        $countries = DB::table('COUNTRY_STATES')
            ->select('country_code', 'description')
            ->where(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $countries);
    }

    public function ContriesSearch(Request $request)
    {
        if (!empty($c_id)) {
            $countries = DB::table('COUNTRY_STATES')->where(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($c_id) . '%')->paginate(100);
        } else {
            $countries = DB::table('COUNTRY_STATES')->paginate(100);
        }

        return $this->respondWithToken($this->token(), '', $countries);
        // return $countries;
    }

    //public function getStatesOfCountry($countryid)
    public function getStatesOfCountry(Request $request)
    {

        $states = DB::table('COUNTRY_STATES')->whereNot('state_code', '**')->paginate(100);
        return $this->respondWithToken($this->token(), '', $states);

        // $states = DB::table('COUNTRY_STATES')
        //     ->select('COUNTRY_STATES.state_code', 'ZIP_CODES.ZIP_CODE')
        //     ->join('ZIP_CODES', 'ZIP_CODES.state', '=', 'COUNTRY_STATES.state_code')
        //     ->where(DB::raw('UPPER(COUNTRY_STATES.state_code)'), 'like', '%' . strtoupper($request->search) . '%')
        //     ->get();

        // return $this->respondWithToken($this->token(), '', $states);
    }


    //public function getStatesOfCountrySearch($state_code = '')
    public function getStatesOfCountrySearch(Request $request)
    {
        $state_code = $request->search;
        if (!empty($state_code)) {
            $states = DB::table('COUNTRY_STATES')->where(DB::raw('UPPER(STATE_CODE)'), 'like', '%' . strtoupper($state_code) . '%')->get();
        } else {
            $states = DB::table('COUNTRY_STATES')->get();
        }
        return $this->respondWithToken($this->token(), '', $states);
    }

    public function getStatesOfCountrySearchNew(Request $request)
    {
        $state_code = $request->search;
        if (!empty($state_code)) {
            $states = DB::table('COUNTRY_STATES')->where(DB::raw('UPPER(STATE_CODE)'), 'like', '%' . strtoupper($state_code) . '%')->paginate(100);
        } else {
            $states = DB::table('COUNTRY_STATES')->paginate(100);
        }
        return $this->respondWithToken($this->token(), '', $states);
    }
    //Member
    public function getMember(Request $request)
    {
        $memberIds = DB::table('member')
            ->where('member_id', 'like', '%' . $request->search . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $memberIds);
    }

    //Provider
    public function getProvider(Request $request)
    {
        $providers = DB::table('pharmacy_table')
            ->where(DB::raw('UPPER(pharmacy_nabp)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $providers);
    }
}
