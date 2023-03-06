<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProviderDataProviderController extends Controller
{


    public function add1(Request $request)
    {


        // $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', $request->pharmacy_nabp)->first();

        // dd($benefitcode);



        // $createddate = date( 'y-m-d' );



        $createddate = DB::table('PHARMACY_TABLE')
            ->where('pharmacy_nabp', $request->pharmacy_nabp)
            ->update(
                [
                    'pharmacy_nabp' => $request->pharmacy_nabp,
                    'pharmacy_name' => $request->pharmacy_name,
                    // 'address_1'=>$request->address_1,
                    // 'provider_first_name'=>$request->provider_first_name,
                    // 'provider_last_name'=>$request->provider_last_name,
                    // 'pharmacy_class'=>$request->pharmacy_class,
                    // 'address_2'=>$request->address_2,

                    // 'city'=>$request->city,
                    // 'state'=>$request->state,
                    // 'zip_code'=>$request->zip_code,
                    // 'zip_plus_2'=>$request->zip_plus_2,
                    // 'phone'=>$request->phone, 
                    // 'fax'=>$request->fax,
                    // 'mailing_address_1'=>$request->mailing_address_1,
                    // 'mailing_address_2'=>$request->mailing_address_2,
                    // 'mailing_city'=>$request->mailing_city,
                    // 'mailing_state'=>$request->mailing_state,
                    // 'mailing_zip_code'=>$request->mailing_zip_code,
                    // 'mailing_zip_plus_2'=>$request->mailing_zip_plus_2,
                    // 'edi_address'=>$request->edi_address,
                    // 'pharmacy_class'=>$request->pharmacy_class,
                    // 'aba_rtn'=>$request->aba_rtn,
                    // 'store_number'=>$request->store_number,
                    // 'head_office_ind'=>$request->head_office_ind,
                    // 'pharmacy_chain'=>$request->pharmacy_chain,
                    // 'mail_order'=>$request->mail_order,
                    // 'region'=>$request->region,
                    // 'district'=>$request->district,
                    // 'market'=>$request->market,

                    // 'price_zone'=>$request->price_zone,
                    // 'scd_age_threshold'=>$request->scd_age_threshold,
                    // 'market'=>$request->market,
                    // 'market'=>$request->market,


                ]
            );

        // // dd($request->pharmacy_nabp);

        // $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', $request->pharmacy_nabp)->first();

        $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', '%' . $request->pharmacy_nabp . '%')->first();


        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }




    public function add(Request $request)
    {
        $getEligibilityData = DB::table('PHARMACY_TABLE')
            ->where('pharmacy_nabp', strtoupper($request->pharmacy_nabp))
            ->first();

        // dd($request->all());

        if ($request->add_new == 1) {
            if ($getEligibilityData) {
                return $this->respondWithToken($this->token(), 'This record is already exists ..!!!');
            } else {

                $addData = DB::table('PHARMACY_TABLE')
                    ->insert([
                        'PHARMACY_NABP' => strtoupper($request->pharmacy_nabp),
                        'PHARMACY_NAME' => ($request->pharmacy_name),
                        'ADDRESS_1' => $request->address_1,
                        'ADDRESS_2' => $request->address_2,
                        'CITY' => $request->city,
                        'STATE' => $request->state,
                        'ZIP_CODE' => $request->zip_code,
                        'ZIP_PLUS_2' => $request->zip_plus_2,
                        'PHONE' => $request->phone,
                        'FAX' => $request->fax,
                        'MAILING_ADDRESS_1' => $request->mailing_address_1,
                        'MAILING_ADDRESS_2' => $request->mailing_address__2,
                        'MAILING_CITY' => $request->mailing_city,
                        'MAILING_STATE' => $request->mailing_state,
                        'MAILING_ZIP_CODE' => $request->mailing_zip_code,
                        'MAILING_ZIP_PLUS_2' => $request->mailing_zip_plus_2,
                        'EDI_ADDRESS' => $request->edi_address,
                        'PHARMACY_CLASS' => $request->pharmacy_class,
                        'ABA_RTN' => $request->aba_rtn,
                        'CONTACT' => $request->contact,
                        'STORE_NUMBER' => $request->store_number,
                        'HEAD_OFFICE_IND' => $request->head_office_ind,
                        'PHARMACY_CHAIN' => $request->pharmacy_chain,
                        'MAIL_ORDER' => $request->mail_order,
                        'REGION' => $request->region,
                        'DISTRICT' => $request->district,
                        'MARKET' => $request->market,
                        'PRICE_ZONE' => $request->price_zone,
                        'SCD_AGE_THRESHOLD' => $request->scd_age_threshold,
                        'PAYMENT_CYCLE' => $request->payment_cycle,
                        'PAYMENT_TYPE' => $request->payment_type,
                        'CAP_AMOUNT' => $request->cap_amount,
                        'COMM_CHARGE_PAID' => $request->comm_charge_paid,
                        'COMM_CHARGE_REJECT' => $request->comm_charge_reject,
                        'REIMB_PREFERENCE' => $request->reimb_preference,
                        'RECORD_USAGE' => $request->record_usage,
                        'BASE_PHARMACY_NABP' => $request->base_pharmacy_nabp,
                        'COUNTY' => $request->country,
                        'TAX_SCHEDULE_ID_1' => $request->tax_schedule_id_1,
                        'TAX_EFFECTIVE_DATE_1' => $request->tax_effective_date_1,
                        'TAX_TERMINATION_DATE_1' => $request->tax_termination_date_1,
                        'TAX_SCHEDULE_ID_2' => $request->tax_schedule_id_2,
                        'TAX_EFFECTIVE_DATE_2' => $request->tax_effective_date_2,
                        'TAX_TERMINATION_DATE_2' => $request->tax_termination_date_2,
                        'TAX_SCHEDULE_ID_3' => $request->tax_schedule_id_3,
                        'TAX_EFFECTIVE_DATE_3' => $request->tax_effective_date_3,
                        'TAX_TERMINATION_DATE_3' => $request->tax_termination_date_3,
                        'WITHHOLD_PAID_AMOUNT' => $request->withhold_paid_amount,
                        'WITHHOLD_PAID_PERCENT' => $request->withhold_paid_percent,
                        'WITHHOLD_REJECT_AMOUNT' => $request->withhold_reject_amount,
                        'WITHHOLD_REJECT_PERCENT' => $request->withhold_reject_percent,
                        'WITHHOLD_REVERSED_AMOUNT' => $request->withhold_reversed_amount,
                        'WITHHOLD_REVERSED_PERCENT' => $request->withhold_reversed_percent,
                        'WITHHOLD_U_AND_C_FLAG' => $request->withhold_u_and_c_flag,
                        'WITHHOLD_ACTIVE_FLAG' => $request->withhold_active_flag,
                        'EFFECTIVE_DATE_1' => $request->effective_date_1,
                        'EFFECTIVE_DATE_2' => $request->effective_date_2,
                        'TERMINATION_DATE_2' => $request->termination_date_2,
                        'EFFECTIVE_DATE_3' => $request->effective_date_3,
                        'TERMINATION_DATE_3' => $request->termination_date_3,
                        'PHARMACY_STATUS' => $request->pharmacy_status,
                        'DISPENSER_CLASS' => $request->dispenser_class,
                        'DISPENSER_TYPE' => $request->dispenser_type,
                        'PROVIDER_FIRST_NAME' => $request->provider_first_name,
                        'PROVIDER_LAST_NAME' => $request->provider_last_name,
                        'MAILING_COUNTRY' => $request->mailing_country,
                        'COUNTRY_CODE' => $request->country_code,
                        'MAILING_COUNTRY_CODE' => $request->mailing_country_code,


                    ]);
            }

            if ($addData) {
                return $this->respondWithToken($this->token(), 'Added Successfully...!!!', $addData);
            }
        } else if ($request->updateForm == 'update') {
            $updateData = DB::table('PHARMACY_TABLE')
                ->where('pharmacy_nabp', strtoupper($request->pharmacy_nabp))
                ->update([
                    'PHARMACY_NAME' => ($request->pharmacy_name),
                    'ADDRESS_1' => $request->address_1,
                    'ADDRESS_2' => $request->address_2,
                    'CITY' => $request->city,
                    'STATE' => $request->state,
                    'ZIP_CODE' => $request->zip_code,
                    'ZIP_PLUS_2' => $request->zip_plus_2,
                    'PHONE' => $request->phone,
                    'FAX' => $request->fax,
                    'MAILING_ADDRESS_1' => $request->mailing_address_1,
                    'MAILING_ADDRESS_2' => $request->mailing_address__2,
                    'MAILING_CITY' => $request->mailing_city,
                    'MAILING_STATE' => $request->mailing_state,
                    'MAILING_ZIP_CODE' => $request->mailing_zip_code,
                    'MAILING_ZIP_PLUS_2' => $request->mailing_zip_plus_2,
                    'EDI_ADDRESS' => $request->edi_address,
                    'PHARMACY_CLASS' => $request->pharmacy_class,
                    'ABA_RTN' => $request->aba_rtn,
                    'CONTACT' => $request->contact,
                    'STORE_NUMBER' => $request->store_number,
                    'HEAD_OFFICE_IND' => $request->head_office_ind,
                    'PHARMACY_CHAIN' => $request->pharmacy_chain,
                    'MAIL_ORDER' => $request->mail_order,
                    'REGION' => $request->region,
                    'DISTRICT' => $request->district,
                    'MARKET' => $request->market,
                    'PRICE_ZONE' => $request->price_zone,
                    'SCD_AGE_THRESHOLD' => $request->scd_age_threshold,
                    'PAYMENT_CYCLE' => $request->payment_cycle,
                    'PAYMENT_TYPE' => $request->payment_type,
                    'CAP_AMOUNT' => $request->cap_amount,
                    'COMM_CHARGE_PAID' => $request->comm_charge_paid,
                    'COMM_CHARGE_REJECT' => $request->comm_charge_reject,
                    'REIMB_PREFERENCE' => $request->reimb_preference,
                    'RECORD_USAGE' => $request->record_usage,
                    'BASE_PHARMACY_NABP' => $request->base_pharmacy_nabp,
                    'COUNTY' => $request->country,
                    'TAX_SCHEDULE_ID_1' => $request->tax_schedule_id_1,
                    'TAX_EFFECTIVE_DATE_1' => $request->tax_effective_date_1,
                    'TAX_TERMINATION_DATE_1' => $request->tax_termination_date_1,
                    'TAX_SCHEDULE_ID_2' => $request->tax_schedule_id_2,
                    'TAX_EFFECTIVE_DATE_2' => $request->tax_effective_date_2,
                    'TAX_TERMINATION_DATE_2' => $request->tax_termination_date_2,
                    'TAX_SCHEDULE_ID_3' => $request->tax_schedule_id_3,
                    'TAX_EFFECTIVE_DATE_3' => $request->tax_effective_date_3,
                    'TAX_TERMINATION_DATE_3' => $request->tax_termination_date_3,
                    'WITHHOLD_PAID_AMOUNT' => $request->withhold_paid_amount,
                    'WITHHOLD_PAID_PERCENT' => $request->withhold_paid_percent,
                    'WITHHOLD_REJECT_AMOUNT' => $request->withhold_reject_amount,
                    'WITHHOLD_REJECT_PERCENT' => $request->withhold_reject_percent,
                    'WITHHOLD_REVERSED_AMOUNT' => $request->withhold_reversed_amount,
                    'WITHHOLD_REVERSED_PERCENT' => $request->withhold_reversed_percent,
                    'WITHHOLD_U_AND_C_FLAG' => $request->withhold_u_and_c_flag,
                    'WITHHOLD_ACTIVE_FLAG' => $request->withhold_active_flag,
                    'EFFECTIVE_DATE_1' => $request->effective_date_1,
                    'EFFECTIVE_DATE_2' => $request->effective_date_2,
                    'TERMINATION_DATE_2' => $request->termination_date_2,
                    'EFFECTIVE_DATE_3' => $request->effective_date_3,
                    'TERMINATION_DATE_3' => $request->termination_date_3,
                    'PHARMACY_STATUS' => $request->pharmacy_status,
                    'DISPENSER_CLASS' => $request->dispenser_class,
                    'DISPENSER_TYPE' => $request->dispenser_type,
                    'PROVIDER_FIRST_NAME' => $request->provider_first_name,
                    'PROVIDER_LAST_NAME' => $request->provider_last_name,
                    'MAILING_COUNTRY' => $request->mailing_country,
                    'COUNTRY_CODE' => $request->country_code,
                    'MAILING_COUNTRY_CODE' => $request->mailing_country_code,

                ]);
            return $this->respondWithToken($this->token(), 'Updated Successfully...!!!', $updateData);
        }
    }

    public function TraditionalIdsearch(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_NAMES')
            ->select('NETWORK_ID', 'NETWORK_NAME')
            ->where('NETWORK_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function FlexibleIdsearch(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES')
            ->select('RX_NETWORK_RULE_ID', 'RX_NETWORK_RULE_NAME')
            ->where('RX_NETWORK_RULE_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function search(Request $request)
    {
        $ndc = DB::table('PHARMACY_TABLE')

            ->where('PHARMACY_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getProviderList($ndcid)
    {
        // $ndclist = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
        // ->where('PROV_TYPE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')
        //         // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        //         ->get();


        $ndclist = DB::table('PHARMACY_VALIDATIONS')
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')

            // ->select('DIAGNOSIS_VALIDATIONS.DIAGNOSIS_LIST', 'DIAGNOSIS_VALIDATIONS.DIAGNOSIS_ID','DIAGNOSIS_CODES.DESCRIPTION as Description')
            // ->where('PHARMACY_LIST', 'like', '%' .strtoupper($request->search). '%')
            // ->orWhere('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }


    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('PHARMACY_TABLE')
            ->where('PHARMACY_NABP', $ndcid)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function addTraditionalNetwork(Request $request)
    {
        $getEligibilityData = DB::table('RX_NETWORKS')
            ->where('pharmacy_nabp', $request->pharmacy_nabp)
            ->first();
        if ($request->has('new')) {
            if (!$getEligibilityData) {
                $addData = DB::table('RX_NETWORKS')
                    ->insert([
                        'NETWORK_ID' => strtoupper($request->network_id),
                        'PHARMACY_NABP' => ($request->pharmacy_nabp),
                        'PRICE_SCHEDULE_OVRD' => $request->price_schedule_ovrd,
                        'PARTICIPATION_OVRD' => $request->participation_ovrd,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,


                    ]);
                return $this->respondWithToken($this->token(), 'Added Successfully...!!!', $addData);
            } else {
                return $this->respondWithToken($this->token(), 'This record is already exists ..!!!');
            }
        } else {
            $updateData = DB::table('RX_NETWORKS')
                ->where('network_id', $request->network_id)
                ->where('pharmacy_nabp', $request->pharmacy_nabp)
                ->update([
                    'PHARMACY_NABP' => ($request->pharmacy_nabp),
                    'PRICE_SCHEDULE_OVRD' => $request->price_schedule_ovrd,
                    'PARTICIPATION_OVRD' => $request->participation_ovrd,
                    'EFFECTIVE_DATE' => $request->effective_date,
                    'TERMINATION_DATE' => $request->termination_date,

                ]);
            return $this->respondWithToken($this->token(), 'Updated Successfully...!!!', $updateData);
        }
    }

    public function addFlexibleNetwork(Request $request)
    {
        $getEligibilityData = DB::table('RX_NETWORK_RULES')
            ->where('PHARMACY_CHAIN', $request->pharmacy_chain)
            ->where('RX_NETWORK_RULE_ID', $request->rx_network_rule_id)
            ->first();
        if ($request->add_new) {
            if (!$getEligibilityData) {
                $addData = DB::table('RX_NETWORK_RULES')
                    ->insert([
                        'RX_NETWORK_RULE_ID' => strtoupper($request->rx_network_rule_id),
                        'PHARMACY_CHAIN' => ($request->pharmacy_chain),
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,


                    ]);
                return $this->respondWithToken($this->token(), 'Added Successfully...!!!', $addData);
            } else {
                return $this->respondWithToken($this->token(), 'This record is already exists ..!!!');
            }
        } else {
            $updateData = DB::table('RX_NETWORK_RULES')
                ->where('rx_network_rule_id', $request->rx_network_rule_id)
                ->where('PHARMACY_CHAIN', $request->pharmacy_chain)
                ->update([
                    'EFFECTIVE_DATE' => $request->effective_date,
                    'TERMINATION_DATE' => $request->termination_date,

                ]);
            return $this->respondWithToken($this->token(), 'Updated Successfully...!!!', $updateData);
        }
    }

    public function getProviderNetworks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $this->errors(), "false");
        } else {
            $provider_networks = DB::table('RX_NETWORK_NAMES')
                ->where(DB::raw('UPPER(NETWORK_ID)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $provider_networks);
        }
    }
}
