<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class BenefitDerivationController extends Controller {

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );
        $effective_date = date( 'Ymd', strtotime( $request->effective_date ) );
        $terminate_date = date( 'Ymd', strtotime( $request->termination_date ) );

        if ( $request->has( 'new' ) ) {

            $accum_benfit_stat_names = DB::table( 'BENEFIT_DERIVATION_NAMES' )->insert(
                [
                    'benefit_derivation_id' => strtoupper( $request->benefit_derivation_id ),
                    'description'=>$request->description,

                ]
            );

            $accum_benfit_stat = DB::table( 'BENEFIT_DERIVATION' )->insert(
                [
                    'benefit_derivation_id' => strtoupper( $request->benefit_derivation_id ),
                    'service_type'=>$request->service_type,

                ]
            );

            $benefitcode = DB::table( 'BENEFIT_DERIVATION_NAMES' )->where( 'benefit_derivation_id', 'like', '%'.$request->benefit_derivation_id .'%' )->first();

        } else {

            $benefitcode = DB::table( 'BENEFIT_DERIVATION_NAMES' )
            ->where( 'benefit_derivation_id', $request->benefit_derivation_id )

            ->update(
                [
                    'description'=>$request->description,

                ]
            );

            $accum_benfit_stat = DB::table( 'BENEFIT_DERIVATION' )
            ->where( 'benefit_derivation_id', $request->benefit_derivation_id )
            ->where( 'benefit_code', $request->benefit_code )
            ->where( 'service_type', $request->service_type )


            ->update(
                [
                    'effective_date'=>$effective_date,
                    'termination_date'=>$terminate_date,

                ]
            );

            $benefitcode = DB::table( 'BENEFIT_DERIVATION' )->where( 'benefit_derivation_id', 'like', $request->benefit_derivation_id )->first();

        }

        return $this->respondWithToken( $this->token(), 'Successfully added', $benefitcode );
    }

    public function search( Request $request ) {
        $ndc = DB::table( 'BENEFIT_DERIVATION_NAMES' )
        ->select( 'BENEFIT_DERIVATION_ID', 'DESCRIPTION' )
        ->where( 'BENEFIT_DERIVATION_ID', 'like', '%' . strtoupper( $request->search ) . '%' )
        ->get();

        return $this->respondWithToken( $this->token(), '', $ndc );
    }

    public function getBLList( $ndcid ) {
        $ndclist = DB::table( 'BENEFIT_DERIVATION' )
        // ->select( 'NDC_EXCEPTION_LIST', 'EXCEPTION_NAME' )
        ->where( 'BENEFIT_DERIVATION_ID', $ndcid )
        // ->orWhere( 'EXCEPTION_NAME', 'like', '%' . strtoupper( $ndcid ) . '%' )
        ->get();

        return $this->respondWithToken( $this->token(), '', $ndclist );
    }

    public function getBLItemDetails( $ndcid, $ndcid2 ) {
        $ndclist = DB::table( 'BENEFIT_DERIVATION' )
        // ->select( 'NDC_EXCEPTION_LIST', 'EXCEPTION_NAME' )
        ->join( 'BENEFIT_DERIVATION_NAMES', 'BENEFIT_DERIVATION_NAMES.BENEFIT_DERIVATION_ID', '=', 'BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID' )
        ->where( 'BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID', $ndcid )
        ->where( 'BENEFIT_DERIVATION.BENEFIT_CODE', $ndcid2 )

        // ->orWhere( 'EXCEPTION_NAME', 'like', '%' . strtoupper( $ndcid ) . '%' )
        ->first();

        return $this->respondWithToken( $this->token(), '', $ndclist );

        return $this->respondWithToken( $this->token(), '', $ndc );

    }
}
