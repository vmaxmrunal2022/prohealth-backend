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
            return response($validator->errors(), 400);
        } else {
            $procedurecodes = DB::table('REASON_CODES')
                ->where('REASON_CODE', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('REASON_DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $procedurecodes);
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "reason_code" => ['required', 'max:10', Rule::unique('REASON_CODES')->where(function ($q) {
                $q->whereNotNull('reason_code');
            })],
            "description" => ['max:35']
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        } else {
            if ($request->new) {
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
                return  $this->respondWithToken($this->token(), 'Added successfully!', $procedurecode);
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
                return  $this->respondWithToken($this->token(), 'Updated successfully!', $procedurecode);
            }
        }
    }

    public function delete(Request $request)
    {
        return DB::table('REASON_CODES')->where('REASON_CODE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
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
