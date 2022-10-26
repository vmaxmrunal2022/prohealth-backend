<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientGroupController extends Controller
{
    public function getClientGroup(Request $request)
    {
        $customerid = $request->customerid;
        $clientid = $request->clientid;
        $groupid = $request->groupid;

        $clientgroup = DB::table('client_group')
                        ->when($customerid, function($q) use ($customerid) {
                            $q->where('CUSTOMER_ID', $customerid);
                        })
                        ->when($clientid, function($q) use ($clientid) {
                            $q->where('CLIENT_ID', $clientid);
                        })
                        ->when($groupid, function($q) use ($groupid) {
                            $q->where('CLIENT_GROUP_ID', $groupid);
                        })
                        ->get();

        $this->respondWithToken($this->token() ?? '', '', $clientgroup);
    }

    
    public function GetOneClientGroup($clientgrpid)
    {
        $client = DB::table('CLIENT_GROUP')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where('CLIENT_GROUP_ID', 'like', '%' . strtoupper($clientgrpid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $client);
    }

    public function searchClientgroup(Request $request)
    {

        $search = $request->search;
     
        $client = DB::table('CLIENT_GROUP')
            // ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
            ->select('CLIENT_ID','GROUP_NAME', 'CUSTOMER_ID', 'CLIENT_GROUP_ID')
            ->where('CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('CLIENT_GROUP_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GROUP_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $client);
    }
}
