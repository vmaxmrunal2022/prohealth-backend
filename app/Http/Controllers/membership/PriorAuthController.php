<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PriorAuthController extends Controller
{
    use AuditTrait;
    public function get(Request $request)
    {
        $priorAuthList = DB::table('PRIOR_AUTHORIZATIONS')
            ->where('member_id', 'like', '%' . $request->search . '%')
            ->orWhere('person_code', 'like', '%' . $request->search . '%')
            ->orWhere('customer_id', 'like', '%' . $request->search . '%')
            ->orWhere('client_id', 'like', '%' . $request->search . '%')
            ->orWhere('client_group_id', 'like', '%' . $request->search . '%')
            ->orWhere('prior_auth_code_num', 'like', '%' . $request->search . '%')

            ->get();


        return $this->respondWithToken($this->token(), '', $priorAuthList);
    }

    public function priorAuthCodeGenerate(Request $request)
    {

        $today_date = date("Ymd");

        $today_count = DB::table('PRIOR_AUTHORIZATIONS')
            ->where('PRIOR_AUTH_CODE_NUM', 'like', '%' . $today_date . '%')->get()->count();

        $count_increment = $today_count + 1;
        $prior_auth_code = date("Ymd");
        $prior_auth_code .= str_pad($count_increment, 4, "0", STR_PAD_LEFT);

        return $this->respondWithToken($this->token(), 'Auto Generate Prior Auth Code', $prior_auth_code);
    }

    public function submitPriorAuthorization(Request $request)
    {


        $getEligibilityData = DB::table('PRIOR_AUTHORIZATIONS')
            ->where('customer_id', $request->customer_id)
            ->where('client_id', $request->client_id)
            ->where('client_group_id', $request->client_group_id)
            ->where('plan_id', $request->plan_id)
            ->where('prior_auth_code_num', $request->prior_auth_code_num)
            ->first();



        // $today_date=date("Ymd");

        // $today_count=DB::table('PRIOR_AUTHORIZATIONS')
        // ->where('PRIOR_AUTH_CODE_NUM','like', '%'. $today_date. '%')->get()->count();

        // $count_increment = $today_count +1;
        // $prior_auth_code = date("Ymd");
        // $prior_auth_code .= str_pad($count_increment,4,"0",STR_PAD_LEFT );

        // dd($today);

        // echo $today;







        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'member_id' => ['required', 'max:18', Rule::unique('PLAN_BENEFIT_TABLE')->where(function ($q) {
                    $q->whereNotNull('plan_id');
                })],
                'person_code' => ['max:3'],
                'customer_id' => ['required', 'max:10'],
                'client_id' => ['required', 'max:15'],
                'client_group_id' => ['max:15'],
                'ndc' => ['max:11'],
                'generic_product_id' => ['max:14'],
                'prior_auth_type' => ['max:1'],
                'prior_auth_code_num' => ['max:12'],
                'prescriber_id' => ['max:10'],
                'prescriber_status_override' => ['max:1'],
                'pharmacy_nabp' => ['max:12'],
                'pharmacy_status_override' => ['max:1'],
                'effective_date' => ['min:0', 'max:10'],
                'termination_date' => ['min:0', 'max:10', 'after:effective_date'],
                'max_quantity' => ['min:0', 'max:6'],
                'max_days' => ['min:0', 'max:6'],
                'max_daily_dose' => ['min:0', 'max:6'],
                'patient_pin_number' => ['max:22'],
                'user_id' => ['max:10'],
                'form_id' => ['max:10'],
                'prior_auth_note' => ['max:6'],
                'prior_auth_basis_type' => ['min:0', 'max:6'],
                'elig_error_cat_ovr' => ['max:1'],
                'phy_error_cat_ovr' => ['max:1'],
                'pharm_error_cat_ovr' => ['max:1'],
                'drug_error_cat_ovr' => ['max:1'],
                'qty_error_cat_ovr' => ['max:1'],
                'days_supply_error_cat_ovr' => ['max:1'],
                'refill_error_cat_ovr' => ['max:1'],
                'generic_indicator' => ['max:1'],
                'accum_bene_error_cat_ovr' => ['max:1'],
                'all_oth_error_cat_ovr' => ['max:1'],
                'price_sched_ovr' => ['max:10'],
                'copay_sched_ovr' => ['max:10'],
                'brand_copay_amt' => ['min:2', 'max:12'],
                'generic_copay_amt' => ['min:2', 'max:12'],
                'patient_paid_diff_flag' => ['max:1'],
                'birth_date' => ['min:0', 'max:8'],
                'relationship' => ['max:1'],
                'plan_id' => ['max:15'],
                'max_dollar_amt' => ['min:2', 'max:12'],
                'accum_bene_exclude_flag' => ['max:1'],
                'max_num_fills' => ['min:0', 'max:2'],
                'num_fills_used' => ['min:0', 'max:2'],
                'oltp_date_used' => ['min:0', 'max:8'],
                'copay_sched_ovr_mail' => ['max:10'],
                'brand_copay_amt_mail' => ['min:2', 'max:12'],
                'generic_copay_amt_mail' => ['min:2', 'max:12'],
                'user_id_created' => ['max:10'],
                'benefit_code' => ['max:10'],
                'procedure_code' => ['max:10'],
                'service_type' => ['max:2'],
                'provider_type' => ['max:2'],
                'diagnosis_id' => ['max:8'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            }

            if ($getEligibilityData) {

                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getEligibilityData);
            } else {
                $addPriorAuth = DB::table('PRIOR_AUTHORIZATIONS')
                    ->insert([
                        //Authorization Tab Starts
                        'customer_id' => $request->customer_id,
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
                        'patient_paid_diff_flag' => $request->patient_paid_diff_flag,
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

            $inserted_record = DB::table('PRIOR_AUTHORIZATIONS')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->where('plan_id', $request->plan_id)
                ->first();
            $record_snap = json_encode($inserted_record);
            $save_audit = $this->auditMethod('IN', $record_snap, 'PRIOR_AUTHORIZATIONS');

            return $this->respondWithToken('success', 'Record Added Successfully', $inserted_record, $this->token(), 200);
        } else if ($request->add_new == 0) {


            $validator = Validator::make($request->all(), [
                'member_id' => ['required', 'max:18'],
                'person_code' => ['max:3'],
                'customer_id' => ['required', 'max:10'],
                'client_id' => ['required', 'max:15'],
                'client_group_id' => ['max:15'],
                'ndc' => ['max:11'],
                'generic_product_id' => ['max:14'],
                'prior_auth_type' => ['max:1'],
                'prior_auth_code_num' => ['max:12'],
                'prescriber_id' => ['max:10'],
                'prescriber_status_override' => ['max:1'],
                'pharmacy_nabp' => ['max:12'],
                'pharmacy_status_override' => ['max:1'],
                'effective_date' => ['min:0', 'max:10'],
                'termination_date' => ['min:0', 'max:10', 'after:effective_date'],
                'max_quantity' => ['min:0', 'max:6'],
                'max_days' => ['min:0', 'max:6'],
                'max_daily_dose' => ['min:0', 'max:6'],
                'patient_pin_number' => ['max:22'],
                'user_id' => ['max:10'],
                'form_id' => ['max:10'],
                'prior_auth_note' => ['max:6'],
                'prior_auth_basis_type' => ['min:0', 'max:6'],
                'elig_error_cat_ovr' => ['max:1'],
                'phy_error_cat_ovr' => ['max:1'],
                'pharm_error_cat_ovr' => ['max:1'],
                'drug_error_cat_ovr' => ['max:1'],
                'qty_error_cat_ovr' => ['max:1'],
                'days_supply_error_cat_ovr' => ['max:1'],
                'refill_error_cat_ovr' => ['max:1'],
                'generic_indicator' => ['max:1'],
                'accum_bene_error_cat_ovr' => ['max:1'],
                'all_oth_error_cat_ovr' => ['max:1'],
                'price_sched_ovr' => ['max:10'],
                'copay_sched_ovr' => ['max:10'],
                'brand_copay_amt' => ['min:2', 'max:12'],
                'generic_copay_amt' => ['min:2', 'max:12'],
                'patient_paid_diff_flag' => ['max:1'],
                'birth_date' => ['min:0', 'max:8'],
                'relationship' => ['max:1'],
                'plan_id' => ['max:15'],
                'max_dollar_amt' => ['min:2', 'max:12'],
                'accum_bene_exclude_flag' => ['max:1'],
                'max_num_fills' => ['min:0', 'max:2'],
                'num_fills_used' => ['min:0', 'max:2'],
                'oltp_date_used' => ['min:0', 'max:8'],
                'copay_sched_ovr_mail' => ['max:10'],
                'brand_copay_amt_mail' => ['min:2', 'max:12'],
                'generic_copay_amt_mail' => ['min:2', 'max:12'],
                'user_id_created' => ['max:10'],
                'benefit_code' => ['max:10'],
                'procedure_code' => ['max:10'],
                'service_type' => ['max:2'],
                'provider_type' => ['max:2'],
                'diagnosis_id' => ['max:8'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            }
            // dd($request->prior_auth_code_num);

            $update = DB::table('PRIOR_AUTHORIZATIONS')
                ->where('prior_auth_code_num', $request->prior_auth_code_num)
                // ->where('member_id',$request->member_id)
                // ->where('customer_id',$request->customer_id)
                // ->where('client_id', $request->client_id)
                // ->where('client_group_id',$request->client_group_id)
                ->update([
                    //Authorization Tab Starts
                    // 'prior_auth_code_num' => $prior_auth_code,
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
                    'patient_paid_diff_flag' => $request->patient_paid_diff_flag,
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

            $inserted_record = DB::table('PRIOR_AUTHORIZATIONS')
                ->where('prior_auth_code_num', $request->prior_auth_code_num)
                ->first();
            $record_snap = json_encode($inserted_record);
            $save_audit = $this->auditMethod('UP', $record_snap, 'PRIOR_AUTHORIZATIONS');
            return $this->respondWithToken($this->token(), 'Record Updated Successfully!', $inserted_record);
        }
    }
}
