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
                ->where('SERVICE_MODIFIER', 'like', '%' .$request->search . '%')
                ->orWhere('DESCRIPTION', 'like', '%' . $request->search . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $procedurecodes);
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
        return  DB::table('SERVICE_MODIFIERS')->where('SERVICE_MODIFIER', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Record Deleted Successfully')
            : $this->respondWithToken($this->token(), 'Could find data');
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
