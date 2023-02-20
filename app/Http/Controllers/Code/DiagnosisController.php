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
            ->where('a.DIAGNOSIS_LIST', '=', $diagnosis_list)
            ->where('a.DIAGNOSIS_ID', '=', $diagnosis_id)
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addDiagnosisValidations(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                'procedure_code' => ['required', 'max:8', Rule::unique('DIAGNOSIS_CODES')->where(function ($q) {
                    $q->whereNotNull('diagnosis_id');
                })],
                "description" => ['max:35']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $benefitcode = DB::table('DIAGNOSIS_CODES')->insert(
                    [
                        'DIAGNOSIS_ID' => strtoupper($request->diagnosis_id),
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'USER_ID_CREATED' => '',
                        'FORM_ID' => '',
                        'COMPLETE_CODE_IND' => ''
                    ]
                );
                $code = DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))->where('DESCRIPTION', strtoupper($request->description))->first();
                return  $this->respondWithToken($this->token(), 'Added Successfully!', $code);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'diagnosis_id' => ['required', 'max:8'],
                "description" => ['max:35']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $benefitcode = DB::table('DIAGNOSIS_CODES')
                    // ->where(DB::raw('UPPER(DIAGNOSIS_ID)'), strtoupper($request->diagnosis_code))
                    ->where('DIAGNOSIS_ID', $request->diagnosis_id)
                    ->update(
                        [
                            'DESCRIPTION' => $request->description,
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'USER_ID_CREATED' => '',
                            'FORM_ID' => '',
                            'COMPLETE_CODE_IND' => ''
                        ]
                    );
                // $code = DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))->where('DESCRIPTION', strtoupper($request->description))->first();
                return  $this->respondWithToken($this->token(), 'Updated Successfully!', $benefitcode);
            }
        }
        // $code = DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))->where('DESCRIPTION', strtoupper($request->description))->first();

    public function getDiagnosisValidations($diagnosis_list)
    {
        $getData = DB::table('DIAGNOSIS_VALIDATIONS')
            ->where('DIAGNOSIS_LIST', $diagnosis_list)
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
