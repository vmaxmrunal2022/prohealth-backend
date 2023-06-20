<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcedureController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => 'required'
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $procedurecodes = DB::table('PROCEDURE_CODES')
                ->where(DB::raw('UPPER(PROCEDURE_CODE)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $procedurecodes);
        }
    }


    public function getCodes(Request $request)
    {

        $procedurecodes = DB::table('PROCEDURE_CODES')->get();

        if ($procedurecodes) {

            return $this->respondWithToken($this->token(), 'Data Fetched Successfully', $procedurecodes);
        } else {

            return $this->respondWithToken($this->token(), 'There Was an Error', $procedurecodes);
        }
    }

    public function add(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                'procedure_code' => ['required', 'max:10', Rule::unique('PROCEDURE_CODES')->where(function ($q) {
                    $q->whereNotNull('procedure_code');
                })],
                "description" => ['max:36']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                $procedurecode = DB::table('PROCEDURE_CODES')->insert(
                    [
                        'PROCEDURE_CODE' => strtoupper($request->procedure_code),
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'FORM_ID' => ''
                    ]
                );
                // $procedurecodes = DB::table('PROCEDURE_CODES')
                // ->where('PROCEDURE_CODE', 'like', '%' . strtoupper($request->procedure_code) . '%')
                // ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
                // ->first();

                return $this->respondWithToken($this->token(), 'Record Added successfully!', $procedurecode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'procedure_code' => ['required', 'max:10'],
                "description" => ['max:36']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $procedurecode = DB::table('PROCEDURE_CODES')
                    ->where(DB::raw('UPPER(procedure_code)'), strtoupper($request->procedure_code))
                    ->update(
                        [
                            // 'PROCEDURE_CODE' => strtoupper($request->procedure_code),
                            'DESCRIPTION' => $request->description,
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID_CREATED' => '',
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'FORM_ID' => ''
                        ]
                    );
                return $this->respondWithToken($this->token(), 'Record Updated successfully !', $procedurecode);
            }
        }
    }


    public function delete(Request $request)
    {
        if (isset($request->procedure_code)) {
            $delete_procedure_code =  DB::table('PROCEDURE_CODES')
                ->where('PROCEDURE_CODE', $request->procedure_code)
                ->delete();
            // dd($delete_procedure_code);
            if ($delete_procedure_code) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
        }
    }

    public function checkProcedureCodeExist(Request $reqeust)
    {
        $check_procedure_exist = DB::table('PROCEDURE_CODES')
            ->where(DB::raw('UPPER(procedure_code)'), strtoupper($reqeust->search))
            ->get()
            ->count();
        return $this->respondWithToken($this->token(), '', $check_procedure_exist);
    }
}
