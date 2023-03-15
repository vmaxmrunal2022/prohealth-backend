<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BenefitListController extends Controller
{
   
    public function index(Request $request)
    {
        $ndc = DB::table('BENEFIT_CODES')->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function BenefitLists(Request $request){

        $ndc = DB::table('BENEFIT_LIST_NAMES')->get();

    return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );


        $recordcheck=DB::table('BENEFIT_LIST')
        ->where('benefit_list_id', strtoupper($request->benefit_list_id))
        ->first();



        if ( $request->has( 'new' ) ) {

            if($recordcheck){

                return $this->respondWithToken( $this->token(), 'Benefit List Id Already Exists',$recordcheck);


            }else{

                $accum_benfit_stat_names = DB::table('BENEFIT_LIST_NAMES')->insert(
                    [
                        'benefit_list_id' => strtoupper( $request->benefit_list_id ),
                        'description'=>$request->description
                        
    
                    ]
                );
    
    
                $accum_benfit_stat = DB::table('BENEFIT_LIST')->insert(
                    [
                        'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                        'BENEFIT_CODE'=>$request->benefit_code,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'USER_ID'=>'',
                        'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                        'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                        'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>'',
                        'MIN_AGE'=>$request->min_age,
                        'MAX_AGE'=>$request->max_age,
                        'MIN_PRICE'=>$request->min_price,
                        'MAX_PRICE'=>$request->max_price,
                        'MIN_PRICE_OPT'=>$request->max_price_opt,
                        'MAX_PRICE_OPT'=>$request->max_price_opt,
                        // 'VALID_RELATION_CODE'=>$request
    
                    ]
                );

            }



            return $this->respondWithToken( $this->token(), 'Record Added Succefully',$accum_benfit_stat);



        } else {


            $benefitcode = DB::table('BENEFIT_LIST_NAMES' )
            ->where('benefit_list_id', $request->benefit_list_id )


            ->update(
                [
                    'description'=>$request->description,

                ]
            );

            $accum_benfit_stat = DB::table( 'BENEFIT_LIST' )
            ->where('benefit_list_id', $request->benefit_list_id )
            ->update(
                [
                    'benefit_code'=>$request->benefit_code,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,
                  
                  

                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Updated Succefully',$accum_benfit_stat);

        }


    }



    public function search(Request $request)
    {
        $ndc = DB::table('BENEFIT_LIST_NAMES')
                ->select('BENEFIT_LIST_ID', 'DESCRIPTION')
                ->where('BENEFIT_LIST_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getBLList($ndcid)
    {
        $ndclist = DB::table('BENEFIT_LIST')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('BENEFIT_LIST_ID', $ndcid)
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getBLItemDetails($ndcid)
    {
        $ndc = DB::table('BENEFIT_LIST')
        // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
        ->join('BENEFIT_LIST_NAMES', 'BENEFIT_LIST_NAMES.BENEFIT_LIST_ID', '=', 'BENEFIT_LIST.BENEFIT_LIST_ID')
        ->join('BENEFIT_CODES','BENEFIT_CODES.BENEFIT_CODE','=','BENEFIT_LIST.BENEFIT_CODE')
        ->where('BENEFIT_LIST.BENEFIT_LIST_ID',$ndcid)

        // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
