<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PrioritiseNetworkController extends Controller
{
use AuditTrait;

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
                    'super_rx_network_priority'=>$request->super_rx_network_priority,                    
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
                    'super_rx_network_priority'=>$request->super_rx_network_priority,

                   
                  

                ]
            );


            $benefitcode = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%'.$request->super_rx_network_id .'%')->first();


        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }
    
    public function search(Request $request)

    {

        if($request->search == 'undefined'){
            $ndc = DB::table('SUPER_RX_NETWORK_NAMES')->get();
        }
        else{


            $ndc = DB::table('SUPER_RX_NETWORK_NAMES')
            ->whereRaw('LOWER(SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orwhereRaw('LOWER(SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME) LIKE ?', ['%' . strtolower($request->search) . '%'])
                    // ->where('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->search) . '%')
                    // ->orWhere('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME', 'like', '%' . strtoupper($request->search) . '%')
             ->get();
        }
       



    return $this->respondWithToken($this->token(), '', $ndc);
    } 

    public function networkList($ndcid)
    {


         $data=  DB::table('SUPER_RX_NETWORK_NAMES')
        ->join('SUPER_RX_NETWORKS','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID','=','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
        ->whereRaw('LOWER(SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID) LIKE ?', ['%' . strtolower($ndcid) . '%'])

        // ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')  
        ->get();
       
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getDetails($id1,$ndcid,$ncdid2,$id3){

        $data=  DB::table('SUPER_RX_NETWORK_NAMES')
        ->join('SUPER_RX_NETWORKS','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID','=','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
        ->where('SUPER_RX_NETWORKS.super_rx_network_id', $id1)  
        ->where('SUPER_RX_NETWORKS.rx_network_id', $ndcid)  
        ->where('SUPER_RX_NETWORKS.EFFECTIVE_DATE', $ncdid2)  
        ->where('SUPER_RX_NETWORKS.super_rx_network_priority',$id3)
        ->first();
       
        return $this->respondWithToken($this->token(), '', $data);
    }

}
