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

        $this->respondWithToken($this->token(), '', $procedurecodes);

    }

    public function add(Request $request)
    {
       
        $procedurecode = DB::table('SERVICE_MODIFIERS')->insert(
            [
                'SERVICE_MODIFIER' => $request->proccodelist,
                'DESCRIPTION' => $request->description,
                'DATE_TIME_CREATED' => '',
                'USER_ID_CREATED' => '',
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'FORM_ID' => '',
                'COMPLETE_CODE_IND' => ''
            ]
        );

        $this->respondWithToken($this->token(), 'Successfully added', $procedurecode);
    }

    public function delete(Request $request)
    {
        DB::table('SERVICE_MODIFIERS')->where('SERVICE_MODIFIER', $request->id)->delete() 
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
