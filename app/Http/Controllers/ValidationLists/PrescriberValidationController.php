<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PrescriberValidationController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $physicianExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('PHYSICIAN_LIST', 'like', '%' . $request->search . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . $request->search . '%')
                ->orderBy('PHYSICIAN_LIST', 'ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $physicianExceptionData);
        }
    }

    public function getProviderValidationList($physician_list)
    {
        // $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS as a')
        //     // ->select('a.PHYSICIAN_VALIDATIONS', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME','a.EXCEPTION_NAME')
        //     // ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME', 'a.EXCEPTION_NAME')
        //     ->join('PHYSICIAN_TABLE as b ', 'b.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
        //     ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
        //     ->where('a.PHYSICIAN_LIST', 'like', '%' . $physician_list . '%')
        //     ->get();
        $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS')
            ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
            ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
            ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $physician_list)
            ->distinct()
            ->get();

        return $this->respondWithToken($this->token(), '', $physician_validation_list);
    }


    public function getProviderDetails($physicain_list, $physicain_id)
    {
        $data = DB::table('PHYSICIAN_VALIDATIONS as a')
            ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.EXCEPTION_NAME', 'c.PHYSICIAN_LAST_NAME', 'c.PHYSICIAN_FIRST_NAME')
            ->join('PHYSICIAN_EXCEPTIONS as b', 'b.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
            ->join('PHYSICIAN_TABLE as c', 'c.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
            ->where('a.PHYSICIAN_LIST', $physicain_list)
            ->where('a.PHYSICIAN_ID', $physicain_id)
            ->first();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addPrescriberDatacopy(Request $request)
    {

        $getProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->first();

        $getProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->where('PHYSICIAN_ID', $request->physician_id)
            ->first();

        $recordcheck = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('physician_id', $request->physician_id)
            ->first();

        if ($request->has('new')) {

            if (!$request->updateForm) {
                $ifExist = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where(DB::raw('physician_list'), strtoupper($request->physician_list))
                    ->get();

                if (count($ifExist) >= 1) {
                    return $this->respondWithToken($this->token(), [["Prescriber List ID already exists"]], '', false);
                }
            } else {
            }


            if (!$getProviderExceptionData && !$getProviderValidationData) {

                $addProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->insert([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'EXCEPTION_NAME' => $request->exception_name,
                        'USER_ID' => $request->user_name,
                        'DATE_TIME_CREATED' => date('d-M-y')
                    ]);

                $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                    ->insert([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'PHYSICIAN_ID' => $request->physician_id,
                        'PHYSICIAN_STATUS' => $request->physician_status,
                        'USER_ID' => $request->user_name,
                        'DATE_TIME_CREATED' => date('d-M-y')
                    ]);

                if ($addProviderExceptionData) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $addProviderExceptionData);
                }
            }
        } else if ($request->updateForm == 'update') {


            $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('PHYSICIAN_LIST', $request->physician_list)
                ->update([
                    'EXCEPTION_NAME' => $request->exception_name,
                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                ]);

            if ($updateProviderExceptionData) {
                $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where('PHYSICIAN_ID', $request->physician_id)
                    ->where('PHYSICIAN_LIST', $request->physician_list)


                    ->update([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        // 'PHYSICIAN_ID' => $request->physician_id,
                        'PHYSICIAN_STATUS' => $request->physician_status,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'USER_ID' => $request->user_name
                    ]);
                if ($addProviderValidationData) {
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $addProviderValidationData);
                }
            }
        }
    }

    public function addPrescriberData(Request $request)
    {
        $createddate = date('d-M-y');
        $validation = DB::table('PHYSICIAN_EXCEPTIONS')
            ->where('physician_list', $request->physician_list)
            ->get();

        if ($request->new) {
            $validator = Validator::make($request->all(), [
                // 'physician_list' => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('physician_list');
                // })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "exception_name" => ['required', 'max:36'],
                "physician_id" => ['required', 'max:10'],
                // "physician_id"=>['max:10'],
                "physician_status" => ['max:10'],
                "DATE_TIME_CREATED" => ['max:10'],
                "DATE_TIME_MODIFIED" => ['max:10']



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if (!$request->updateForm) {

                    $ifExist = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                        ->get();
                    if (count($ifExist) >= 1) {
                        return $this->respondWithToken($this->token(), [["Prescriber List ID already exists"]], '', false);
                    }
                } else {
                }
                if ($request->physician_list && $request->physician_id) {
                    $count = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                        ->get()
                        ->count();
                    if ($count <= 0) {
                        $add_names = DB::table('PHYSICIAN_EXCEPTIONS')->insert(
                            [
                                'physician_list' => $request->physician_list,
                                'EXCEPTION_NAME' => $request->exception_name,
                                'date_time_created' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]
                        );
                        $add = DB::table('PHYSICIAN_VALIDATIONS')
                            ->insert([
                                'PHYSICIAN_LIST' => $request->physician_list,
                                'PHYSICIAN_ID' => $request->physician_id,
                                'PHYSICIAN_STATUS' => $request->physician_status,
                                'date_time_created' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);

                        $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                            ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                            ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                            ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                            ->distinct()
                            ->get();
                        $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->get();

                        // $add = DB::table('PHYSICIAN_VALIDATIONS')->where('physician_list', 'like', '%' . $request->physician_list . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', [$diag_validation, $diag_exception]);
                    } else {
                        $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->where('PHYSICIAN_LIST', $request->physician_list)
                            ->update([
                                'exception_name' => $request->exception_name,
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);
                        $countValidation = DB::table('PHYSICIAN_VALIDATIONS')
                            ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), strtoupper($request->physician_list))
                            ->where(DB::raw('UPPER(PHYSICIAN_ID)'), strtoupper($request->physician_id))
                            ->get();

                        $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                            ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                            ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                            ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                            ->distinct()
                            ->get();
                        $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->get();

                        if (count($countValidation) >= 1) {
                            return $this->respondWithToken(
                                $this->token(),
                                [['Physician ID already exists']],
                                [$diag_validation, $diag_exception],
                                false
                            );
                        } else {
                            $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                                ->insert([
                                    'physician_list' => $request->physician_list,
                                    'physician_id' => $request->physician_id,
                                    'physician_status' => $request->physician_status,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                    'USER_ID' => Cache::get('userId'),
                                    'date_time_modified' => date('d-M-y'),
                                    'form_id' => ''
                                ]);
                            $reecord = DB::table('PHYSICIAN_EXCEPTIONS')
                                ->join('PHYSICIAN_VALIDATIONS', 'PHYSICIAN_EXCEPTIONS.physician_list', '=', 'PHYSICIAN_VALIDATIONS.physician_list')
                                ->where('PHYSICIAN_VALIDATIONS.physician_list', $request->physician_list)
                                ->where('PHYSICIAN_VALIDATIONS.physician_id', $request->physician_id)
                                ->first();
                            $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                                ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                                ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                                ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                                ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                                ->distinct()
                                ->get();
                            $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                                ->get();
                            return $this->respondWithToken(
                                $this->token(),
                                'Record Added successfully',
                                [$diag_validation, $diag_exception],
                            );
                        }
                    }
                }
            }
        } else {
            $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('physician_list', $request->physician_list)
                ->update([
                    'exception_name' => $request->exception_name,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $countValidation = DB::table('PHYSICIAN_VALIDATIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                // ->where('pharmacy_status', $request->pharmacy_status)
                ->update([
                    'physician_status' => $request->physician_status,
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                ->distinct()
                ->get();
            $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                ->get();
            return $this->respondWithToken(
                $this->token(),
                'Record Updated successfully',
                [$diag_validation, $diag_exception],
            );
        }
    }

    public function searchDropDownPrescriberList()
    {
        $data = DB::table('PHYSICIAN_TABLE')
            ->orWhere('PHYSICIAN_LAST_NAME', 'LIKE', '%' . strtoupper('campB') . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function deleteRecord(Request $request)
    {
        // return $request->all();
        $count = 0;
        foreach ($request->all() as $key => $value) {
            if (is_array($value)) {
                $count++;
            }
        }
        if ($count > 0) {
            $data = $request->all();
            $delete_physician_id = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($data[0]['physician_list']))
                ->delete();
            $delete_physician_id = DB::table('PHYSICIAN_VALIDATIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($data[0]['physician_list']))
                ->delete();
            $diagnosis_exception =
                DB::table('PHYSICIAN_EXCEPTIONS')
                ->get();
            return $this->respondWithToken($this->token(), "Record Deleted Successfully", $diagnosis_exception);
        } else        
        if ($request->physician_list) {
            if ($request->physician_id) {
                $delete_physician_id = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                    ->delete();
                $diagnosis_validation = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->get();
                if (count($diagnosis_validation) <= 0) {
                    $delete_physician_list = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                        ->delete();
                    $diagnosis_validation1 = DB::table('PHYSICIAN_EXCEPTIONS')
                        // ->where(DB::raw('UPPER(physician_list)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), "Parent and Child Deleted Successfully", $diagnosis_validation1, false);
                }
                return $this->respondWithToken($this->token(), "Record Deleted Successfully", $diagnosis_validation);
            } else {
                $delete_physician_id = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->delete();
                $delete_physician_id = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->delete();
                $diagnosis_exception =
                    DB::table('PHYSICIAN_EXCEPTIONS')
                    ->get();
                return $this->respondWithToken($this->token(), "Record Deleted Successfully", $diagnosis_exception);
            }
        }
    }
}
