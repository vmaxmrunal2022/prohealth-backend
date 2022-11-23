<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MacListController extends Controller
{
    public function get(Request $request)
    {
        $macList = DB::table('MAC_LIST')
                   ->where('MAC_LIST', 'like', '%'.strtoupper($request->search).'%')
                   ->orWhere('MAC_DESC', 'like', '%'.strtoupper($request->search).'%')
                   ->get();

        return $this->respondWithToken($this->token(), '', $macList);
    }

    public function getMacList(Request $request)
    {
        $data = DB::table('MAC_LIST')
                ->join('MAC_TABLE', 'mac_list.mac_list','=','mac_table.mac_list')
                ->where('mac_table.mac_list',$request->search)
                ->get();
        return $this->respondWithToken($this->token(), '', $data);        
    }
}
