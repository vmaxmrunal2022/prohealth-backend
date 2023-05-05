<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanValidationController extends Controller
{
  public function get(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "search" => ['required']
    ]);
    if ($validator->fails()) {
      return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
    } else {
      $planValidation = DB::table('PLAN_VALIDATION_LISTS')
        ->join('CUSTOMER', 'CUSTOMER.customer_id', '=', 'PLAN_VALIDATION_LISTS.customer_id')
        ->where('CUSTOMER.customer_id', 'like', '%' . strtoupper($request->search) . '%')
        //   ->orWhere('PLAN_VALIDATION_LISTS.client_id', 'like', '%'. strtoupper($request->search) .'%')
        ->orWhere('CUSTOMER.customer_name', 'like', '%' . strtoupper($request->search) . '%')
        ->get();
      return $this->respondWithToken($this->token(), '', $planValidation);
    }
  }

  public function getClientDetails(Request $request)
  {
    $clientList = DB::table('PLAN_VALIDATION_LISTS')
    // ->join('CLIENT_GROUP','CLIENT_GROUP.CLIENT_GROUP_ID','=','PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID')
    
  //   ->join("CLIENT_GROUP",function($join){
  //     $join->on("CLIENT_GROUP.CLIENT_GROUP_ID","=","PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID")
  //         ->on("CLIENT_GROUP.CLIENT_GROUP_ID","=","PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID");
  // })
    
    // ->join('CLIENT_GROUP','CLIENT_GROUP.CLIENT_GROUP_ID','=','PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID')

    // ->leftjoin('CUSTOMER','CUSTOMER.CUSTOMER_ID','=','PLAN_VALIDATION_LISTS.CUSTOMER_ID')
    // ->leftjoin('CLIENT','CLIENT.CLIENT_ID','=','PLAN_VALIDATION_LISTS.CLIENT_ID')
    ->where('PLAN_VALIDATION_LISTS.plan_id',$request->customer_id)

        // ->orWhere('PLAN_VALIDATION_LISTS.client_id', $request->client_id)              
        // ->orWhere('PLAN_VALIDATION_LISTS.client_group_id', $request->client_group_id)  
        // ->orwhere('PLAN_VALIDATION_LISTS.plan_id',$request->plan_id)                 
      ->get();

    return $this->respondWithToken($this->token(), '', $clientList);
  }

  public function addPlanValidation(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "customer_id" => ['required'],
      "client_id"=>['required'],
      "client_group_id" => ['required'],
      "plan_id" => ['required'],
      
    ]);
    if ($validator->fails()) {
        return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
    }

    $getEligibilityData = DB::table('plan_validation_lists')
      // ->where('customer_id', strtoupper($request->customer_id))
      ->where('client_id', strtoupper($request->client_id))
      ->where('client_group_id', strtoupper($request->client_group_id))
      ->where('plan_id', $request->plan_id)
      ->first();


    if ($request->add_new == 1) {

      if ($getEligibilityData) {

        return $this->respondWithToken($this->token(), 'Plan Validation ID Already Exists ', $getEligibilityData);
      } else {

        $planValidation = DB::table('plan_validation_lists')
          ->insert([
            'customer_id' => $request->customer_id,
            'client_id' => $request->client_id,
            'client_group_id' => $request->client_group_id,
            'plan_id' => $request->plan_id
          ]);
        return $this->respondWithToken($this->token(), 'Record Added Successfully', $planValidation);
      }
    } else if ($request->add_new == 0) {
      $planValidation = DB::table('plan_validation_lists')
        ->where('customer_id', $request->customer_id)
        ->where('client_id', $request->client_id)
        ->where('client_group_id', $request->client_group_id)
        ->where('plan_id', $request->plan_id)
        ->update([
          // 'customer_id' => $request->customer_id,
          'plan_id' => $request->plan_id,
          'date_time_modified' => date('Y-m-d H:i:s'),
        ]);
      // ->get();
      // dd($planValidation);
      return $this->respondWithToken($this->token(), 'Record Updated Successfully', $planValidation);
    }
  }

  public function getPlanId(Request $request)
  {
    $plan_ids = DB::table('PLAN_TABLE_EXTENSIONS')
    ->join('PLAN_BENEFIT_TABLE','PLAN_TABLE_EXTENSIONS.PLAN_ID','=','PLAN_BENEFIT_TABLE.PLAN_ID')
      // ->select('id')
      ->get();
    return $this->respondWithToken($this->token(), '', $plan_ids);
  }
}
