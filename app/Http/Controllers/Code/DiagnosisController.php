<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DiagnosisController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['required']
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        } else {
            $benefitcodes = DB::table('DIAGNOSIS_CODES')
                ->where('DIAGNOSIS_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('DESCRIPTION', 'like', '%' . $request->search . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $benefitcodes);
        }
    }


    public function getLimitations(Request $request)
    {
        $benefitcodes = DB::table('LIMITATIONS_LIST')
            ->where('LIMITATIONS_LIST', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('LIMITATIONS_LIST_NAME', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $benefitcodes);
    }

    public function add(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                'procedure_code' => ['required', 'max:8', Rule::unique('DIAGNOSIS_CODES')->where(function ($q) {
                    $q->whereNotNull('diagnosis_id');
                })],
                "description" => ['max:35']
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
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
                return response($validator->errors(), 400);
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

    }

    public function delete(Request $request)
    {
        return  DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }

    public function checkDiagnosisCodeExist(Request $request)
    {
        $check_diagnosis_exist = DB::table('DIAGNOSIS_CODES')
            // ->where(DB::raw('UPPER(diagnosis_id)'), strtoupper($request->diagnosis_id))
            ->where('DIAGNOSIS_ID', $request->search)
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $check_diagnosis_exist);
    }
}
