<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceModifierController extends Controller
{
    public function get(Request $request)
    {
        $procedurecodes = DB::table('SERVICE_MODIFIERS')
                                ->where('SERVICE_MODIFIER', 'like', '%'.$request->code.'%')
                                ->orWhere('DESCRIPTION', 'like', '%'.$request->description.'%')
                                ->get();

       return $this->respondWithToken($this->token(), '', $procedurecodes);

    }

    public function add(Request $request)
    {
       
        $procedurecode = DB::table('SERVICE_MODIFIERS')->insert(
            [
                'SERVICE_MODIFIER' => $request->service_modifier_code,
                'DESCRIPTION' => $request->service_modifier_description,
                'DATE_TIME_CREATED' => date('y-m-d'),
                'USER_ID_CREATED' => '',
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'FORM_ID' => '',
                // 'COMPLETE_CODE_IND' => ''
            ]
        );

        return  $this->respondWithToken($this->token(), 'Successfully added', $procedurecode);
    }

    public function delete(Request $request)
    {
        return  DB::table('SERVICE_MODIFIERS')->where('SERVICE_MODIFIER', $request->id)->delete() 
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
