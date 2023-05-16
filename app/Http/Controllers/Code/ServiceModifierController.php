<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as ValidationValidator;

class ServiceModifierController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $procedurecodes = DB::table('SERVICE_MODIFIERS')
                ->where(DB::raw('UPPER(SERVICE_MODIFIER)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(description)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $procedurecodes);
        }
    }

    public function get_all(Request $request)
    {
        $modifiers = $procedurecodes = DB::table('SERVICE_MODIFIERS')->get();
        if ($modifiers) {
            return $this->respondWithToken($this->token(), '', $modifiers);
        } else {
            return $this->respondWithToken($this->token(), 'data not found', $modifiers);
        }
    }

    public function add(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                "service_modifier" => ['required', 'max:2', Rule::unique('SERVICE_MODIFIERS')->where(function ($q) {
                    $q->whereNotNull('service_modifier');
                })],
                "description" => ['max:35']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                $procedurecode = DB::table('SERVICE_MODIFIERS')->insert(
                    [
                        'SERVICE_MODIFIER' => strtoupper($request->service_modifier),
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => $request->user_id_created,
                        'USER_ID' => $request->user_id,
                        'DATE_TIME_MODIFIED' => $request->date_time_modified,
                        'FORM_ID' => $request->form_id,
                        // 'COMPLETE_CODE_IND' => ''
                    ]
                );
                return  $this->respondWithToken($this->token(), 'Record Added Successfully ', $procedurecode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "service_modifier" => ['required', 'max:2'],
                "description" => ['max:35']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                $procedurecode = DB::table('SERVICE_MODIFIERS')
                    ->where('SERVICE_MODIFIER', strtoupper($request->service_modifier))
                    ->update(
                        [
                            // 'SERVICE_MODIFIER' => $request->service_modifier,
                            'DESCRIPTION' => $request->description,
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID_CREATED' => $request->user_id_created,
                            'USER_ID' => $request->user_id,
                            'DATE_TIME_MODIFIED' => $request->date_time_modified,
                            'FORM_ID' => $request->form_id,
                            // 'COMPLETE_CODE_IND' => ''
                        ]
                    );

                return  $this->respondWithToken($this->token(), 'Record Updated Successfully', $procedurecode);
            }
        }
    }



    public function delete(Request $request)
    {
        if (isset($request->service_modifier)) {
            $delete_service_modifier =  DB::table('SERVICE_MODIFIERS')
                ->where('SERVICE_MODIFIER', $request->service_modifier)
                ->delete();
            if ($delete_service_modifier) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
        }
    }

    public function checkServiceExist(Request $request)
    {
        $check_service_exist = DB::table('SERVICE_MODIFIERS')
            ->where('SERVICE_MODIFIER', $request->search)
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $check_service_exist);
    }
}
