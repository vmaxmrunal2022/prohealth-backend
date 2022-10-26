<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function getClient(Request $request)
    {
        // $customerid = $request->customerid;
        // $customername = $request->customername;
        // $clientid = $request->clientid;
        // $clientname = $request->clientname;

        $search = $request->search;


        $clients = DB::table('client')
                        ->when($search, function($q) use ($search) {
                            $q->where('CUSTOMER_ID', $search);
                        })
                        ->when($search, function($q) use ($search) {
                            $q->where('CLIENT_ID', $search);
                        })
                        ->when($search, function($q) use ($search) {
                            $q->where('CLIENT_NAME', $search);
                        })
                        ->get();

        $this->respondWithToken($this->token() ?? '', '', $clients);
        
    }

    public function GetOneClient($clientid)
    {
        $client = DB::table('client')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where('CLIENT_ID', 'like', '%' . strtoupper($clientid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $client);
    }

    public function searchClient(Request $request)
    {

        $search = $request->search;
     
        $client = DB::table('client')
            ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
            ->select('CLIENT_ID','CLIENT_NAME', 'customer.CUSTOMER_NAME as customername', 'client.CUSTOMER_ID as customerid','client.EFFECTIVE_DATE as clienteffectivedate','client.TERMINATION_DATE as clientterminationdate')
            ->where('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('CLIENT_NAME', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('customer.CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('customer.CUSTOMER_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $client);
    }
}
