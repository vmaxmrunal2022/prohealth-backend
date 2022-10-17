<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcedureController extends Controller
{
    public function get(Request $request)
    {
        $procedurecodes = DB::table('PROC_CODE_LIST_NAMES')
                                ->where('PROC_CODE_LIST_ID', 'like', '%'.$request->code.'%')
                                ->orWhere('DESCRIPTION', 'like', '%'.$request->description.'%')
                                ->get();

        $this->respondWithToken($this->token(), '', $procedurecodes);

    }

    public function add(Request $request)
    {
       
        $procedurecode = DB::table('PROC_CODE_LIST_NAMES')->insert(
            [
                'PROC_CODE_LIST_ID' => $request->proccodelist,
                'DESCRIPTION' => $request->description,
                'DATE_TIME_CREATED' => '',
                'USER_ID_CREATED' => '',
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'FORM_ID' => ''
            ]
        );

        $this->respondWithToken($this->token(), 'Successfully added', $procedurecode);
    }

    public function delete(Request $request)
    {
        DB::table('PROC_CODE_LIST_NAMES')->where('PROC_CODE_LIST_ID', $request->id)->delete() 
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
