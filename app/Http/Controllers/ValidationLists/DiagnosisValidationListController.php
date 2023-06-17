<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DiagnosisValidationListController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // "search" => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            // $data = DB::table('DIAGNOSIS_EXCEPTIONS as a')
            //     // ->join('DIAGNOSIS_VALIDATIONS as b','b.DIAGNOSIS_LIST','=','a.DIAGNOSIS_LIST')
            //     ->where(DB::raw('UPPER(a.DIAGNOSIS_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
            //     ->orWhere(DB::raw('UPPER(a.EXCEPTION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            //     // ->groupBy('DIAGNOSIS_LIST')
            //     ->get();
            $data = DB::table('DIAGNOSIS_EXCEPTIONS')
                // ->join('DIAGNOSIS_VALIDATIONS as b','b.DIAGNOSIS_LIST','=','a.DIAGNOSIS_LIST')
                ->where(DB::raw('UPPER(DIAGNOSIS_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(EXCEPTION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
                // ->groupBy('DIAGNOSIS_LIST')
                ->get();

            return $this->respondWithToken($this->token(), '', $data);
        }
    }

    public function getPriorityDiagnosis($diagnosis_list)
    {
        $data = DB::table('DIAGNOSIS_VALIDATIONS as a')
            ->join('DIAGNOSIS_EXCEPTIONS as b', 'b.DIAGNOSIS_LIST', '=', 'a.DIAGNOSIS_LIST')
            ->join('DIAGNOSIS_CODES as c', 'c.DIAGNOSIS_ID', '=', 'a.DIAGNOSIS_ID')
            ->where('a.DIAGNOSIS_LIST', 'like', '%' . $diagnosis_list . '%')
            ->orderBy('PRIORITY', 'ASC')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getDiagnosisCodeList($search = '')
    {
        $data = DB::table('DIAGNOSIS_CODES')
            ->where('DIAGNOSIS_ID', 'like', '%' . strtoupper($search) . '%')
            ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getLimitationsCode($search = '')
    {
        $data = DB::table('LIMITATIONS_LIST')
            ->where(DB::raw('UPPER(LIMITATIONS_LIST)'), 'like', '%' . strtoupper($search) . '%')
            ->where(DB::raw('UPPER(LIMITATIONS_LIST_NAME)'), 'like', '%' . strtoupper($search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getDiagnosisLimitations($diagnosis_list, $diagnosis_id)
    {
        $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')

            // ->join('DIAGNOSIS_CODES as c','c.DIAGNOSIS_ID','=','a.DIAGNOSIS_ID')

            // ->join('DIAGNOSIS_EXCEPTIONS as c', 'c.DIAGNOSIS_LIST', '=', 'a.DIAGNOSIS_LIST')


            ->where(DB::raw('UPPER(DIAGNOSIS_LIST)'), strtoupper($diagnosis_list))
            ->where(DB::raw('UPPER(DIAGNOSIS_ID)'), strtoupper($diagnosis_id))
            ->get();

        //  ->join('DIAGNOSIS_CODES', 'DIAGNOSIS_CODES.DIAGNOSIS_ID', '=', 'DIAGNOSIS_LIMITATIONS_ASSOC.DIAGNOSIS_ID')

        // ->join('DIAGNOSIS_EXCEPTIONS', 'DIAGNOSIS_EXCEPTIONS.DIAGNOSIS_LIST', '=', 'DIAGNOSIS_LIMITATIONS_ASSOC.DIAGNOSIS_LIST')


        // ->where(DB::raw('UPPER(DIAGNOSIS_LIMITATIONS_ASSOC.DIAGNOSIS_LIST)'), strtoupper($diagnosis_list))
        // ->where(DB::raw('UPPER(DIAGNOSIS_LIMITATIONS_ASSOC.DIAGNOSIS_ID)'), strtoupper($diagnosis_id))
        // ->get();
        // return $diagnosis_id;
        // dd($data[0]->diagnosis_list);
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addDiagnosisValidationscopy(Request $request)
    {
        // dd($request->all());
        if ($request->has('new')) {
            $validator = Validator::make($request->all(), [
                "diagnosis_list" => ['required', 'max:10', Rule::unique('DIAGNOSIS_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('diagnosis_list');
                })],
                "EXCEPTION_NAME" => ['max:35'],
                "diagnosis_list" => ['max:8'],
                "diagnosis_status" => ['max:1', 'alpha_num'],
                "priority" => ['max:1', 'alpha_num'],
                "effective_date" => ['max:8', 'numeric'],
                "termination_date" => ['max:8', 'numeric']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {


                $exceptiondata = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where('DIAGNOSIS_LIST', strtoupper($request->diagnosis_list))
                    ->first();

                $limitationsdata = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                    ->where('DIAGNOSIS_LIST', strtoupper($request->diagnosis_list))
                    ->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))
                    ->first();

                if ($limitationsdata) {

                    return $this->respondWithToken($this->token(), 'Limitation Data Already Exists', $limitationsdata);
                } else {
                    $limitation_list_obj = json_decode(json_encode($request->limitations_form, true));


                    if (!empty($request->limitations_form)) {

                        $limitation_list = $limitation_list_obj[0];
                        // $effective_date   = $limitation_list->effective_date;
                        // $termination_date = $limitation_list->termination_date;
                        // $limitations_list = $limitation_list->limitations_list;
                        foreach ($limitation_list_obj as $key => $limitation_list) {
                            $limitdataAddData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                                ->insert([
                                    'DIAGNOSIS_LIST' => $request->diagnosis_list,
                                    'DIAGNOSIS_ID'   => $request->diagnosis_id,
                                    'limitations_list' => $limitation_list->limitations_list,
                                    'EFFECTIVE_DATE' => $limitation_list->effective_date,
                                    'TERMINATION_DATE' => $limitation_list->termination_date,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                ]);
                        }
                    }
                }

                if ($exceptiondata) {
                    return $this->respondWithToken($this->token(), 'Diagnosis List ID Already Exists', $exceptiondata);
                } else {
                    $exceptionAddData = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->insert([
                            'DIAGNOSIS_LIST' => $request->diagnosis_list,
                            'EXCEPTION_NAME' => $request->exception_name,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name,
                        ]);
                }

                $validationsdata = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->where('DIAGNOSIS_LIST', strtoupper($request->diagnosis_list))
                    ->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))
                    ->first();


                if ($validationsdata) {

                    return $this->respondWithToken($this->token(), 'Diagnosis Validations List Data Already Exists', $validationsdata);
                } else {

                    $validationAddData = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->insert([
                            'DIAGNOSIS_LIST' => $request->diagnosis_list,
                            'DIAGNOSIS_ID' => $request->diagnosis_id,
                            'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                            'PRIORITY' => $request->priority == null ? "1" : $request->priority,
                        ]);
                }
                return $this->respondWithToken($this->token(), 'Record  Added Successfully', $validationAddData);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "diagnosis_list" => ['required', 'max:10'],
                "EXCEPTION_NAME" => ['max:35'],
                "diagnosis_list" => ['max:8'],
                "diagnosis_status" => ['max:1', 'alpha_num'],
                "priority" => ['max:1', 'alpha_num'],
                "effective_date" => ['max:8', 'numeric'],
                "termination_date" => ['max:8', 'numeric']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                if ($request->updateForm == 'update') {

                    $updateData = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->where('DIAGNOSIS_LIST', $request->diagnosis_list)
                        ->update([
                            'EXCEPTION_NAME' => $request->exception_name,
                            'DATE_TIME_MODIFIED' => date('d-M-y'),
                        ]);




                    if (isset($request->diagnosis_id)) {
                        $updateDataValid = DB::table('DIAGNOSIS_VALIDATIONS')
                            ->where('DIAGNOSIS_ID', $request->diagnosis_id)
                            ->where('DIAGNOSIS_LIST', $request->diagnosis_list)
                            ->update([
                                'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                                'PRIORITY' => $request->priority == null ? "1" : $request->priority,
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'USER_ID_MODIFIED' => $request->user_name,
                                'DIAGNOSIS_LIST' => $request->diagnosis_list,
                                'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                            ]);
                    }




                    $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')->where('diagnosis_list', strtoupper($request->diagnosis_list))->delete();



                    $limitation_list_obj = json_decode(json_encode($request->limitations_form, true));
                    // $effective_date   = $limitation_list->effective_date;
                    // $termination_date = $limitation_list->termination_date;
                    // $limitations_list = $limitation_list->limitations_list;
                    if (!empty($request->limitations_form)) {

                        $limitation_list = $limitation_list_obj[0];


                        foreach ($limitation_list_obj as $key => $limitation_list) {
                            $limitdataAddData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                                ->insert([
                                    'DIAGNOSIS_LIST' => $request->diagnosis_list,
                                    'DIAGNOSIS_ID'   => $request->diagnosis_id,
                                    'LIMITATIONS_LIST' => $limitation_list->limitations_list,
                                    'EFFECTIVE_DATE' => $limitation_list->effective_date,
                                    'TERMINATION_DATE' => $limitation_list->termination_date,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                ]);
                        }
                    }
                }

                if ($updateData) {
                    return $this->respondWithToken($this->token(), 'Record  Updated Successfully', $updateData);
                }
            }
        }
    }

    public function addDiagnosisValidations_old(Request $request)
    {
        // dd($request->all());
        $createddate = date('d-M-y');
        // $validation = DB::table('DIAGNOSIS_EXCEPTIONS')
        //     ->where('diagnosis_list', $request->diagnosis_list)
        //     ->get();
        $validation = DB::table('DIAGNOSIS_VALIDATIONS')
            ->where('diagnosis_list', $request->diagnosis_list)
            ->where('diagnosis_id', $request->diagnosis_id)
            ->get();
        // dd($validation);

        if ($request->new == 1) {
            $validator = Validator::make($request->all(), [
                'diagnosis_list' => ['required', 'max:10', Rule::unique('DIAGNOSIS_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('diagnosis_list');
                })],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                // dd($validation);
                if ($validation->count() >= 1) {
                    return $this->respondWithToken($this->token(), 'Record Already Exists', $validation, false);
                }
                $add_names = DB::table('DIAGNOSIS_EXCEPTIONS')->insert(
                    [
                        'diagnosis_list' => $request->diagnosis_list,
                        'exception_name' => $request->exception_name,
                    ]
                );

                $add = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->insert([
                        'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        'DIAGNOSIS_ID' => $request->diagnosis_id,
                        'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                        'DATE_TIME_CREATED' => $createddate,
                        'PRIORITY' => $request->priority == null ? "1" : $request->priority,
                        'USER_ID' => '',
                    ]);

                $limitationsdata = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                    ->where('DIAGNOSIS_LIST', $request->diagnosis_list)
                    ->where('DIAGNOSIS_ID', $request->diagnosis_id)
                    ->first();

                if ($limitationsdata) {

                    return $this->respondWithToken($this->token(), 'Limitation Data Already Exists', $limitationsdata);
                } else {
                    $limitation_list_obj = json_decode(json_encode($request->limitations_form, true));


                    if (!empty($request->limitations_form)) {

                        $limitation_list = $limitation_list_obj[0];
                        // $effective_date   = $limitation_list->effective_date;
                        // $termination_date = $limitation_list->termination_date;
                        // $limitations_list = $limitation_list->limitations_list;
                        foreach ($limitation_list_obj as $key => $limitation_list) {
                            $limitdataAddData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                                ->insert([
                                    'DIAGNOSIS_LIST' => $request->diagnosis_list,
                                    'DIAGNOSIS_ID'   => $request->diagnosis_id,
                                    'LIMITATIONS_LIST' => $limitation_list->limitations_list,
                                    'EFFECTIVE_DATE' => $limitation_list->effective_date,
                                    'TERMINATION_DATE' => $limitation_list->termination_date,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                ]);
                        }
                    }
                }

                $add = DB::table('DIAGNOSIS_VALIDATIONS')->where('DIAGNOSIS_LIST', 'like', '%' . $request->diagnosis_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
            }
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [
                'diagnosis_list' => ['required', 'max:10'],
                "exception_name" => ['max:36'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }

                $update_names = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where('diagnosis_list', $request->diagnosis_list)
                    ->first();


                $checkGPI = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->where('diagnosis_list', $request->diagnosis_list)
                    ->where('diagnosis_id', $request->diagnosis_id)
                    ->get()
                    ->count();

                if ($checkGPI <= "0") {
                    $update = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->insert([
                            'diagnosis_list' => $request->diagnosis_list,
                            'DIAGNOSIS_ID' => $request->diagnosis_id,
                            'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                            'PRIORITY' => $request->priority == null ? "1" : $request->priority,
                        ]);

                    $update = DB::table('DIAGNOSIS_VALIDATIONS')->where('diagnosis_list', 'like', '%' . $request->diagnosis_list . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                } else {




                    $add_names = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->where('diagnosis_list', $request->diagnosis_list)
                        ->update(
                            [
                                'exception_name' => $request->exception_name,

                            ]
                        );

                    $update = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->where('diagnosis_list', $request->diagnosis_list)
                        ->where('diagnosis_id', $request->diagnosis_id)

                        ->update(
                            [
                                'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                                'PRIORITY' => $request->priority == null ? "1" : $request->priority,

                            ]
                        );

                    $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')->where('diagnosis_list', strtoupper($request->diagnosis_list))->delete();



                    $limitation_list_obj = json_decode(json_encode($request->limitations_form, true));
                    // $effective_date   = $limitation_list->effective_date;
                    // $termination_date = $limitation_list->termination_date;
                    // $limitations_list = $limitation_list->limitations_list;
                    if (!empty($request->limitations_form)) {

                        $limitation_list = $limitation_list_obj[0];


                        // foreach ($limitation_list_obj as $key => $limitation_list) {
                        //     $limitdataAddData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                        //         ->insert([
                        //             'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        //             'DIAGNOSIS_ID'   => $request->diagnosis_id,
                        //             // 'LIMITATIONS_LIST' => $limitation_list->limitations_list,
                        //             'EFFECTIVE_DATE' => $limitation_list->effective_date,
                        //             'TERMINATION_DATE' => $limitation_list->termination_date,
                        //             'DATE_TIME_CREATED' => date('d-M-y'),
                        //         ]);
                        // }
                    }

                    $update = DB::table('DIAGNOSIS_VALIDATIONS')->where('diagnosis_list', 'like', '%' . $request->diagnosis_list . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                }
            }
        }
    }

    public function addDiagnosisValidations(Request $request)
    {
        // return $request->all();
        $createddate = date('d-M-y');
        if ($request->new) {

            if (!$request->updateForm) {

                $ifExist = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->get();
                if (count($ifExist) >= 1) {
                    return $this->respondWithToken($this->token(), [["Diagnosis List ID already exists"]], '', false);
                }
            } else {
            }

            if ($request->diagnosis_list && $request->diagnosis_id) {
                $count = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->get()
                    ->count();
                if ($count <= 0) {
                    $add_names = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->insert(
                            [
                                'diagnosis_list' => $request->diagnosis_list,
                                'EXCEPTION_NAME' => $request->exception_name,
                                'date_time_created' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]
                        );
                    $add = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->insert([
                            'diagnosis_list' => $request->diagnosis_list,
                            'diagnosis_id' => $request->diagnosis_id,
                            'diagnosis_status' => $request->diagnosis_status,
                            'date_time_created' => date('d-M-y'),
                            'user_id' => Cache::get('userId'),
                            'date_time_modified' => date('d-M-y'),
                            'priority' => $request->priority == null ? "1" : $request->priority,
                            'form_id' => ''
                        ]);

                    $limitation_list_obj = json_decode(json_encode($request->limitations_form, true));
                    if (!empty($request->limitations_form)) {
                        $limitation_list = $limitation_list_obj[0];
                        foreach ($limitation_list_obj as $key => $limitation_list) {
                            $limitdataAddData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                                ->insert([
                                    'DIAGNOSIS_LIST' => $request->diagnosis_list,
                                    'DIAGNOSIS_ID'   => $request->diagnosis_id,
                                    'LIMITATIONS_LIST' => $limitation_list->limitations_list,
                                    'EFFECTIVE_DATE' => $limitation_list->effective_date,
                                    'TERMINATION_DATE' => $limitation_list->termination_date,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                ]);
                        }
                    }


                    $diag_validation = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                        ->get();
                    $diag_exception = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->where(DB::raw('UPPER(diagnosis_list)'), 'like', '%' . strtoupper($request->diagnosis_list) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', ['', '']);
                } else {
                    $updateProviderExceptionData = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->where('diagnosis_list', $request->diagnosis_list)
                        ->update([
                            'exception_name' => $request->exception_name,
                            'user_id' => Cache::get('userId'),
                            'date_time_modified' => date('d-M-y'),
                            'form_id' => ''
                        ]);
                    $countValidation = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                        ->where(DB::raw('UPPER(diagnosis_id)'), strtoupper($request->diagnosis_id))
                        ->get();

                    if (count($countValidation) >= 1) {
                        return $this->respondWithToken(
                            $this->token(),
                            [['Diagnosis ID already exists']],
                            [['Diagnosis ID already exists']],
                            false
                        );
                    } else {
                        $addProviderValidationData = DB::table('DIAGNOSIS_VALIDATIONS')
                            ->insert([
                                'diagnosis_list' => $request->diagnosis_list,
                                'diagnosis_id' => $request->diagnosis_id,
                                'diagnosis_status' => $request->diagnosis_status,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'USER_ID' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'priority' => $request->priority == null ? "1" : $request->priority,
                                'form_id' => ''
                            ]);
                        // $reecord = DB::table('DIAGNOSIS_EXCEPTIONS')
                        //     ->join('DIAGNOSIS_VALIDATIONS', 'DIAGNOSIS_EXCEPTIONS.diagnosis_list', '=', 'DIAGNOSIS_VALIDATIONS.diagnosis_list')
                        //     ->where('DIAGNOSIS_VALIDATIONS.diagnosis_list', $request->diagnosis_list)
                        //     ->where('DIAGNOSIS_VALIDATIONS.diagnosis_id', $request->diagnosis_id)
                        //     ->first();
                        $diag_validation = DB::table('DIAGNOSIS_VALIDATIONS')
                            ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                            ->where(DB::raw('UPPER(diagnosis_list)'), 'like', '%' . strtoupper($request->diagnosis_list) . '%')
                            ->get();
                        $diag_exception = DB::table('DIAGNOSIS_EXCEPTIONS')
                            ->where(DB::raw('UPPER(diagnosis_list)'), 'like', '%' . strtoupper($request->diagnosis_list) . '%')
                            ->get();

                        $limitation_list_obj = json_decode(json_encode($request->limitations_form, true));
                        $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')->where('diagnosis_list', $request->diagnosis_list)->delete();

                        if (!empty($request->limitations_form)) {
                            $limitation_list = $limitation_list_obj[0];
                            foreach ($limitation_list_obj as $key => $limitation_list) {
                                if ($limitation_list->has('limitations_list')) {
                                    $LIMITATION_LIST = $limitation_list->limitations_list;
                                } else {
                                    $LIMITATION_LIST = '';
                                }
                                $limitdataAddData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                                    ->insert([
                                        'DIAGNOSIS_LIST' => $request->diagnosis_list,
                                        'DIAGNOSIS_ID'   => $request->diagnosis_id,
                                        'LIMITATIONS_LIST' => $LIMITATION_LIST,
                                        'EFFECTIVE_DATE' => $limitation_list->effective_date,
                                        'TERMINATION_DATE' => $limitation_list->termination_date,
                                        'DATE_TIME_CREATED' => date('d-M-y'),
                                    ]);
                            }
                        }
                        return $this->respondWithToken(
                            $this->token(),
                            'Record Added successfully',
                            [$diag_validation, $diag_exception],
                        );
                    }
                }
            } else {
                $updateProviderExceptionData = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where('diagnosis_list', $request->diagnosis_list)
                    ->update([
                        'exception_name' => $request->exception_name,
                        'user_id' => Cache::get('userId'),
                        'date_time_modified' => date('d-M-y'),
                        'form_id' => ''
                    ]);

                $countValidation = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->where(DB::raw('UPPER(diagnosis_id)'), strtoupper($request->diagnosis_id))
                    // ->where('diagnosis_status', $request->diagnosis_status)
                    ->update([
                        'diagnosis_list' => $request->diagnosis_list,
                        'date_time_modified' => date('d-M-y'),
                        'diagnosis_status' => $request->diagnosis_status,
                        'form_id' => ''
                    ]);

                $diag_validation = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->get();
                $diag_exception = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), 'like', '%' . strtoupper($request->diagnosis_list) . '%')
                    ->get();

                return $this->respondWithToken(
                    $this->token(),
                    'Record Updated successfully',
                    [$diag_validation, $diag_exception],
                );
            }
        } else {
            // return $request->all();
            $updateProviderExceptionData = DB::table('DIAGNOSIS_EXCEPTIONS')
                ->where('diagnosis_list', $request->diagnosis_list)
                ->update([
                    'exception_name' => $request->exception_name,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $countValidation = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                ->where(DB::raw('UPPER(diagnosis_id)'), strtoupper($request->diagnosis_id))
                // ->where('diagnosis_status', $request->diagnosis_status)
                ->update([
                    'diagnosis_status' => $request->diagnosis_status,
                    'diagnosis_list' => $request->diagnosis_list,
                    'date_time_modified' => date('d-M-y'),
                    'diagnosis_status' => $request->diagnosis_status,
                    'form_id' => ''
                ]);

            $limitation_list_obj = json_decode(json_encode($request->limitations_form, true));
            if (!empty($request->limitations_form)) {
                $limitation_list = $limitation_list_obj[0];
                foreach ($limitation_list_obj as $key => $limitation_list) {
                    $values = [
                        // 'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        // 'diagnosis_id'  => $request->diagnosis_id,
                        'limitations_list' => isset($limitation_list->limitations_list) ? $limitation_list->limitations_list : null,
                        // 'EFFECTIVE_DATE' => $limitation_list->effective_date,
                        'TERMINATION_DATE' => $limitation_list->termination_date,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                    ];

                    $searchAttributes = [
                        'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        'diagnosis_id'  => $request->diagnosis_id,
                        'EFFECTIVE_DATE' => $limitation_list->effective_date,
                    ];
                    $user = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')->updateOrInsert($searchAttributes, $values);
                    $check = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                        ->where('DIAGNOSIS_LIST', '=', $request->diagnosis_list)
                        ->where('diagnosis_id', '=', $request->diagnosis_id)
                        ->where('EFFECTIVE_DATE', '!=', $limitation_list->effective_date)
                        ->get();
                }
            }

            $diag_validation = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                ->get();
            $diag_exception = DB::table('DIAGNOSIS_EXCEPTIONS')
                ->where(DB::raw('UPPER(diagnosis_list)'), 'like', '%' . strtoupper($request->diagnosis_list) . '%')
                ->get();

            return $this->respondWithToken(
                $this->token(),
                'Record Updated successfully',
                [$diag_validation, $diag_exception],
            );
        }
    }


    public function DiagnosisLimitationAdd(Request $request)
    {
        if ($request->add_new == 1) {



            $recordcheck = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                ->where('DIAGNOSIS_LIST', strtoupper($request->diagnosis_list))
                ->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))
                ->where('LIMITATIONS_LIST', strtoupper($request->limitation_list))
                ->first();

            if ($recordcheck) {


                return $this->respondWithToken($this->token(), 'Diagnosis List Already Exists', $recordcheck);
            } else {


                $addData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                    ->insert([
                        'DIAGNOSIS_LIST' => $request->diagnosis_list,
                        'DIAGNOSIS_ID' => $request->diagnosis_id,
                        'LIMITATIONS_LIST' => $request->limitation_list,
                        'EFFECTIVE_DATE' => date('Ydm', strtotime($request->effective_date)),
                        'TERMINATION_DATE' => date('Ydm', strtotime($request->termination_date)),
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'USER_ID_CREATED' => $request->user_name

                    ]);
            }



            if ($addData) {
                return $this->respondWithToken($this->token(), 'Record Added Succcessfully');
            }
        } else if ($request->add_new == 0) {
            $updateData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                ->where('DIAGNOSIS_LIST', $request->diagnosis_list)
                ->where('DIAGNOSIS_ID', $request->diagnosis_id)
                ->update([
                    'LIMITATIONS_LIST' => $request->limitations_list,

                ]);
        }
        if ($updateData) {
            return $this->respondWithToken($this->token(), 'Record  Update Successfully', $updateData);
        }
    }


    public function getDiagnosisValidations($diagnosis_list)
    {
        $getData = DB::table('DIAGNOSIS_VALIDATIONS')
            ->join('DIAGNOSIS_EXCEPTIONS', 'DIAGNOSIS_EXCEPTIONS.DIAGNOSIS_LIST', '=', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST')
            ->where('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', $diagnosis_list)
            ->orderBy('DIAGNOSIS_VALIDATIONS.PRIORITY')
            ->get();
        return $this->respondWithToken($this->token(), '', $getData);
    }

    public function getDiagnosisDetails($diagnosis_list, $diagnosis_id)
    {
        $data = DB::table('DIAGNOSIS_VALIDATIONS as a')
            ->join('DIAGNOSIS_EXCEPTIONS as b', 'b.DIAGNOSIS_LIST', '=', 'a.DIAGNOSIS_LIST')
            ->where('a.DIAGNOSIS_LIST', $diagnosis_list)
            ->where('a.DIAGNOSIS_ID', $diagnosis_id)
            ->first();
        return $this->respondWithToken($this->token(), '', $data);
    }


    public function updatePriorityDiagnosisValidation(Request $request)
    {
        $data = DB::table('DIAGNOSIS_VALIDATIONS')
            ->where('DIAGNOSIS_LIST', $request->diagnosis_list)
            ->where('DIAGNOSIS_ID', $request->diagnosis_id)
            ->update([
                'PRIORITY' => $request->priority == null ? "1" : $request->priority,
            ]);
        if ($data) {
            return $this->respondWithToken($this->token(), 'updatd successfully', $data);
        }
    }
    public function getAll()
    {
        $data = DB::table('DIAGNOSIS_VALIDATIONS as a')
            ->join('DIAGNOSIS_EXCEPTIONS as b', 'b.DIAGNOSIS_LIST', '=', 'a.DIAGNOSIS_LIST')
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    /**  function will remove only limitation row */
    public function deleteLimitation(Request $request)
    {
        $delete_limitation = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
            ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
            ->where(DB::raw('UPPER(diagnosis_id)'), strtoupper($request->diagnosis_id))
            ->where('effective_date', date('Ymd', strtotime($request->effective_date)))
            ->get();
        return $this->respondWithToken($this->token(), 'Limitation Deleted', $delete_limitation);
    }

    public function deleteRecordold(Request $request)
    {
        if ($request->diagnosis_list) {
            if ($request->diagnosis_id) {
                $delete_diagnosis_id = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->where(DB::raw('UPPER(diagnosis_id)'), strtoupper($request->diagnosis_id))
                    ->delete();
                $diagnosis_validation = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->get();

                if (count($diagnosis_validation) <= 0) {
                    $delete_specialty_list = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                        ->delete();
                    $diagnosis_validation1 = DB::table('DIAGNOSIS_EXCEPTIONS')
                        ->where(DB::raw('UPPER(diagnosis_list)'), 'like', '%' . strtoupper($request->diagnosis_list) . '%')
                        ->get();
                    return $this->respondWithToken($this->token(), "Parent and Child Deleted Successfully ", $diagnosis_validation1, false);
                }
                return $this->respondWithToken($this->token(), "Record Deleted Successfully ", $diagnosis_validation);
            } else {
                $delete_diagnosis_id = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->delete();
                $delete_diagnosis_id = DB::table('DIAGNOSIS_VALIDATIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), strtoupper($request->diagnosis_list))
                    ->delete();
                $diagnosis_exception =
                    DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->where(DB::raw('UPPER(diagnosis_list)'), 'like', '%' . strtoupper($request->diagnosis_list) . '%')
                    ->get();
                return $this->respondWithToken($this->token(), "Record Deleted Successfully ", '');
            }
        }
    }

    public function deleteRecord(Request $request)
    {
        if (isset($request->diagnosis_list) && isset($request->diagnosis_id)) {
            $all_copay_strategy = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where('diagnosis_list', $request->diagnosis_list)
                ->where('diagnosis_id', $request->diagnosis_id)
                // ->where('diagnosis_status',$request->diagnosis_status)
                ->first();
            if ($all_copay_strategy) {
                $copay_strategy = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where('diagnosis_list', $request->diagnosis_list)
                ->where('diagnosis_id', $request->diagnosis_id)
                // ->where('diagnosis_status',$request->diagnosis_status)
                    ->delete();


                    $limitations_delete=DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                    ->where('diagnosis_list', $request->diagnosis_list)
                        ->delete();


                if ($copay_strategy) {
                    $val = DB::table('DIAGNOSIS_VALIDATIONS')
                        // ->join('SPECIALTY_EXCEPTIONS', 'DIAGNOSIS_VALIDATIONS.copay_strategy_id', '=', 'SPECIALTY_EXCEPTIONS.copay_strategy_id')
                        ->where('DIAGNOSIS_VALIDATIONS.diagnosis_list', $request->diagnosis_list)
                        ->count();
                    return $this->respondWithToken($this->token(), 'Record Deleted Successfully ', $val);
                }
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } elseif (isset($request->diagnosis_list)) {
            $all_accum_bene_strategy_names = DB::table('DIAGNOSIS_EXCEPTIONS')
                ->where('diagnosis_list', $request->diagnosis_list)
                ->delete();
            $copay_strategy = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where('diagnosis_list', $request->diagnosis_list)
                ->delete();
                $limitations_delete=DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                ->where('diagnosis_list', $request->diagnosis_list)
                    ->delete();
            if ($all_accum_bene_strategy_names) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not found', 'false');
            }
        }
    }
}
