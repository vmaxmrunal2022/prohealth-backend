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

       return $this->respondWithToken($this->token(), '', $procedurecodes);

    }

    public function add(Request $request)
    {
       
        $procedurecode = DB::table('PROC_CODE_LIST_NAMES')->insert(
            [
                'PROC_CODE_LIST_ID' => $request->procedure_code,
                'DESCRIPTION' => $request->procedure_description,
                'DATE_TIME_CREATED' => date('y-m-d'),
                'USER_ID_CREATED' => '',
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'FORM_ID' => ''
            ]
        );

       return $this->respondWithToken($this->token(), 'Successfully added', $procedurecode);
    }

    public function delete(Request $request)
    {
        return DB::table('PROC_CODE_LIST_NAMES')->where('PROC_CODE_LIST_ID', $request->id)->delete() 
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
