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

    public function submitPriorAuthorization(Request $request)
    {
        if($request->add_new)
        {
            $addPriorAuth = DB::table('PRIOR_AUTHORIZATIONS')
                            ->insert([
                                //Authorization Tab Starts
                                'customer_id' => strtoupper($request->customer_id),
                                'client_id' => $request->client_id,
                                'client_group_id' => $request->client_group_id,
                                'prior_auth_code_num' => $request->prior_auth_code_num,
                                'prior_auth_type' => $request->prior_auth_type,
                                'member_id' => $request->member_id,
                                'person_code' => $request->person_code,
                                'patient_pin_number' => $request->patient_pin_number,
                                'effective_date' => $request->effective_date,
                                'termination_date' =>  $request->termination_date,
                                'birth_date' => $request->birth_date,
                                'relationship' => $request->relationship,
                                'plan_id' => $request->plan_id,
                                'all_oth_error_cat_ovr' => $request->all_oth_error_cat_ovr,
                                'ndc' => $request->ndc,
                                'generic_product_id' => $request->generic_product_id,
                                'generic_indicator' => $request->generic_indicator,
                                //Authorization Tab Ends
                                //Pricing/ Misc tab start
                                'prescriber_id' => $request->prescriber_id,
                                'prescriber_status_override' => $request->prescriber_status_override,
                                'copay_sched_ovr_mail' => $request->copay_sched_ovr_mail,
                                'brand_copay_amt_mail' => $request->brand_copay_amt_mail,
                                'generic_copay_amt_mail' => $request->generic_copay_amt_mail,
                                'price_sched_ovr' => $request->price_sched_ovr,
                                'copay_sched_ovr' => $request->copay_sched_ovr,
                                'accum_bene_exclude_flag' => $request->accum_bene_exclude_flag,
                                'provider_type' => $request->provider_type,                              
                            ]);
            return $this->respondWithToken($this->token(),'', $addPriorAuth);
        }else{
            $addPriorAuth = DB::table('PRIOR_AUTHORIZATIONS')
                            ->where('customer_id', $request->customer_id)
                            ->where('client_id', $request->client_id)
                            ->where('client_group_id', $request->client_group_id)
                            ->update([
                                //Authorization Tab Starts
                                'prior_auth_code_num' => $request->prior_auth_code_num,
                                'prior_auth_type' => $request->prior_auth_type,
                                'member_id' => $request->member_id,
                                'person_code' => $request->person_code,
                                'patient_pin_number' => $request->patient_pin_number,
                                'effective_date' => $request->effective_date,
                                'termination_date' =>  $request->termination_date,
                                'birth_date' => $request->birth_date,
                                'relationship' => $request->relationship,
                                'plan_id' => $request->plan_id,
                                'all_oth_error_cat_ovr' => $request->all_oth_error_cat_ovr,
                                'ndc' => $request->ndc,
                                'generic_product_id' => $request->generic_product_id,
                                'generic_indicator' => $request->generic_indicator,
                                //Authorization Tab Ends
                                //Pricing/ Misc tab start
                                'prescriber_id' => $request->prescriber_id,
                                'prescriber_status_override' => $request->prescriber_status_override,
                                'copay_sched_ovr_mail' => $request->copay_sched_ovr_mail,
                                'brand_copay_amt_mail' => $request->brand_copay_amt_mail,
                                'generic_copay_amt_mail' => $request->generic_copay_amt_mail,
                                'price_sched_ovr' => $request->price_sched_ovr,
                                'copay_sched_ovr' => $request->copay_sched_ovr,
                                'accum_bene_exclude_flag' => $request->accum_bene_exclude_flag,
                                'provider_type' => $request->provider_type,  
                                 //Pricing/ Misc tab ends                            
                            ]);
            return $this->respondWithToken($this->token(),'', $addPriorAuth);
        }
    }
}
