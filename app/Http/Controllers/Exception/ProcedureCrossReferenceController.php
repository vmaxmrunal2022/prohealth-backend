<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

class ProcedureCrossReferenceController extends Controller
{
    public function ProcedureCodes(Request $request){

        $codes = DB::table('PROCEDURE_CODES')
        ->select('PROCEDURE_CODES.PROCEDURE_CODE','PROCEDURE_CODES.DESCRIPTION')
         ->get();

        return $this->respondWithToken($this->token(), '', $codes);

    }
}
