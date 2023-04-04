<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceTypeController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $procedurecodes = DB::table('SERVICE_TYPES')
                ->where(DB::raw('UPPER(SERVICE_TYPE)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(description)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();
            return  $this->respondWithToken($this->token(), '', $procedurecodes);
        }
    }

    public function getallServicetypes(Request $request){

       $service_types_data= DB::table('SERVICE_TYPES')->get();

       if($service_types_data){
        return  $this->respondWithToken($this->token(), 'data Fetched Successfully', $service_types_data);
       }

       else{
        return  $this->respondWithToken($this->token(), 'something went wrong', $service_types_data);

       }






    }

    public function add(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                "service_type" => ['required', 'max:2', Rule::unique('SERVICE_TYPES')->where(function ($q) {
                    $q->whereNotNull('service_type');
                })],
                "description" => ['max:35']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                $procedurecode = DB::table('SERVICE_TYPES')->insert(
                    [
                        'SERVICE_TYPE' => strtoupper($request->service_type),
                        'DESCRIPTION' => strtoupper($request->description),
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => $request->user_id_created,
                        'USER_ID' => $request->user_id,
                        'DATE_TIME_MODIFIED' => $request->date_time_modified,
                        'FORM_ID' => $request->form_id,
                        // 'COMPLETE_CODE_IND' => ''
                    ]
                );
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $procedurecode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "service_type" => ['required', 'max:2'],
                "description" => ['max:35']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $procedurecode = DB::table('SERVICE_TYPES')
                    ->where(DB::raw('UPPER(SERVICE_TYPE)'), strtoupper($request->service_type))
                    ->update(
                        [
                            // 'SERVICE_TYPE' => strtoupper($request->service_type),
                            'DESCRIPTION' => strtoupper($request->description),
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID_CREATED' => $request->user_id_created,
                            'USER_ID' => $request->user_id,
                            'DATE_TIME_MODIFIED' => $request->date_time_modified,
                            'FORM_ID' => $request->form_id,
                            // 'COMPLETE_CODE_IND' => ''
                        ]
                    );
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $procedurecode);
            }
        }
    }

    public function delete(Request $request)
    {
        return DB::table('SERVICE_TYPES')->where('SERVICE_TYPE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could not find data');
    }

    public function checkServiceTypeExist(Request $request)
    {
        $check_service_type_exist = DB::table('SERVICE_TYPES')
            ->where(DB::raw('UPPER(SERVICE_TYPE)'), strtoupper($request->search))
            ->get()
            ->count();
        return $this->respondWithToken($this->token(), '', $check_service_type_exist);
    }
}
