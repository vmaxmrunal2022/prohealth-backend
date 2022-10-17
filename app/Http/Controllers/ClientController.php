<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function getClient(Request $request)
    {
        $customerid = $request->customerid;
        $customername = $request->customername;
        $clientid = $request->clientid;
        $clientname = $request->clientname;


        $clients = DB::table('client')
                        ->when($customerid, function($q) use ($customerid) {
                            $q->where('CUSTOMER_ID', $customerid);
                        })
                        // ->when($customername, function($q) use ($customername) {
                        //     $q->where('customer_id', $customername);
                        // })
                        ->when($clientid, function($q) use ($clientid) {
                            $q->where('CLIENT_ID', $clientid);
                        })
                        ->when($clientname, function($q) use ($clientname) {
                            $q->where('CLIENT_NAME', $clientname);
                        })
                        ->get();

        $this->respondWithToken($this->token() ?? '', '', $clients);
        
    }
}
