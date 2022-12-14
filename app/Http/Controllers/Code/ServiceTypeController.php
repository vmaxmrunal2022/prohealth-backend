<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceTypeController extends Controller
{
    public function get(Request $request)
    {
        $procedurecodes = DB::table('SERVICE_TYPES')
            ->where('SERVICE_TYPE', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return  $this->respondWithToken($this->token(), '', $procedurecodes);
    }

    public function add(Request $request)
    {

        $procedurecode = DB::table('SERVICE_TYPES')->insert(
            [
                'SERVICE_TYPE' => strtoupper($request->service_type_code),
                'DESCRIPTION' => strtoupper($request->service_type_description),
                'DATE_TIME_CREATED' => date('y-m-d'),
                'USER_ID_CREATED' => '',
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'FORM_ID' => '',
                // 'COMPLETE_CODE_IND' => ''
            ]
        );

        return $this->respondWithToken($this->token(), 'Successfully added', $procedurecode);
    }

    public function delete(Request $request)
    {
        return DB::table('SERVICE_TYPES')->where('SERVICE_TYPE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could not find data');
    }
}
