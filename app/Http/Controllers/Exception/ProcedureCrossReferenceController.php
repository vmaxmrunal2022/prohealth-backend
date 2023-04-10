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

    public function search( Request $request )
    {
           
           $entity_names = DB::table( 'ENTITY_NAMES' )
           ->where( 'ENTITY_USER_ID', 'like', '%'.strtoupper( $request->search ).'%' )
           ->orWhere( 'ENTITY_USER_NAME', 'like', '%'.strtoupper( $request->search ).'%' )
           ->get();
           return $this->respondWithToken( $this->token(), '', $entity_names);
    }
}
