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
}
