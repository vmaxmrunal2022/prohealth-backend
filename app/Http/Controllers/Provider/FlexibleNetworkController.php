<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class FlexibleNetworkController extends Controller
{
    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {



            $accum_benfit_stat_names = DB::table('RX_NETWORK_RULE_NAMES')->insert(
                [
                    'rx_network_rule_id' => strtoupper( $request->rx_network_rule_id ),
                    

                ]
            );


            $accum_benfit_stat = DB::table('RX_NETWORKS' )->insert(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'pharmacy_nabp'=>$request->pharmacy_nabp,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,

                ]
            );
            $benefitcode = DB::table('RX_NETWORK_NAMES')->get();


        } else {


            $benefitcode = DB::table('RX_NETWORK_RULE_NAMES' )
            ->where( 'rx_network_rule_id', $request->rx_network_rule_id )


            ->update(
                [
                    'rx_network_rule_id' =>  $request->rx_network_rule_id ,
                    'rx_network_rule_name'=>$request->rx_network_rule_name,

                ]
            );

            $accum_benfit_stat = DB::table( 'RX_NETWORK_RULES' )
            ->where('rx_network_rule_id', $request->rx_network_rule_id )
            ->update(
                [
                    'rx_network_rule_id' => $request->rx_network_rule_id,
                    'rx_network_rule_id_number'=>$request->rx_network_rule_id_number,
                    'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                   'exclude_rule'=>$request->exclude_rule,
                  

                ]
            );

            $benefitcode = DB::table('RX_NETWORK_RULES')->where('rx_network_rule_id', 'like', $request->rx_network_rule_id )->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }


    public function all(Request $request)

    {
           
             $ndc = DB::table('RX_NETWORK_RULE_NAMES')
        ->join('RX_NETWORK_RULES', 'RX_NETWORK_RULE_NAMES.RX_NETWORK_RULE_ID', '=', 'RX_NETWORK_RULE_NAMES.RX_NETWORK_RULE_ID')
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
      $ndc  = DB::select("SELECT * FROM RX_NETWORK_RULE_NAMES");

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
