<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\returnSelf;

class CouseOfLossController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $procedurecodes = DB::table('CAUSE_OF_LOSS_CODES')

                ->whereRaw('LOWER(CAUSE_OF_LOSS_CODE) LIKE ?', ['%' . strtolower($request->search) . '%'])
                // ->where(DB::raw('UPPER(CAUSE_OF_LOSS_CODE)'), 'like', '%' . $request->search . '%')
                ->orWhere(DB::raw('UPPER(description)'), 'like', '%' . $request->search . '%')
                ->get();

            return  $this->respondWithToken($this->token(), '', $procedurecodes);
        }
    }

    public function add(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                'cause_of_loss_code' => ['required', 'max:8', Rule::unique('CAUSE_OF_LOSS_CODES')->where(function ($q) {
                    $q->whereNotNull('cause_of_loss_code');
                })],
                "description" => ['max:36'],
                "complete_code_ind" => ['max:3']

            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')->insert(
                    [
                        'CAUSE_OF_LOSS_CODE' => $request->cause_of_loss_code,
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => $request->user_id_created,
                        'USER_ID' => $request->user_id,
                        'DATE_TIME_MODIFIED' => $request->date_time_modified,
                        'FORM_ID' => $request->form_id,
                        'COMPLETE_CODE_IND' => $request->complete_code_ind
                    ]
                );

                // $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')->where('CAUSE_OF_LOSS_CODE', $request->cause_of_loss_code)->first();
                return   $this->respondWithToken($this->token(), 'Record Added Successfully', $procedurecode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "cause_of_loss_code" => ['required', 'max:8'],
                "description" => ['max:36'],
                "complete_code_ind" => ['max:3']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')
                    // ->where('CAUSE_OF_LOSS_CODES', 'like', strtoupper($request->benefit_code))
                    ->where('cause_of_loss_code',$request->cause_of_loss_code)
                    ->update(
                        [
                            // 'CAUSE_OF_LOSS_CODE' => strtoupper($request->cause_of_loss_code),
                            'DESCRIPTION' => $request->description,
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID_CREATED' => '',
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'FORM_ID' => '',
                            'COMPLETE_CODE_IND' => $request->complete_code_ind
                        ]
                    );
                // $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')->where('CAUSE_OF_LOSS_CODE', $request->cause_of_loss_code)->first();
                return   $this->respondWithToken($this->token(), 'Record Updated successfully', $procedurecode);
            }
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->cause_of_loss_code)) {
            $delete_cause_of_loss =  DB::table('CAUSE_OF_LOSS_CODES')
                ->where('CAUSE_OF_LOSS_CODE', $request->cause_of_loss_code)
                ->delete();
            if ($delete_cause_of_loss) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found');
        }
    }

    public function checkCauseOfLossExisted(Request $request)
    {
        $check_cause_exist = DB::table('CAUSE_OF_LOSS_CODES')
            ->where(DB::raw('upper(CAUSE_OF_LOSS_CODE)'), $request->search)
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $check_cause_exist);
    }
}
