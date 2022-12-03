<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class PrioritiseNetworkController extends Controller
{
    
    public function search(Request $request)
    {
        $ndc = DB::table('SUPER_RX_NETWORK_NAMES')

                ->where('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME', 'like', '%' . strtoupper($request->search) . '%')

                ->get();



    return $this->respondWithToken($this->token(), '', $ndc);
    } 

    public function networkList($ndcid)
    {


                $data=  DB::table('SUPER_RX_NETWORK_NAMES')
                ->join('SUPER_RX_NETWORKS','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID','=','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
             ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')  
             ->first();
        $id="MASTER";
             $formdata=DB::table('SUPER_RX_NETWORKS')
          ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' .strtoupper($id). '%')  
          ->get();
          $data->list =  $formdata;



        return $this->respondWithToken($this->token(), '', $data);
    }

}
