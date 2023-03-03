<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class FlexibleNetworkController extends Controller
{
    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        $recordCheck=DB::table('RX_NETWORK_RULES')->where('rx_network_rule_id',$request->rx_network_rule_id)->first();
        

        if ( $request->has( 'new' ) ) {





            $accum_benfit_stat_names = DB::table('RX_NETWORK_RULE_NAMES')->insert(
                [
                    'rx_network_rule_id' => strtoupper( $request->rx_network_rule_id ),
                    'rx_network_rule_name'=>strtoupper( $request->rx_network_rule_name ),
                    

                ]
            );


            $accum_benfit_stat = DB::table('RX_NETWORK_RULES' )->insert(
                [
                    'rx_network_rule_id' => strtoupper( $request->rx_network_rule_id ),
                    'rx_network_rule_id_number'=>$request->rx_network_rule_id_number,
                    'pharmacy_chain'=>$request->pharmacy_chain,
                    'state'=>$request->state,
                    'county'=>$request->county,
                    'zip_code'=>$request->zip_code,
                    'area_code'=>$request->area_code,
                    'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                     'exclude_rule'=>$request->exclude_rule,
                  
                   

                ]
            );

            $benefitcode = DB::table( 'RX_NETWORK_RULE_NAMES' )->where( 'rx_network_rule_id', 'like', $request->rx_network_rule_id )->first();



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
                    'rx_network_rule_id' => strtoupper( $request->rx_network_rule_id ),
                    'rx_network_rule_id_number'=>$request->rx_network_rule_id_number,
                    'pharmacy_chain'=>$request->pharmacy_chain,
                    'state'=>$request->state,
                    'county'=>$request->county,
                    'zip_code'=>$request->zip_code,
                    'area_code'=>$request->area_code,
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
        ->where('RX_NETWORK_RULES.PHARMACY_CHAIN',$request->pharmacy_chain)->get();



    return $this->respondWithToken($this->token(), '', $ndc);
    }


   

    public function getDetails( $ndcid ) {
        $ndc = DB::table('RX_NETWORK_RULES' )
        ->join('RX_NETWORK_RULE_NAMES', 'RX_NETWORK_RULE_NAMES.RX_NETWORK_RULE_ID', '=', 'RX_NETWORK_RULES.RX_NETWORK_RULE_ID')
        // ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'RX_NETWORKS.PHARMACY_NABP')


        ->where('RX_NETWORK_RULES.RX_NETWORK_RULE_ID', $ndcid)
        ->first();

        return $this->respondWithToken( $this->token(), '', $ndc );

    }



    public function search(Request $request)

    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES')
                ->where('RX_NETWORK_RULE_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('RX_NETWORK_RULE_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function RuleIdsearch(Request $request)

    {
        $ndc = DB::table('RX_NETWORK_RULES')
                ->where('RX_NETWORK_RULE_ID_NUMBER', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getList ($ndcid)
    {
        $ndc =DB::table('RX_NETWORK_RULES')
        ->Where('RX_NETWORK_RULE_ID', 'like', '%' .$ndcid. '%')
        ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
