<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TraditionalNetworkController extends Controller
{



    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {

            $accum_benfit_stat_names = DB::table( 'accum_bene_strategy_names' )->insert(
                [
                    'accum_bene_strategy_id' => strtoupper( $request->accum_bene_strategy_id ),
                    'accum_bene_strategy_name' => $request->accum_bene_strategy_name,

                ]
            );

            $accum_benfit_stat = DB::table( 'ACCUM_BENEFIT_STRATEGY' )->insert(
                [
                    'accum_bene_strategy_id' => strtoupper( $request->accum_bene_strategy_id ),
                    'pharm_type_variation_ind'=>$request->pharm_type_variation_ind,
                    'formulary_variation_ind'=>$request->formulary_variation_ind,
                    'network_part_variation_ind'=>$request->network_part_variation_ind,
                    'claim_type_variation_ind'=>$request->claim_type_variation_ind,
                    'date_time_created'=>$createddate,
                    'user_id'=>'',
                    'date_time_modified'=>'',
                    'form_id'=>'',
                    'user_id_created'=>'',
                    'accum_exclusion_flag'=>$request->accum_exclusion_flag,
                    'effective_date'=>$request->effective_date,
                    'module_exit'=>'',
                    'plan_accum_deduct_id'=>$request->plan_accum_deduct_id,

                ]
            );

        } else {

            $benefitcode = DB::table('RX_NETWORK_NAMES' )
            ->where( 'network_id', $request->network_id )
            ->update(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'network_name' => $request->network_name,

                ]
            );

            $accum_benfit_stat = DB::table( 'RX_NETWORKS' )
            ->where('pharmacy_nabp', $request->pharmacy_nabp )
            ->update(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'pharmacy_nabp'=>$request->pharmacy_nabp,
                    'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,
                  

                ]
            );

            $benefitcode = DB::table( 'RX_NETWORKS' )->where( 'pharmacy_nabp', 'like', $request->pharmacy_nabp )->first();

        }

        // $benefitcode = DB::table( 'benefit_codes' )->where( 'benefit_code', 'like', $request->benefit_code )->first();

        return $this->respondWithToken( $this->token(), 'Successfully added', $benefitcode );
    }


    public function all(Request $request)

    {
           
             $ndc = DB::table('RX_NETWORK_NAMES')
        ->join('RX_NETWORKS', 'RX_NETWORK_NAMES.NETWORK_ID', '=', 'RX_NETWORK_NAMES.NETWORK_ID')
                ->get();



    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails( $ndcid ) {
        $ndc = DB::table('RX_NETWORKS' )
        ->where( 'PHARMACY_NABP', 'like', '%' .$ndcid. '%' )
        ->first();

        return $this->respondWithToken( $this->token(), '', $ndc );

    }



    public function search(Request $request)

    {
             $ndc  = DB::select("SELECT * FROM RX_NETWORK_NAMES");

    return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getList ($ndcid)
    {
        $ndc =DB::table('RX_NETWORK_NAMES')
        ->join('RX_NETWORKS', 'RX_NETWORKS.NETWORK_ID', '=', 'RX_NETWORK_NAMES.NETWORK_ID')
        ->where('RX_NETWORK_NAMES.NETWORK_NAME', 'like', '%' .$ndcid. '%')
        ->orWhere('RX_NETWORKS.NETWORK_ID', 'like', '%' .$ndcid. '%')
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }

}
