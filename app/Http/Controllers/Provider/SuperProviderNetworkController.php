<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class SuperProviderNetworkController extends Controller
{



     public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {



            $accum_benfit_stat_names = DB::table('SUPER_RX_NETWORK_NAMES')->insert(
                [
                    'super_rx_network_id' => strtoupper( $request->super_rx_network_id ),
                    'super_rx_network_id_name'=>strtoupper( $request->super_rx_network_id_name ),

                ]
            );


            $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS' )->insert(
                [
                    'super_rx_network_id' => strtoupper( $request->super_rx_network_id),
                    'rx_network_id'=>$request->rx_network_id,
                    'effective_date'=>$request->effective_date,                    
                ]
            );

            $benefitcode = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%'.$request->super_rx_network_id .'%')->first();

        } else {


            $benefitcode = DB::table('SUPER_RX_NETWORK_NAMES' )
            ->where( 'super_rx_network_id', $request->super_rx_network_id )


            ->update(
                [
                    'super_rx_network_id' =>  strtoupper($request->super_rx_network_id ),
                    'super_rx_network_id_name'=>strtoupper($request->super_rx_network_id_name),

                ]
            );

            $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS')
            ->where('super_rx_network_id', $request->super_rx_network_id )
            ->update(
                [
                    'super_rx_network_id' => strtoupper($request->super_rx_network_id),
                    'rx_network_id'=>$request->rx_network_id,
                    'effective_date'=>$request->effective_date,
                   
                  

                ]
            );


            $benefitcode = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%'.$request->super_rx_network_id .'%')->first();


        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }
    public function search(Request $request)
    {
        $ndc = DB::table('SUPER_RX_NETWORK_NAMES')
        ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID')

                ->select('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_PRIORITY','SUPER_RX_NETWORKS.EFFECTIVE_DATE','SUPER_RX_NETWORKS.RX_NETWORK_TYPE','SUPER_RX_NETWORKS.PRICE_SCHEDULE_OVRD')
                ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();



    return $this->respondWithToken($this->token(), '', $ndc);
    } 

    public function networkList($ndcid)
    {
      
                $ndclist=  DB::table('SUPER_RX_NETWORK_NAMES')
                ->join('SUPER_RX_NETWORKS','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID','=','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
             ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')  
             ->first();


        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function dropDown(Request $request)
    {
      
       $ndclist=  DB::table('SUPER_RX_NETWORK_NAMES')
        ->join('SUPER_RX_NETWORKS','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID','=','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
        ->get();
        return $this->respondWithToken($this->token(), 'Data Fetched Succssfully', $ndclist);
    }


    public function getDetails( $ndcid ) {
       
        $ndc=  DB::table('SUPER_RX_NETWORK_NAMES')
        ->join('SUPER_RX_NETWORKS','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID','=','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
     ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')  
     ->first();

        return $this->respondWithToken( $this->token(), '', $ndc );

    }

   

}