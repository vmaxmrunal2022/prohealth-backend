<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TraditionalNetworkController extends Controller
{


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
