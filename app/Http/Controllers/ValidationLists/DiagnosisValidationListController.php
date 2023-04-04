<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DiagnosisValidationListController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $data = DB::table('DIAGNOSIS_EXCEPTIONS as a')
                // ->join('DIAGNOSIS_VALIDATIONS as b','b.DIAGNOSIS_LIST','=','a.DIAGNOSIS_LIST')
                ->where(DB::raw('UPPER(a.DIAGNOSIS_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(a.EXCEPTION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
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
        $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC as a')

            // ->join('DIAGNOSIS_CODES as c','c.DIAGNOSIS_ID','=','a.DIAGNOSIS_ID')

            ->join('DIAGNOSIS_EXCEPTIONS as c', 'c.DIAGNOSIS_LIST', '=', 'a.DIAGNOSIS_LIST')


            ->where('a.DIAGNOSIS_LIST', '=', $diagnosis_list)
            ->where('a.DIAGNOSIS_ID', '=', $diagnosis_id)
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addDiagnosisValidations(Request $request)
    {



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

                    return $this->respondWithToken($this->token(), 'limitation data exists!!!', $limitationsdata);
                } else {
                    $limitation_list = json_decode(json_encode($request->limitations_form, true));
                    $limitation_list = $limitation_list[0];
                    $effective_date = $limitation_list->effective_date;
                    $termination_date = $limitation_list->termination_date;
                    $limitations_list = $limitation_list->limitations_list;

                    $limitdataAddData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                        ->insert([
                            'DIAGNOSIS_LIST' => $request->diagnosis_list,
                            'DIAGNOSIS_ID' => $request->diagnosis_id,
                            'LIMITATIONS_LIST' => $limitations_list,
                            'EFFECTIVE_DATE' => date('Ydm', strtotime($effective_date)),
                            'TERMINATION_DATE' => date('Ydm', strtotime($termination_date)),
                            'DATE_TIME_CREATED' => date('d-M-y'),

                        ]);
                }

                if ($exceptiondata) {
                    return $this->respondWithToken($this->token(), 'exception data exists!!!', $exceptiondata);
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

                    return $this->respondWithToken($this->token(), 'validations data exists!!!', $validationsdata);
                } else {

                    $validationAddData = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->insert([
                            'DIAGNOSIS_LIST' => $request->diagnosis_list,
                            'DIAGNOSIS_ID' => $request->diagnosis_id,
                            'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                            'PRIORITY' => $request->priority,
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
                                'PRIORITY' => $request->priority,
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'USER_ID_MODIFIED' => $request->user_name,
                                'DIAGNOSIS_LIST' => $request->diagnosis_list,
                                'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                            ]);
                    }

                    $limitation_list = json_decode(json_encode($request->limitations_form, true));
                    $limitation_list = $limitation_list[0];
                    $effective_date = $limitation_list->effective_date;
                    $termination_date = $limitation_list->termination_date;
                    $limitations_list = $limitation_list->limitations_list;

                    $updateData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                        ->where('DIAGNOSIS_LIST', $request->diagnosis_list)
                        ->where('DIAGNOSIS_ID', $request->diagnosis_id)
                        ->update([

                            'LIMITATIONS_LIST' => $limitations_list,
                            'EFFECTIVE_DATE' => date('Ydm', strtotime($effective_date)),
                            'TERMINATION_DATE' => date('Ydm', strtotime($termination_date)),

                        ]);
                }

                if ($updateData) {
                    return $this->respondWithToken($this->token(), 'Record  Updated Successfully!!!', $updateData);
                }
            }
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


                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $recordcheck);
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
                return $this->respondWithToken($this->token(), 'Added Succcessfully Limitation!!!');
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
            return $this->respondWithToken($this->token(), 'Limitation Update Successfully !!!', $updateData);
        }
    }


    public function getDiagnosisValidations($diagnosis_list)
    {
        $getData = DB::table('DIAGNOSIS_VALIDATIONS')
            ->join('DIAGNOSIS_EXCEPTIONS', 'DIAGNOSIS_EXCEPTIONS.DIAGNOSIS_LIST', '=', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST')
            ->where('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', $diagnosis_list)
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
                'PRIORITY' => $request->priority
            ]);
        if ($data) {
            return $this->respondWithToken($this->token(), 'updatd successfully', $data);
        }
    }
}
