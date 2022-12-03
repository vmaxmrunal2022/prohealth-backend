<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanValidationController extends Controller
{
    public function get(Request $request)
    {
        $planValidation = DB::table('PLAN_VALIDATION_LISTS') 
                          ->join('CUSTOMER', 'CUSTOMER.customer_id', '=', 'PLAN_VALIDATION_LISTS.customer_id')
                          ->where('CUSTOMER.customer_id', 'like', '%'. strtoupper($request->search) .'%')
                        //   ->orWhere('PLAN_VALIDATION_LISTS.client_id', 'like', '%'. strtoupper($request->search) .'%')
                          ->orWhere('CUSTOMER.customer_name', 'like', '%'. strtoupper($request->search) .'%')
                          ->get();
        return $this->respondWithToken($this->token(),'', $planValidation);
    }

    public function getClientDetails(Request $request)
    {
        $clientList = DB::table('PLAN_VALIDATION_LISTS')
                      ->where('customer_id', 'like', '%'. strtoupper($request->customer_id) .'%')
                    //   ->orWhere('client_id', 'like', '%'. strtoupper($request->client_id))                      
                    //   ->orWhere('client_group_id', 'like', '%'. strtoupper($request->client_group_id))                      
                      ->get();

        return $this->respondWithToken($this->token(), '', $clientList);
    }
}
