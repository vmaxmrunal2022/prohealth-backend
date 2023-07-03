<?php

namespace App\Http\Controllers\Speciality;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SpecialityController extends Controller
{
    use AuditTrait;
    public function getAll(Request $request)
    {

        $data = DB::table('SPECIALTY_EXCEPTIONS')
            // ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
            ->select('SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', 'SPECIALTY_EXCEPTIONS.EXCEPTION_NAME')
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $data = DB::table('SPECIALTY_EXCEPTIONS')
                // ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
                ->select('SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', 'SPECIALTY_EXCEPTIONS.EXCEPTION_NAME')
                ->where(DB::raw('UPPER(SPECIALTY_EXCEPTIONS.SPECIALTY_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();
            return $this->respondWithToken($this->token(), '', $data);
        }
    }


    public function getSpecialityList($specialty_id)
    {
        $ndclist = DB::table('SPECIALTY_VALIDATIONS')
            ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
            ->where('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', '=', $specialty_id)
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getSpecialityDetails($specialty_id, $specialty_list)
    {
        $ndc = DB::table('SPECIALTY_VALIDATIONS')
            ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
            ->where('SPECIALTY_VALIDATIONS.SPECIALTY_ID', '=', $specialty_id)
            ->where('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', '=', $specialty_list)
            ->first();
        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function addSpecialitycopy(Request $request)
    {
        if ($request->has('new')) {
            $validator = Validator::make($request->all(), [
                "specialty_list" => ['required', 'max:10', Rule::unique('SPECIALTY_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('specialty_list');
                })],
                "exception_name" => ['max:35'],
                "specialty_status" => ['max:1']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $getSpecialtyExceptionData = DB::table('SPECIALTY_EXCEPTIONS')
                    ->where(DB::raw('UPPER(SPECIALTY_LIST)'), '=', strtoupper($request->specialty_list))
                    ->first();

                $getSpecialtyValidationData = DB::table('SPECIALTY_VALIDATIONS')
                    ->where(DB::raw('UPPER(SPECIALTY_LIST)'), '=', strtoupper($request->specialty_list))
                    ->where(DB::raw('UPPER(SPECIALTY_ID)'), '=', strtoupper($request->specialty_id))
                    ->first();


                if (!$getSpecialtyExceptionData && !$getSpecialtyValidationData) {
                    $addData = DB::table('SPECIALTY_EXCEPTIONS')
                        ->insert([
                            'SPECIALTY_LIST' => $request->specialty_list,
                            'EXCEPTION_NAME' => $request->exception_name,
                            'USER_ID' => $request->user_name,
                        ]);

                    if ($addData) {
                        $data = DB::table('SPECIALTY_VALIDATIONS')
                            ->insert([
                                'SPECIALTY_LIST' => $request->specialty_list,
                                'SPECIALTY_ID' => $request->specialty_id,
                                'SPECIALTY_STATUS' => $request->specialty_status,
                                'USER_ID' => $request->user_name,
                            ]);

                        return $this->respondWithToken($this->token(), 'Added Successfully..!!!', $addData);
                    }
                } else {
                    if (!$getSpecialtyValidationData) {
                        $data = DB::table('SPECIALTY_VALIDATIONS')
                            ->insert([
                                'SPECIALTY_LIST' => $request->specialty_list,
                                'SPECIALTY_ID' => $request->specialty_id,
                                'SPECIALTY_STATUS' => $request->specialty_status,
                                'USER_ID' => $request->user_name,
                            ]);
                        return $this->respondWithToken($this->token(), 'Added Successfully..!!!', $data);
                    } else {
                        return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getSpecialtyValidationData);
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                "specialty_list" => ['required', 'max:10'],
                "exception_name" => ['max:35'],
                "specialty_status" => ['max:1']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $updateData = DB::table('SPECIALTY_EXCEPTIONS')
                    ->where('SPECIALTY_LIST', $request->specialty_list)
                    ->update([
                        'EXCEPTION_NAME' => $request->exception_name
                    ]);
                if ($updateData) {
                    $data = DB::table('SPECIALTY_VALIDATIONS')
                        ->where('SPECIALTY_LIST', $request->specialty_list)
                        ->where('SPECIALTY_ID', $request->specialty_id)
                        ->update([
                            'SPECIALTY_STATUS' => $request->specialty_status
                        ]);
                    return $this->respondWithToken($this->token(), 'Updated Successfully..!!!', $updateData);
                }
            }
        }
    }

    public function addSpeciality(Request $request)
    {
        $createddate = date('d-M-y');

        $validation = DB::table('SPECIALTY_EXCEPTIONS')
            ->where('specialty_list', $request->specialty_list)
            ->get();

        if ($request->add_new) {

            $validator = Validator::make($request->all(), [
                // 'specialty_list' => ['required', 'max:10', Rule::unique('SPECIALTY_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('specialty_list');
                // })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "exception_name" => ['required', 'max:35'],
                "specialty_id" => ['required', 'max:6'],
                "specialty_status" => ['max:1']

            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if (!$request->updateForm) {

                    $ifExist = DB::table('SPECIALTY_EXCEPTIONS')
                        ->where(DB::raw('UPPER(specialty_list)'), strtoupper($request->specialty_list))
                        ->get();
                    if (count($ifExist) >= 1) {
                        return $this->respondWithToken($this->token(), [["Speciality List ID already exists"]], '', false);
                    }
                } else {
                }

                if ($request->specialty_list && $request->specialty_id) {
                    $count = DB::table('SPECIALTY_EXCEPTIONS')
                        ->where(DB::raw('UPPER(specialty_list)'), strtoupper($request->specialty_list))
                        ->get()
                        ->count();
                    if ($count <= 0) {
                        // return date('d-M-y');
                        $add_names = DB::table('SPECIALTY_EXCEPTIONS')->insert(
                            [
                                'specialty_list' => $request->specialty_list,
                                'EXCEPTION_NAME' => $request->exception_name,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'form_id' => ''
                            ]
                        );
                        $add = DB::table('SPECIALTY_VALIDATIONS')
                            ->insert([
                                'specialty_list' => $request->specialty_list,
                                'specialty_id' => $request->specialty_id,
                                'specialty_status' => $request->specialty_status,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'form_id' => ''
                            ]);

                        $add = DB::table('SPECIALTY_VALIDATIONS')->where('specialty_list', 'like', '%' . $request->specialty_list . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
                    } else {
                        $updateProviderExceptionData = DB::table('SPECIALTY_EXCEPTIONS')
                            ->where('specialty_list', $request->specialty_list)
                            ->update([
                                'exception_name' => $request->exception_name,
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);
                        $countValidation = DB::table('SPECIALTY_VALIDATIONS')
                            ->where(DB::raw('UPPER(specialty_list)'), strtoupper($request->specialty_list))
                            ->where(DB::raw('UPPER(specialty_id)'), strtoupper($request->specialty_id))
                            ->get();

                        if (count($countValidation) >= 1) {
                            return $this->respondWithToken(
                                $this->token(),
                                [['Specialty ID already exists']],
                                [['Specialty ID already exists']],
                                false
                            );
                        } else {
                            $addProviderValidationData = DB::table('SPECIALTY_VALIDATIONS')
                                ->insert([
                                    'specialty_list' => $request->specialty_list,
                                    'specialty_id' => $request->specialty_id,
                                    'specialty_status' => $request->specialty_status,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                    'USER_ID' => Cache::get('userId'),
                                    'date_time_modified' => date('d-M-y'),
                                    'form_id' => ''
                                ]);
                            $reecord = DB::table('SPECIALTY_EXCEPTIONS')
                                ->join('SPECIALTY_VALIDATIONS', 'SPECIALTY_EXCEPTIONS.specialty_list', '=', 'SPECIALTY_VALIDATIONS.specialty_list')
                                ->where('SPECIALTY_VALIDATIONS.specialty_list', $request->specialty_list)
                                ->where('SPECIALTY_VALIDATIONS.specialty_id', $request->specialty_id)
                                ->first();
                            return $this->respondWithToken(
                                $this->token(),
                                'Record Added successfully',
                                $reecord,
                            );
                        }
                    }
                }
                //old code               
            }
        } else {
            $updateProviderExceptionData = DB::table('SPECIALTY_EXCEPTIONS')
                ->where('specialty_list', $request->specialty_list)
                ->update([
                    'exception_name' => $request->exception_name,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $countValidation = DB::table('SPECIALTY_VALIDATIONS')
                ->where(DB::raw('UPPER(specialty_list)'), strtoupper($request->specialty_list))
                ->where(DB::raw('UPPER(specialty_id)'), strtoupper($request->specialty_id))
                // ->where('pharmacy_status', $request->pharmacy_status)
                ->update([
                    'specialty_list' => $request->specialty_list,
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            return $this->respondWithToken(
                $this->token(),
                'Record Updated successfully',
                $countValidation,
            );
        }
    }
}
