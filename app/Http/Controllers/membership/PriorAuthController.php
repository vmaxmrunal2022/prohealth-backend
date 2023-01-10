<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriorAuthController extends Controller
{
    public function get(Request $request)
    {
        $priorAuthList = DB::table('PRIOR_AUTHORIZATIONS')
                         ->where('member_id', 'like', '%'. strtoupper($request->search) .'%')
                         ->orWhere('person_code', 'like', '%'. strtoupper($request->search) .'%')
                         ->orWhere('customer_id', 'like', '%'. strtoupper($request->search) .'%')
                         ->orWhere('client_id', 'like', '%'. strtoupper($request->search) .'%')
                         ->orWhere('client_group_id', 'like', '%'. strtoupper($request->search) .'%')
                         ->get();

        return $this->respondWithToken($this->token(), '', $priorAuthList);
    }


}
