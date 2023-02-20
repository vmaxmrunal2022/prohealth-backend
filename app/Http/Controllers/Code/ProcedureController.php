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
            return response($validator->errors(), 400);
        } else {
            $procedurecodes = DB::table('PROCEDURE_CODES')
                ->where('PROCEDURE_CODE', 'like', '%' . strtoupper($request->search) . '%')
                // ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $procedurecodes);
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
                return response($validator->errors(), 400);
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

                return $this->respondWithToken($this->token(), 'Added successfully!', $procedurecode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'procedure_code' => ['required', 'max:10'],
                "description" => ['max:36']
            ]);
            if ($validator->fails()) {
                return response($validator->errors(), 400);
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
                return $this->respondWithToken($this->token(), 'Updated successfully !', $procedurecode);
            }
        }
    }


    public function delete(Request $request)
    {
        return DB::table('PROCEDURE_CODES')->where('PROCEDURE_CODE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
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
