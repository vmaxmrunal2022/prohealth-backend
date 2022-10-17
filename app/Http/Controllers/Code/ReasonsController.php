<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReasonsController extends Controller
{
    public function get(Request $request)
    {
        $procedurecodes = DB::table('REASON_CODES')
                                ->where('REASON_CODE', 'like', '%'.$request->code.'%')
                                ->orWhere('REASON_DESCRIPTION', 'like', '%'.$request->description.'%')
                                ->get();

        $this->respondWithToken($this->token(), '', $procedurecodes);

    }

    public function add(Request $request)
    {
       
        $procedurecode = DB::table('REASON_CODES')->insert(
            [
                'REASON_CODE' => $request->proccodelist,
                'REASON_DESCRIPTION' => $request->description,
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
        DB::table('REASON_CODES')->where('REASON_CODE', $request->id)->delete() 
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
