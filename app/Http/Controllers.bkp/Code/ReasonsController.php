<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReasonsController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $procedurecodes = DB::table('REASON_CODES')
                ->where('REASON_CODE', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('REASON_DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $procedurecodes);
        }
    }

    public function all(Request $request)
    {
        $reasoncodes = DB::table('REASON_CODES')
            ->get();
        return $this->respondWithToken($this->token(), '', $reasoncodes);
    }

    public function add(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                "reason_code" => ['required', 'max:10', Rule::unique('REASON_CODES')->where(function ($q) {
                    $q->whereNotNull('reason_code');
                })],
                "reason_description" => ['max:36'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                $procedurecode = DB::table('REASON_CODES')->insert(
                    [
                        'REASON_CODE' => strtoupper($request->reason_code),
                        'REASON_DESCRIPTION' => strtoupper($request->reason_description),
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'FORM_ID' => ''
                    ]
                );
                $procedurecode  = DB::table('REASON_CODES')->where('reason_code', $request->reason_code)->first();
                return  $this->respondWithToken($this->token(), 'Record Added Successfully', $procedurecode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "reason_code" => ['required', 'max:10'],
                "reason_description" => ['max:36'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $procedurecode = DB::table('REASON_CODES')
                    ->where('reason_code', $request->reason_code)
                    ->update(
                        [
                            // 'REASON_CODE' => strtoupper($request->reason_code),
                            'REASON_DESCRIPTION' => strtoupper($request->reason_description),
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID_CREATED' => '',
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'FORM_ID' => ''
                        ]
                    );
                $procedurecode  = DB::table('REASON_CODES')->where('reason_code', $request->reason_code)->first();
                return  $this->respondWithToken($this->token(), 'Record Updated Successfully', $procedurecode);
            }
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->reason_code)) {
            $delete_reason_code =  DB::table('REASON_CODES')
                ->where('reason_code', $request->reason_code)
                ->delete();
            if ($delete_reason_code) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
        }
    }

    public function checkReasonExist(Request $request)
    {
        $check_reason_exist = DB::table('REASON_CODES')
            ->where('reason_code', $request->search)
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $check_reason_exist);
    }
}
