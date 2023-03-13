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


        $recordcheck = DB::table('PROVIDER_TYPE_VALIDATIONS')
        ->where('prov_type_list_id', strtoupper($request->prov_type_list_id))
        ->first();
        

        if ( $request->has( 'new' ) ) {

            if($recordcheck){

                return $this->respondWithToken($this->token(), 'Benefit Derivation ID Already Exists', $recordcheck);

            }

            $accum_benfit_stat_names = DB::table( 'BENEFIT_DERIVATION_NAMES' )->insert(
                [
                    'BENEFIT_DERIVATION_ID' => strtoupper( $request->benefit_derivation_id ),
                    'DESCRIPTION'=>$request->description,
                    'DATE_TIME_CREATED'=>$createddate

                ]
            );

            $accum_benfit_stat = DB::table( 'BENEFIT_DERIVATION' )->insert(
                [
                    'BENEFIT_DERIVATION_ID' => strtoupper( $request->benefit_derivation_id ),
                    'SERVICE_TYPE'=>$request->service_type,
                    'SERVICE_MODIFIER'=>$request->service_modifier,
                    'BENEFIT_CODE'=>$request->benefit_code,
                    'EFFECTIVE_DATE'=>$request->effective_date,
                    'TERMINATION_DATE'=>$request->termination_date,
                    'DATE_TIME_CREATED'=>$createddate,

                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Added Successfully', $accum_benfit_stat );



        } else {

            $benefitcode = DB::table( 'BENEFIT_DERIVATION_NAMES' )
            ->where( 'benefit_derivation_id', $request->benefit_derivation_id )

            ->update(
                [
                    'DESCRIPTION'=>$request->description,
                    'DATE_TIME_CREATED'=>$createddate
                ]
            );

            $accum_benfit_stat = DB::table( 'BENEFIT_DERIVATION' )
            ->where( 'benefit_derivation_id', $request->benefit_derivation_id )
            ->where( 'benefit_code', $request->benefit_code )
            ->where( 'service_type', $request->service_type )


            ->update(
                [
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,

                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Updated Successfully', $accum_benfit_stat );

        }

    }


    public function getAll(Request $request){

    
        $data = DB::table('BENEFIT_DERIVATION_NAMES')
        ->where('BENEFIT_DERIVATION_ID','LIKE','%'.strtoupper($request->benefit_derivation_id).'%')
        ->get();

        return $this->respondWithToken( $this->token(), 'data fetched Successfully', $data );

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


    }
}
