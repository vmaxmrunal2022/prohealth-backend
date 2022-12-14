<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class SuperProviderNetworkController extends Controller
{
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

   

}