<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanValidationController extends Controller
{

  use AuditTrait;

  public function get(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "search" => ['required']
    ]);
    if ($validator->fails()) {
      return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
    } else {
      $planValidation = DB::table('PLAN_VALIDATION_LISTS')
        ->select('PLAN_VALIDATION_LISTS.*','CUSTOMER.CUSTOMER_NAME','CLIENT.CLIENT_NAME','CLIENT_GROUP.GROUP_NAME')
        ->leftjoin('CUSTOMER', 'CUSTOMER.customer_id', '=', 'PLAN_VALIDATION_LISTS.customer_id')
        ->leftJoin('CLIENT', function ($join) {
          $join->on('CLIENT.CLIENT_ID','=','PLAN_VALIDATION_LISTS.CLIENT_ID')
              ->on('CLIENT.CUSTOMER_ID','=','PLAN_VALIDATION_LISTS.CUSTOMER_ID');
         })
         ->leftJoin('CLIENT_GROUP', function ($join) {
          $join->on('CLIENT_GROUP.CUSTOMER_ID','=','PLAN_VALIDATION_LISTS.CUSTOMER_ID')
              ->on('CLIENT_GROUP.CLIENT_ID','=','PLAN_VALIDATION_LISTS.CLIENT_ID')
              ->on('CLIENT_GROUP.CLIENT_GROUP_ID','=','PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID');
         })
        // ->where('CUSTOMER.customer_id', 'like', '%' . $request->search. '%')
        ->whereRaw('LOWER(PLAN_VALIDATION_LISTS.CUSTOMER_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
          ->orWhere('PLAN_VALIDATION_LISTS.client_id', 'like', '%'. strtoupper($request->search) .'%')
        // ->orWhere('CUSTOMER.customer_name', 'like', '%' . $request->search . '%')
        ->get();
      return $this->respondWithToken($this->token(), '', $planValidation);
    }
  }

  public function getplanIds(Request $request)
  {

    $clientList = DB::table('PLAN_VALIDATION_LISTS')
      ->select('PLAN_VALIDATION_LISTS.*','CUSTOMER.CUSTOMER_NAME','CLIENT.CLIENT_NAME','CLIENT_GROUP.GROUP_NAME')
      ->leftjoin('CUSTOMER', 'CUSTOMER.customer_id', '=', 'PLAN_VALIDATION_LISTS.customer_id')
      ->leftJoin('CLIENT', function ($join) {
        $join->on('CLIENT.CLIENT_ID','=','PLAN_VALIDATION_LISTS.CLIENT_ID')
            ->on('CLIENT.CUSTOMER_ID','=','PLAN_VALIDATION_LISTS.CUSTOMER_ID');
      })
      ->leftJoin('CLIENT_GROUP', function ($join) {
        $join->on('CLIENT_GROUP.CUSTOMER_ID','=','PLAN_VALIDATION_LISTS.CUSTOMER_ID')
            ->on('CLIENT_GROUP.CLIENT_ID','=','PLAN_VALIDATION_LISTS.CLIENT_ID')
            ->on('CLIENT_GROUP.CLIENT_GROUP_ID','=','PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID');
      })
      ->where('PLAN_VALIDATION_LISTS.customer_id', $request->customer_id)->get();

    return $this->respondWithToken($this->token(), '', $clientList);







  }

  public function getDetails(Request $request)
  {

    // dd($request->client_id);
    $clientList = DB::table('PLAN_VALIDATION_LISTS')
      // ->join('CLIENT_GROUP','CLIENT_GROUP.CLIENT_GROUP_ID','=','PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID')

      //   ->join("CLIENT_GROUP",function($join){
      //     $join->on("CLIENT_GROUP.CLIENT_GROUP_ID","=","PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID")
      //         ->on("CLIENT_GROUP.CLIENT_GROUP_ID","=","PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID");
      // })

      // ->join('CLIENT_GROUP','CLIENT_GROUP.CLIENT_GROUP_ID','=','PLAN_VALIDATION_LISTS.CLIENT_GROUP_ID')

      // ->leftjoin('CUSTOMER','CUSTOMER.CUSTOMER_ID','=','PLAN_VALIDATION_LISTS.CUSTOMER_ID')
      // ->leftjoin('CLIENT','CLIENT.CLIENT_ID','=','PLAN_VALIDATION_LISTS.CLIENT_ID')
      ->where('PLAN_VALIDATION_LISTS.customer_id', $request->customer_id)
      ->where('PLAN_VALIDATION_LISTS.CLIENT_ID', $request->client_id)
      // ->where('PLAN_VALIDATION_LISTS.client_group_id', $request->client_group_id)  
      // ->where('PLAN_VALIDATION_LISTS.plan_id',$request->plan_id)                 
      ->get();

    return $this->respondWithToken($this->token(), '', $clientList);
  }

  public function addPlanValidation(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'customer_id' => ['required', 'max:10'],
      'client_id' => ['required', 'max:15'],
      'client_group_id' => ['required', 'max:15'],
      'plan_id' => ['required', 'max:15'],

    ]);
    if ($validator->fails()) {
      return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
    }

    $getEligibilityData = DB::table('plan_validation_lists')
      ->where('customer_id', $request->customer_id)
      ->where('client_id', $request->client_id)
      ->where('client_group_id', $request->client_group_id)
      ->where('plan_id', $request->plan_id)
      ->first();


    if ($request->add_new == 1) {

      if ($getEligibilityData) {

        return $this->respondWithToken($this->token(), [['Plan Validation ID Already Exists ']], $getEligibilityData, 'false');
      } else {

        $planValidation = DB::table('plan_validation_lists')
          ->insert([
            'customer_id' => $request->customer_id,
            'client_id' => $request->client_id,
            'client_group_id' => $request->client_group_id,
            'plan_id' => $request->plan_id
          ]);
        $getEligibilityData = DB::table('plan_validation_lists')
          ->where('customer_id', $request->customer_id)
          ->where('client_id', $request->client_id)
          ->where('client_group_id', $request->client_group_id)
          ->where('plan_id', $request->plan_id)
          ->first();
        if($getEligibilityData){
          $record_snap = json_encode($getEligibilityData);
          $save_audit = $this->auditMethod('IN', $record_snap, 'plan_validation_lists');
        }  
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

      $getEligibilityData = DB::table('plan_validation_lists')
        ->where('customer_id', $request->customer_id)
        ->where('client_id', $request->client_id)
        ->where('client_group_id', $request->client_group_id)
        ->where('plan_id', $request->plan_id)
        ->first();
      if($getEligibilityData){
        $record_snap = json_encode($getEligibilityData);
        $save_audit = $this->auditMethod('UP', $record_snap, 'plan_validation_lists');
      }    
     
      return $this->respondWithToken($this->token(), 'Record Updated Successfully', $planValidation);
    }
  }

  public function getPlanId(Request $request)
  {
    $searchQuery = $request->search;
    $plan_ids = DB::table('PLAN_TABLE_EXTENSIONS')
      ->join('PLAN_BENEFIT_TABLE', 'PLAN_TABLE_EXTENSIONS.PLAN_ID', '=', 'PLAN_BENEFIT_TABLE.PLAN_ID')
      // ->select('id')
      ->paginate(100);
    return $this->respondWithToken($this->token(), '', $plan_ids);
  }
  public function planValidationdelete(Request $request)
  {

    if (isset($request->customer_id) && isset($request->client_id) && isset($request->client_group_id) && isset($request->plan_id)) {
      $getEligibilityData = DB::table('plan_validation_lists')
        ->where('customer_id', $request->customer_id)
        ->where('client_id', $request->client_id)
        ->where('client_group_id', $request->client_group_id)
        ->where('plan_id', $request->plan_id)
        ->first();
      if($getEligibilityData){
        $record_snap = json_encode($getEligibilityData);
        $save_audit = $this->auditMethod('DE', $record_snap, 'plan_validation_lists');
      }  
      $planValidation = DB::table('plan_validation_lists')
        ->where('customer_id', $request->customer_id)
        ->where('client_id', $request->client_id)
        ->where('client_group_id', $request->client_group_id)
        ->where('plan_id', $request->plan_id)
        ->delete();

      if ($planValidation) {

        return $this->respondWithToken($this->token(), 'Record Deleted Successfully');

      } else {

        return $this->respondWithToken($this->token(), 'Record Not Found');

      }

    }

  }
}