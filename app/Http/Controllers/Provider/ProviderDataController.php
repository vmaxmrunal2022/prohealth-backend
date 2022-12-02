<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ProviderDataController extends Controller {
    public function search( Request $request ) {
        $ndc = DB::table( 'PHARMACY_TABLE' )

        ->where( 'PHARMACY_NABP', 'like', '%' . strtoupper( $request->search ) . '%' )
        ->orWhere( 'PHARMACY_NAME', 'like', '%' . strtoupper( $request->search ) . '%' )

        ->get();

        return $this->respondWithToken( $this->token(), '', $ndc );
    }

    public function networkDetails( $ndcid ) {

        $ndc =  DB::table( 'PHARMACY_TABLE' )

        ->where( 'PHARMACY_NABP', 'like', '%' . strtoupper( $ndcid ) . '%' )
        ->first();

        return $this->respondWithToken( $this->token(), '', $ndc );

    }
}