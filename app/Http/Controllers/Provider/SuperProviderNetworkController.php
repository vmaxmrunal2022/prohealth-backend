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
                ->select('SUPER_RX_NETWORK_ID', 'SUPER_RX_NETWORK_ID_NAME')
                ->where('SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('SUPER_RX_NETWORK_ID_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    } 

    public function networkList($ndcid)
    {
        $ndclist = DB::table('SUPER_RX_NETWORKS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function networkDetails($ndcid)
    {
        // $ndc = DB::table('SUPER_RX_NETWORK_NAMES')
        // ->select('SUPER_RX_NETWORK_ID', 'SUPER_RX_NETWORK_ID_NAME')
        // ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
        //             ->where('NDC_EXCEPTION_LISTS.NDC', 'like', '%' . strtoupper($ndcid) . '%')  
        //             ->first();


                  $ndc=  DB::table('SUPER_RX_NETWORK_NAMES')
                    ->select('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID','SUPER_RX_NETWORKS.RX_NETWORK_ID','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME','SUPER_RX_NETWORKS.MIN_RX_QTY','SUPER_RX_NETWORKS.MAX_RX_QTY','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_PRIORITY','SUPER_RX_NETWORKS.DAYS_SUPPLY_OPT','SUPER_RX_NETWORKS.EFFECTIVE_DATE','SUPER_RX_NETWORKS.TERMINATION_DATE','SUPER_RX_NETWORKS.PRICING_OVRD_LIST_ID','SUPER_RX_NETWORKS.STARTER_DOSE_DAYS','SUPER_RX_NETWORKS.STARTER_DOSE_BYPASS_DAYS','SUPER_RX_NETWORKS.STARTER_DOSE_MAINT_BYPASS_DAYS','SUPER_RX_NETWORKS.COMM_CHARGE_PAID','SUPER_RX_NETWORKS.COMM_CHARGE_REJECT')
                    ->join('SUPER_RX_NETWORKS','SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID','=','SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
                 ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')  
                 ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


}
