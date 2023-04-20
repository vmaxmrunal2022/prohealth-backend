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
                         ->orWhere('prior_auth_code_num', 'like', '%'. strtoupper($request->search) .'%')

                         ->get();


        return $this->respondWithToken($this->token(), '', $priorAuthList);
    }

    public function submitPriorAuthorization(Request $request)
    {   


        $getEligibilityData = DB::table('PRIOR_AUTHORIZATIONS')
        ->where('customer_id',strtoupper($request->customer_id))
        ->where('client_id',strtoupper($request->client_id))
        ->where('client_group_id', strtoupper($request->client_group_id))
        ->where('plan_id', $request->plan_id)
        ->first();
        
        
        if($request->add_new==1)
        {  

            if($getEligibilityData){

                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getEligibilityData);


            }

            else{
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
                                
                                'accum_bene_error_cat_ovr' => $request->accum_bene_error_cat_ovr,
                                'benefit_code' => $request->benefit_code,
                                'brand_copay_amt' => $request->brand_copay_amt,
                                'days_supply_error_cat_ovr' => $request->days_supply_error_cat_ovr,
                                'diagnosis_id' => $request->diagnosis_id,
                                'drug_error_cat_ovr' => $request->drug_error_cat_ovr,
                                'elig_error_cat_ovr' => $request->elig_error_cat_ovr,
                                'generic_copay_amt' => $request->generic_copay_amt,
                                'max_daily_dose' => $request->max_daily_dose,
                                'max_days' => $request->max_days,
                                'max_dollar_amt' => $request->max_dollar_amt,
                                'max_num_fills' => $request->max_num_fills,
                                'max_quantity' => $request->max_quantity,
                                'num_fills_used' => $request->num_fills_used,
                                'oltp_date_used' => $request->oltp_date_used,
                                'patient_paid_diff_flag'=> $request->patient_paid_diff_flag,
                                'pharm_error_cat_ovr' => $request->pharm_error_cat_ovr,
                                'pharmacy_nabp' => $request->pharmacy_nabp,
                                'pharmacy_status_override' => $request->pharmacy_status_override,
                                'phy_error_cat_ovr' => $request->phy_error_cat_ovr,
                                'prior_auth_basis_type' => $request->prior_auth_basis_type,
                                'prior_auth_note' => $request->prior_auth_note,
                                'procedure_code' => $request->procedure_code,
                                'qty_error_cat_ovr' => $request->qty_error_cat_ovr,
                                'refill_error_cat_ovr' => $request->refill_error_cat_ovr,
                                'service_type' => $request->service_type,
                            ]);

            }

            
            return $this->respondWithToken($this->token(),'Record Added Successfully !', $addPriorAuth);
        }else if($request->add_new==0){
            $update = DB::table('PRIOR_AUTHORIZATIONS')
                            ->where('member_id',strtoupper($request->member_id))
                            ->where('customer_id', strtoupper($request->customer_id))
                            ->where('client_id', strtoupper($request->client_id))
                            ->where('client_group_id', strtoupper($request->client_group_id))
                            ->update([
                                //Authorization Tab Starts
                                'prior_auth_code_num' => $request->prior_auth_code_num,
                                'prior_auth_type' => $request->prior_auth_type,
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

                                'accum_bene_error_cat_ovr' => $request->accum_bene_error_cat_ovr,
                                'benefit_code' => $request->benefit_code,
                                'brand_copay_amt' => $request->brand_copay_amt,
                                'days_supply_error_cat_ovr' => $request->days_supply_error_cat_ovr,
                                'diagnosis_id' => $request->diagnosis_id,
                                'drug_error_cat_ovr' => $request->drug_error_cat_ovr,
                                'elig_error_cat_ovr' => $request->elig_error_cat_ovr,
                                'generic_copay_amt' => $request->generic_copay_amt,
                                'max_daily_dose' => $request->max_daily_dose,
                                'max_days' => $request->max_days,
                                'max_dollar_amt' => $request->max_dollar_amt,
                                'max_num_fills' => $request->max_num_fills,
                                'max_quantity' => $request->max_quantity,
                                'num_fills_used' => $request->num_fills_used,
                                'oltp_date_used' => $request->oltp_date_used,
                                'patient_paid_diff_flag'=> $request->patient_paid_diff_flag,
                                'pharm_error_cat_ovr' => $request->pharm_error_cat_ovr,
                                'pharmacy_nabp' => $request->pharmacy_nabp,
                                'pharmacy_status_override' => $request->pharmacy_status_override,
                                'phy_error_cat_ovr' => $request->phy_error_cat_ovr,
                                'prior_auth_basis_type' => $request->prior_auth_basis_type,
                                'prior_auth_note' => $request->prior_auth_note,
                                'procedure_code' => $request->procedure_code,
                                'qty_error_cat_ovr' => $request->qty_error_cat_ovr,
                                'refill_error_cat_ovr' => $request->refill_error_cat_ovr,
                                'service_type' => $request->service_type,
                                 //Pricing/ Misc tab ends                            
                            ]);
            return $this->respondWithToken($this->token(),'Record Updated Successfully!', $update);
        }
    }
}
