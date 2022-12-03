<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcedureController extends Controller
{
    public function get(Request $request)
    {
        $procedurecodes = DB::table('PROCEDURE_CODES')
            ->where('PROCEDURE_CODE', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $procedurecodes);
    }

    public function add(Request $request)
    {

        $procedurecode = DB::table('PROCEDURE_CODES')->updateOrInsert(
            [
                'PROCEDURE_CODE' => strtoupper($request->procedure_code),
            ],
            [
                'PROCEDURE_CODE' => strtoupper($request->procedure_code),
                'DESCRIPTION' => $request->description,
                'DATE_TIME_CREATED' => date('y-m-d'),
                'USER_ID_CREATED' => '',
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'FORM_ID' => ''
            ]
        );

        $procedurecodes = DB::table('PROCEDURE_CODES')
            ->where('PROCEDURE_CODE', 'like', '%' . strtoupper($request->procedure_code) . '%')
            // ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
            ->first();

        return $this->respondWithToken($this->token(), 'Successfully added', $procedurecodes);
    }

    public function delete(Request $request)
    {
        return DB::table('PROCEDURE_CODES')->where('PROCEDURE_CODE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
