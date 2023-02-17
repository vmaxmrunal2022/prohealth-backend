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
      ->where('CUSTOMER.customer_id', 'like', '%' . strtoupper($request->search) . '%')
      //   ->orWhere('PLAN_VALIDATION_LISTS.client_id', 'like', '%'. strtoupper($request->search) .'%')
      ->orWhere('CUSTOMER.customer_name', 'like', '%' . strtoupper($request->search) . '%')
      ->get();
    return $this->respondWithToken($this->token(), '', $planValidation);
  }

  public function getClientDetails(Request $request)
  {
    $clientList = DB::table('PLAN_VALIDATION_LISTS')
      ->where('customer_id', 'like', '%' . strtoupper($request->customer_id) . '%')
      //   ->orWhere('client_id', 'like', '%'. strtoupper($request->client_id))                      
      //   ->orWhere('client_group_id', 'like', '%'. strtoupper($request->client_group_id))                      
      ->get();

    return $this->respondWithToken($this->token(), '', $clientList);
  }

  public function addPlanValidation(Request $request)
  {

    $getEligibilityData = DB::table('plan_validation_lists')
        ->where('customer_id',strtoupper($request->customer_id))
        ->where('client_id',strtoupper($request->client_id))
        ->where('client_group_id', strtoupper($request->client_group_id))
        ->where('plan_id', $request->plan_id)
        ->first();


    if ($request->add_new==1) {

      if($getEligibilityData){

        return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getEligibilityData);


      }else{

        $planValidation = DB::table('plan_validation_lists')
        ->insert([
          'customer_id' => $request->customer_id,
          'client_id' => $request->client_id,
          'client_group_id' => $request->client_group_id,
          'plan_id' => $request->plan_id
        ]);
      return $this->respondWithToken($this->token(), 'Record Added Successfully !', $planValidation);

      }
     
    } else if($request->add_new==0){
      $planValidation = DB::table('plan_validation_lists')
        ->where('customer_id', $request->customer_id)
        ->where('client_id',$request->client_id)
        ->where('client_group_id',$request->client_group_id)
        ->update([
          // 'customer_id' => $request->customer_id,
          'plan_id' => $request->plan_id
        ]);
      return $this->respondWithToken($this->token(), 'Record Updated Successfully !', $planValidation);
    }
  }

  public function getPlanId(Request $request)
  {
    $plan_ids = DB::table('PLAN_TABLE')
      ->select('id')
      ->get();
    return $this->respondWithToken($this->token(), '', $plan_ids);
  }

}
