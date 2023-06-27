<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProviderDataProviderController extends Controller
{

use AuditTrait;
    // public function add1(Request $request)
    // {


    //     // $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', $request->pharmacy_nabp)->first();

    //     // dd($benefitcode);



    //     // $createddate = date( 'y-m-d' );



    //     $createddate = DB::table('PHARMACY_TABLE')
    //         ->where('pharmacy_nabp', $request->pharmacy_nabp)
    //         ->update(
    //             [
    //                 'pharmacy_nabp' => $request->pharmacy_nabp,
    //                 'pharmacy_name' => $request->pharmacy_name,
    //                 // 'address_1'=>$request->address_1,
    //                 // 'provider_first_name'=>$request->provider_first_name,
    //                 // 'provider_last_name'=>$request->provider_last_name,
    //                 // 'pharmacy_class'=>$request->pharmacy_class,
    //                 // 'address_2'=>$request->address_2,

    //                 // 'city'=>$request->city,
    //                 // 'state'=>$request->state,
    //                 // 'zip_code'=>$request->zip_code,
    //                 // 'zip_plus_2'=>$request->zip_plus_2,
    //                 // 'phone'=>$request->phone, 
    //                 // 'fax'=>$request->fax,
    //                 // 'mailing_address_1'=>$request->mailing_address_1,
    //                 // 'mailing_address_2'=>$request->mailing_address_2,
    //                 // 'mailing_city'=>$request->mailing_city,
    //                 // 'mailing_state'=>$request->mailing_state,
    //                 // 'mailing_zip_code'=>$request->mailing_zip_code,
    //                 // 'mailing_zip_plus_2'=>$request->mailing_zip_plus_2,
    //                 // 'edi_address'=>$request->edi_address,
    //                 // 'pharmacy_class'=>$request->pharmacy_class,
    //                 // 'aba_rtn'=>$request->aba_rtn,
    //                 // 'store_number'=>$request->store_number,
    //                 // 'head_office_ind'=>$request->head_office_ind,
    //                 // 'pharmacy_chain'=>$request->pharmacy_chain,
    //                 // 'mail_order'=>$request->mail_order,
    //                 // 'region'=>$request->region,
    //                 // 'district'=>$request->district,
    //                 // 'market'=>$request->market,

    //                 // 'price_zone'=>$request->price_zone,
    //                 // 'scd_age_threshold'=>$request->scd_age_threshold,
    //                 // 'market'=>$request->market,
    //                 // 'market'=>$request->market,


    //             ]
    //         );

    //     // // dd($request->pharmacy_nabp);

    //     // $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', $request->pharmacy_nabp)->first();

    //     $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', '%' . $request->pharmacy_nabp . '%')->first();


    //     return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    // }




    public function add(Request $request)
    {
        // dd('hii');
        $getEligibilityData = DB::table('PHARMACY_TABLE')
            ->where('pharmacy_nabp', $request->pharmacy_nabp)
            ->first();

        $createddate = date('y-m-d');


        // dd($request->all());

        if ($request->add_new == 1) {
            // if ($getEligibilityData) {
            //     return $this->respondWithToken($this->token(), 'This record is already exists ..!!!');
            // } 
            $validator = Validator::make($request->all(), [
                'pharmacy_nabp' => [
                    'required', 'max:10',
                    Rule::unique('PHARMACY_TABLE')->where(function ($q) use ($request) {
                        $q->whereNotNull('PHARMACY_NABP');
                    })
                ],
                "pharmacy_name" => ['required', 'max:35'],
                'effective_date_1' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate2 = $request->effective_date_2;
                        $effdate3 = $request->effective_date_3;
                        if ($value <= $effdate3) {
                            $fail('Effective date 1 must be greater than Effective date 3.');
                        }
                        if ($value <= $effdate2) {
                            $fail('Effective date 1 must be greater than Effective date 2.');
                        }
                    }
                ],
                'effective_date_2' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate3 = $request->effective_date_3;
                        $termdate1 = $request->termination_date_1;
                        if ($value <= $effdate3) {
                            $fail('Effective date 2 must be greater than Effective date 3.');
                        }
                        if ($value >= $termdate1) {
                            $fail('Effective date 2 must be less than Termination date 1.');
                        }
                    }
                ],
                'effective_date_3' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $termdate2 = $request->termination_date_2;
                        $termdate1 = $request->termination_date_1;

                        if ($value >= $termdate1) {
                            $fail('Effective date 3 must be less than Termination date 1.');
                        }
                        if ($value >= $termdate2) {
                            $fail('Effective date 3 must be  less than Termination date 2.');
                        }
                    }
                ],
                'tax_effective_date_1' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate2 = $request->tax_effective_date_2;
                        $effdate3 = $request->tax_effective_date_3;
                        if ($value <= $effdate3) {
                            $fail('Tax Effective date 1 must be greater than Tax Effective date 3.');
                        }
                        if ($value <= $effdate2) {
                            $fail('Tax Effective date 1 must be greater than Tax Effective date 2.');
                        }
                    }
                ],
                'tax_effective_date_2' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate3 = $request->tax_effective_date_3;
                        $termdate1 = $request->tax_termination_date_1;
                        if ($value <= $effdate3) {
                            $fail('Tax Effective date 2 must be greater than Tax Effective date 3.');
                        }
                        if ($value >= $termdate1) {
                            $fail('Tax Effective date 2 must be less than Tax Termination date 1.');
                        }
                    }
                ],
                'tax_effective_date_3' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $termdate2 = $request->tax_termination_date_2;
                        $termdate1 = $request->tax_termination_date_1;

                        if ($value >= $termdate1) {
                            $fail('Tax Effective date 3 must be less than Tax Termination date 1.');
                        }
                        if ($value >= $termdate2) {
                            $fail('Tax Effective date 3 must be  less than Tax Termination date 2.');
                        }
                    }
                ],
                "termination_date_1" => ['nullable', 'after:effective_date_1'],
                "termination_date_2" => ['nullable', 'after:effective_date_2'],
                "termination_date_3" => ['nullable', 'after:effective_date_3'],
                "tax_termination_date_1" => ['nullable', 'after:tax_effective_date_1'],
                "tax_termination_date_2" => ['nullable', 'after:tax_effective_date_2'],
                "tax_termination_date_3" => ['nullable', 'after:tax_effective_date_3'],
            ], [
                'termination_date_1.after' => 'Termination date 1 must be greater than Effective date 1',
                'termination_date_2.after' => 'Termination date 2 must be greater than Effective date 2',
                'termination_date_3.after' => 'Termination date 3 must be greater than Effective date 3',
                'tax_termination_date_1.after' => 'Tax Termination date 1 must be greater than Tax Effective date 1',
                'tax_termination_date_2.after' => 'Tax Termination date 2 must be greater than Tax Effective date 2',
                'tax_termination_date_3.after' => 'Tax Termination date 3 must be greater than Tax Effective date 3',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } else {

                $addData = DB::table('PHARMACY_TABLE')
                    ->insert([
                        'PHARMACY_NABP' => $request->pharmacy_nabp,
                        'PHARMACY_NAME' => $request->pharmacy_name,
                        'ADDRESS_1' => $request->address_1,
                        'ADDRESS_2' => $request->address_2,
                        'CITY' => $request->city,
                        'STATE' => $request->state,
                        'ZIP_CODE' => $request->zip_code,
                        'ZIP_PLUS_2' => $request->zip_plus_2,
                        'PHONE' => $request->phone,
                        'FAX' => $request->fax,
                        'MAILING_ADDRESS_1' => $request->mailing_address_1,
                        'MAILING_ADDRESS_2' => $request->mailing_address_2,
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
                        'COUNTY' => $request->county,
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

                        'TERMINATION_DATE_1' => $request->termination_date_1,
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

                $get_provider = DB::table('PHARMACY_TABLE')
                    ->where(DB::raw('UPPER(pharmacy_nabp)'), strtoupper($request->pharmacy_nabp))
                    ->first();

                $save_adit = $this->auditmethod('IN', json_encode($get_provider), 'PHARMACY_TABLE');

                $traditional_list_obj = json_decode(json_encode($request->traditional_form, true));

                if (!empty($request->traditional_form)) {
                    $traditional_list = $traditional_list_obj[0];

                    foreach ($traditional_list_obj as $key => $traditional_list) {

                        $rx_networks = DB::table('RX_NETWORKS')->insert(
                            [
                                'NETWORK_ID' => $traditional_list->network_id,
                                'PHARMACY_NABP' => $traditional_list->pharmacy_nabp,
                                'PRICE_SCHEDULE_OVRD' => $traditional_list->price_schedule_ovrd,
                                // 'PARTICIPATION_OVRD' => $traditional_list->participation_ovrd,
                                'DATE_TIME_CREATED' => $createddate,
                                'DATE_TIME_MODIFIED' => $createddate,
                                'EFFECTIVE_DATE' => $traditional_list->effective_date,
                                'TERMINATION_DATE' => $traditional_list->termination_date,

                            ]
                        );
                        $rx_networksnames = DB::table('RX_NETWORK_NAMES')
                            ->where('NETWORK_ID', $traditional_list->network_id,)
                            ->update(
                                [
                                    // 'NETWORK_ID' => $traditional_list->network_id,
                                    'NETWORK_NAME' => $traditional_list->network_name,

                                ]
                            );
                        $get_parent = DB::table('RX_NETWORK_NAMES')
                            ->where(DB::raw('UPPER(network_id)'), strtoupper($traditional_list->network_id))
                            ->where(DB::raw('UPPER(pharmacy_nabp)'), strtoupper($traditional_list->pharmacy_nabp))
                            ->first();
                        $get_child = DB::table('RX_NETWORKS')
                            ->where(DB::raw('UPPER(network_id)'), strtoupper($traditional_list->network_id))
                            ->first();
                        $save_audit_parent = $this->auditMethod('IN', json_encode($get_parent), 'RX_NETWORK_NAMES');
                        $save_audit_child = $this->auditMethod('UP', json_encode($get_child), 'RX_NETWORKS');
                    }
                }
            }

            if ($addData) {
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $addData);
            }
        } else if ($request->add_new == 0) {
            $validator = Validator::make($request->all(), [
                'pharmacy_nabp' => [
                    'required', 'max:10',
                    Rule::unique('PHARMACY_TABLE')->where(function ($q) use ($request) {
                        $q->whereNotNull('PHARMACY_NABP');
                        $q->where('PHARMACY_NABP', '!=', $request->pharmacy_nabp);
                    })
                ],
                "pharmacy_name" => ['required', 'max:35'],
                'effective_date_1' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate2 = $request->effective_date_2;
                        $effdate3 = $request->effective_date_3;
                        if ($value <= $effdate3) {
                            $fail('Effective date 1 must be greater than Effective date 3.');
                        }
                        if ($value <= $effdate2) {
                            $fail('Effective date 1 must be greater than Effective date 2.');
                        }
                    }
                ],
                'effective_date_2' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate3 = $request->effective_date_3;
                        $termdate1 = $request->termination_date_1;
                        if ($value <= $effdate3) {
                            $fail('Effective date 2 must be greater than Effective date 3.');
                        }
                        if ($value >= $termdate1) {
                            $fail('Effective date 2 must be less than Termination date 1.');
                        }
                    }
                ],
                'effective_date_3' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $termdate2 = $request->termination_date_2;
                        $termdate1 = $request->termination_date_1;

                        if ($value >= $termdate1) {
                            $fail('Effective date 3 must be less than Termination date 1.');
                        }
                        if ($value >= $termdate2) {
                            $fail('Effective date 3 must be  less than Termination date 2.');
                        }
                    }
                ],
                'tax_effective_date_1' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate2 = $request->tax_effective_date_2;
                        $effdate3 = $request->tax_effective_date_3;
                        if ($value <= $effdate3) {
                            $fail('Tax Effective date 1 must be greater than Tax Effective date 3.');
                        }
                        if ($value <= $effdate2) {
                            $fail('Tax Effective date 1 must be greater than Tax Effective date 2.');
                        }
                    }
                ],
                'tax_effective_date_2' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate3 = $request->tax_effective_date_3;
                        $termdate1 = $request->tax_termination_date_1;
                        if ($value <= $effdate3) {
                            $fail('Tax Effective date 2 must be greater than Tax Effective date 3.');
                        }
                        if ($value >= $termdate1) {
                            $fail('Tax Effective date 2 must be less than Tax Termination date 1.');
                        }
                    }
                ],
                'tax_effective_date_3' => [
                    'nullable', 'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $termdate2 = $request->tax_termination_date_2;
                        $termdate1 = $request->tax_termination_date_1;

                        if ($value >= $termdate1) {
                            $fail('Tax Effective date 3 must be less than Tax Termination date 1.');
                        }
                        if ($value >= $termdate2) {
                            $fail('Tax Effective date 3 must be  less than Tax Termination date 2.');
                        }
                    }
                ],
                "termination_date_1" => ['nullable', 'after:effective_date_1'],
                "termination_date_2" => ['nullable', 'after:effective_date_2'],
                "termination_date_3" => ['nullable', 'after:effective_date_3'],
                "tax_termination_date_1" => ['nullable', 'after:tax_effective_date_1'],
                "tax_termination_date_2" => ['nullable', 'after:tax_effective_date_2'],
                "tax_termination_date_3" => ['nullable', 'after:tax_effective_date_3'],
            ], [
                'termination_date_1.after' => 'Termination date 1 must be greater than Effective date 1',
                'termination_date_2.after' => 'Termination date 2 must be greater than Effective date 2',
                'termination_date_3.after' => 'Termination date 3 must be greater than Effective date 3',
                'tax_termination_date_1.after' => 'Tax Termination date 1 must be greater than Tax Effective date 1',
                'tax_termination_date_2.after' => 'Tax Termination date 2 must be greater than Tax Effective date 2',
                'tax_termination_date_3.after' => 'Tax Termination date 3 must be greater than Tax Effective date 3',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            }

            // dd($request->mailing_country);
        } else if ($request->add_new == 0) {
            $validator = Validator::make($request->all(), [
                'pharmacy_nabp' => ['required', 'max:10',
                 Rule::unique('PHARMACY_TABLE')->where(function ($q) use($request) {
                    $q->whereNotNull('PHARMACY_NABP');
                    $q->where('PHARMACY_NABP','!=',$request->pharmacy_nabp);
                })], 
                "pharmacy_name" => ['required', 'max:35'],
                'effective_date_1' => [
                    'nullable','date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate2 = $request->effective_date_2;
                        $effdate3 = $request->effective_date_3;
                        if ($value <= $effdate3) {
                            $fail('Effective date 1 must be greater than Effective date 3.');
                        }
                        if ($value <= $effdate2) {
                            $fail('Effective date 1 must be greater than Effective date 2.');
                        }
                        
                    }
                ],
                'effective_date_2' => [
                    'nullable','date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate3 = $request->effective_date_3;
                        $termdate1 = $request->termination_date_1;
                        if ($value <= $effdate3) {
                            $fail('Effective date 2 must be greater than Effective date 3.');
                        } 
                        if ($value >= $termdate1) {
                            $fail('Effective date 2 must be less than Termination date 1.');
                        }
                       
                    }
                ],
                'effective_date_3' => [
                    'nullable','date',
                    function ($attribute, $value, $fail) use ($request) {
                        $termdate2 = $request->termination_date_2;
                        $termdate1 = $request->termination_date_1;
                       
                        if ($value >= $termdate1) {
                            $fail('Effective date 3 must be less than Termination date 1.');
                        }
                        if ($value >= $termdate2) {
                            $fail('Effective date 3 must be  less than Termination date 2.');
                        } 
                       
                    }
                ],
                'tax_effective_date_1' => [
                    'nullable','date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate2 = $request->tax_effective_date_2;
                        $effdate3 = $request->tax_effective_date_3;
                        if ($value <= $effdate3) {
                            $fail('Tax Effective date 1 must be greater than Tax Effective date 3.');
                        }
                        if ($value <= $effdate2) {
                            $fail('Tax Effective date 1 must be greater than Tax Effective date 2.');
                        }
                        
                    }
                ],
                'tax_effective_date_2' => [
                    'nullable','date',
                    function ($attribute, $value, $fail) use ($request) {
                        $effdate3 = $request->tax_effective_date_3;
                        $termdate1 = $request->tax_termination_date_1;
                        if ($value <= $effdate3) {
                            $fail('Tax Effective date 2 must be greater than Tax Effective date 3.');
                        } 
                        if ($value >= $termdate1) {
                            $fail('Tax Effective date 2 must be less than Tax Termination date 1.');
                        }
                       
                    }
                ],
                'tax_effective_date_3' => [
                    'nullable','date',
                    function ($attribute, $value, $fail) use ($request) {
                        $termdate2 = $request->tax_termination_date_2;
                        $termdate1 = $request->tax_termination_date_1;
                       
                        if ($value >= $termdate1) {
                            $fail('Tax Effective date 3 must be less than Tax Termination date 1.');
                        }
                        if ($value >= $termdate2) {
                            $fail('Tax Effective date 3 must be  less than Tax Termination date 2.');
                        } 
                       
                    }
                ],
                "termination_date_1" => ['nullable','after:effective_date_1'],
                "termination_date_2" => ['nullable','after:effective_date_2'],
                "termination_date_3" => ['nullable','after:effective_date_3'],
                "tax_termination_date_1" => ['nullable','after:tax_effective_date_1'],
                "tax_termination_date_2" => ['nullable','after:tax_effective_date_2'],
                "tax_termination_date_3" => ['nullable','after:tax_effective_date_3'],
                ],[
                    'termination_date_1.after' => 'Termination date 1 must be greater than Effective date 1',
                    'termination_date_2.after' => 'Termination date 2 must be greater than Effective date 2',
                    'termination_date_3.after' => 'Termination date 3 must be greater than Effective date 3',
                    'tax_termination_date_1.after' => 'Tax Termination date 1 must be greater than Tax Effective date 1',
                    'tax_termination_date_2.after' => 'Tax Termination date 2 must be greater than Tax Effective date 2',
                    'tax_termination_date_3.after' => 'Tax Termination date 3 must be greater than Tax Effective date 3',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } 

            // dd($request->mailing_country);
            $updateData = DB::table('PHARMACY_TABLE')
                ->where('pharmacy_nabp',$request->pharmacy_nabp)
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
                    'MAILING_ADDRESS_2' => $request->mailing_address_2,
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
                    'COUNTY' => $request->county,
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
                    'TERMINATION_DATE_1' => $request->termination_date_1,
                    'TERMINATION_DATE_1' => $request->termination_date_1,
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

                $data = DB::table('RX_NETWORKS')->where('pharmacy_nabp', $request->pharmacy_nabp)->delete();


                $traditional_list_obj = json_decode(json_encode($request->traditional_form, true));
    
                if (!empty($request->traditional_form)) {
                    $traditional_list = $traditional_list_obj[0];

                  
    
                    foreach ($traditional_list_obj as $key => $traditional_list) {


                      
                        $update_rx_networks = DB::table('RX_NETWORKS')->insert(
                            [
                                'NETWORK_ID' => $traditional_list->network_id,
                                'PHARMACY_NABP' => $traditional_list->pharmacy_nabp,
                                'PRICE_SCHEDULE_OVRD' => $traditional_list->price_schedule_ovrd,
                                'PARTICIPATION_OVRD' => $traditional_list->participation_ovrd,
                                'DATE_TIME_CREATED' => $createddate,
                                'DATE_TIME_MODIFIED' => $createddate,
                                'EFFECTIVE_DATE' => $traditional_list->effective_date,
                                'TERMINATION_DATE' => $traditional_list->termination_date,
            
                            ]
                        );


                // $data = DB::table('RX_NETWORK_NAMES')
                // ->where('network_id', $traditional_list->network_id)
                // ->delete();
                // if($data){
                     $rx_networksnames = DB::table('RX_NETWORK_NAMES')
                     ->where('network_id', $traditional_list->network_id)
                     ->update(
                    [
                        // 'NETWORK_ID' => $traditional_list->network_id,
                        'NETWORK_NAME' => $traditional_list->network_name,
                        
    
                    ]
                );


                // }

                      
    
    
                    }
    
                   
    
                }

                $data = DB::table('RX_NETWORK_RULES')->where('RX_NETWORK_RULE_ID', $request->rx_network_rule_id)->delete();

                $flixible_list_obj = json_decode(json_encode($request->flexible_form, true));
    
                if (!empty($request->flexible_form)) {
                    $flixible_list = $flixible_list_obj[0];
    
                    foreach ($flixible_list_obj as $key => $flixible_list) {
    
                      
                        $Network_rules = DB::table('RX_NETWORK_RULES')->insert(
                            [
                                'RX_NETWORK_RULE_ID' => $request->rx_network_rule_id,
                                // 'RX_NETWORK_RULE_ID_NUMBER' => $flixible_list->rx_network_rule_id_number,
                                // 'PHARMACY_CHAIN' => $flixible_list->pharmacy_chain,
                                // 'STATE' => $flixible_list->state,
                                // 'COUNTY' => $flixible_list->county,
                                // 'ZIP_CODE' => $flixible_list->zip_code,
                                // 'AREA_CODE' => $flixible_list->area_code,
                                // 'EXCHANGE_CODE'=>$flixible_list->exchange_code,
                                'PRICE_SCHEDULE_OVRD' => $flixible_list->price_schedule_ovrd,
                                'EXCLUDE_RULE' => $flixible_list->exclude_rule,
                                // 'DATE_TIME_CREATED'=>$createddate,
                                // 'DATE_TIME_MODIFIED'=>$createddate,
                                // 'PHARMACY_STATUS' => $flixible_list->pharmacy_status,
                                // 'EFFECTIVE_DATE' => $flixible_list->effective_date,
                                // 'TERMINATION_DATE' =>$flixible_list->termination_date,
                            ]
                        );
    
                    }
    
                   
    
                }

                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData);

            $save_adit = $this->auditmethod('UP', json_encode($get_provider), 'PHARMACY_TABLE');

            $data = DB::table('RX_NETWORKS')->where('pharmacy_nabp', $request->pharmacy_nabp)->delete();


            $traditional_list_obj = json_decode(json_encode($request->traditional_form, true));

            if (!empty($request->traditional_form)) {
                $traditional_list = $traditional_list_obj[0];



                foreach ($traditional_list_obj as $key => $traditional_list) {



                    $update_rx_networks = DB::table('RX_NETWORKS')->insert(
                        [
                            'NETWORK_ID' => $traditional_list->network_id,
                            'PHARMACY_NABP' => $traditional_list->pharmacy_nabp,
                            'PRICE_SCHEDULE_OVRD' => $traditional_list->price_schedule_ovrd,
                            'PARTICIPATION_OVRD' => $traditional_list->participation_ovrd,
                            'DATE_TIME_CREATED' => $createddate,
                            'DATE_TIME_MODIFIED' => $createddate,
                            'EFFECTIVE_DATE' => $traditional_list->effective_date,
                            'TERMINATION_DATE' => $traditional_list->termination_date,

                        ]
                    );

                    $rx_networksnames = DB::table('RX_NETWORK_NAMES')
                        ->where('network_id', $traditional_list->network_id)
                        ->update(
                            [
                                // 'NETWORK_ID' => $traditional_list->network_id,
                                'NETWORK_NAME' => $traditional_list->network_name,
                            ]
                        );


                    // }

                    $get_parent = DB::table('RX_NETWORK_NAMES')
                        ->where(DB::raw('UPPER(network_id)'), strtoupper($traditional_list->network_id))
                        ->where(DB::raw('UPPER(pharmacy_nabp)'), strtoupper($traditional_list->pharmacy_nabp))
                        ->first();
                    $get_child = DB::table('RX_NETWORKS')
                        ->where(DB::raw('UPPER(network_id)'), strtoupper($traditional_list->network_id))
                        ->first();
                    $save_audit_parent = $this->auditMethod('IN', json_encode($get_parent), 'RX_NETWORK_NAMES');
                    $save_audit_child = $this->auditMethod('UP', json_encode($get_child), 'RX_NETWORKS');
                }
            }

            $data = DB::table('RX_NETWORK_RULES')->where('RX_NETWORK_RULE_ID', $request->rx_network_rule_id)->delete();

            $flixible_list_obj = json_decode(json_encode($request->flexible_form, true));

            if (!empty($request->flexible_form)) {
                $flixible_list = $flixible_list_obj[0];

                foreach ($flixible_list_obj as $key => $flixible_list) {


                    $Network_rules = DB::table('RX_NETWORK_RULES')->insert(
                        [
                            'RX_NETWORK_RULE_ID' => $request->rx_network_rule_id,
                            // 'RX_NETWORK_RULE_ID_NUMBER' => $flixible_list->rx_network_rule_id_number,
                            // 'PHARMACY_CHAIN' => $flixible_list->pharmacy_chain,
                            // 'STATE' => $flixible_list->state,
                            // 'COUNTY' => $flixible_list->county,
                            // 'ZIP_CODE' => $flixible_list->zip_code,
                            // 'AREA_CODE' => $flixible_list->area_code,
                            // 'EXCHANGE_CODE'=>$flixible_list->exchange_code,
                            'PRICE_SCHEDULE_OVRD' => $flixible_list->price_schedule_ovrd,
                            'EXCLUDE_RULE' => $flixible_list->exclude_rule,
                            // 'DATE_TIME_CREATED'=>$createddate,
                            // 'DATE_TIME_MODIFIED'=>$createddate,
                            // 'PHARMACY_STATUS' => $flixible_list->pharmacy_status,
                            // 'EFFECTIVE_DATE' => $flixible_list->effective_date,
                            // 'TERMINATION_DATE' =>$flixible_list->termination_date,
                        ]
                    );

                    $get_net_rules = DB::table('RX_NETWORK_RULES')
                        ->where(DB::raw('UPPER(rx_network_rule_id)'), strtoupper($request->rx_network_rule_id))
                        ->first();
                    $save_audit_net_rule = $this->auditMethod('IN', json_encode($get_net_rules), 'RX_NETWORK_RULES');
                }
            }

            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData);
        }
    }

    public function TraditionalIdsearch(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_NAMES')
            ->select('NETWORK_ID', 'NETWORK_NAME')
            ->where('NETWORK_ID', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function FlexibleIdsearch(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES')
            ->select('RX_NETWORK_RULE_ID', 'RX_NETWORK_RULE_NAME')
            ->where('RX_NETWORK_RULE_ID', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function search(Request $request)
    {
        $ndc = DB::table('PHARMACY_TABLE')
            ->where(DB::raw('UPPER(PHARMACY_NABP)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PHARMACY_NAME', 'like', '%' . $request->search . '%')
            // ->Where('PHARMACY_NABP', 'like', '%' .$request->search. '%')

            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getAll(Request $request)
    {
        $ndc = DB::table('PHARMACY_TABLE')->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getAllNew(Request $request)
    {
        $ndc = DB::table('PHARMACY_TABLE')->paginate(100);

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getProviderList($ndcid)
    {
        $ndclist = DB::table('PHARMACY_VALIDATIONS')
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
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
                        'NETWORK_ID' => $request->network_id,
                        'PHARMACY_NABP' => ($request->pharmacy_nabp),
                        'PRICE_SCHEDULE_OVRD' => $request->price_schedule_ovrd,
                        'PARTICIPATION_OVRD' => $request->participation_ovrd,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,


                    ]);
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $addData);
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
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData);
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
                        'RX_NETWORK_RULE_ID' => $request->rx_network_rule_id,
                        'PHARMACY_CHAIN' => $request->pharmacy_chain,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,


                    ]);
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $addData);
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
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData);
        }
    }

    public function getProviderNetworks(Request $request)
    {

        $traditional_network_data = DB::table('RX_NETWORKS')->get();

        return $this->respondWithToken($this->token(), '', $traditional_network_data);
    }


    public function getCombileNetworks(Request $request)
    {

        $traditional_network_data = DB::table('RX_NETWORKS')
            ->select('RX_NETWORKS.*', 'RX_NETWORK_NAMES.NETWORK_NAME')
            ->join('RX_NETWORK_NAMES', 'RX_NETWORK_NAMES.NETWORK_ID', '=', 'RX_NETWORKS.NETWORK_ID')

            ->where('RX_NETWORKS.pharmacy_nabp', $request->pharmacy_nabp)
            ->get();
        //    ->pluck('pharmacy_nabp');

        $flexible_network_data = DB::table('RX_NETWORK_RULES')->where('RX_NETWORK_RULE_ID', $request->id)->get();
        $merged = [
            'traditional_network_data' => $traditional_network_data,
            // 'flexible_network_data' => $flexible_network_data
        ];
        return $this->respondWithToken($this->token(), '', $merged);
    }

    public function providerdataDelete(Request $request)
    {
        if (isset($request->pharmacy_nabp)) {
            $pharmacy_nabp = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', $request->pharmacy_nabp)->delete();

            $Network_rules = DB::table('RX_NETWORKS')->where('pharmacy_nabp', $request->pharmacy_nabp)->delete();

            if ($pharmacy_nabp) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }       
            
    public function getProviderNetworksNew(Request $request)
    {

        $traditional_network_data = DB::table('RX_NETWORKS')->paginate(100);

        return $this->respondWithToken($this->token(), '', $traditional_network_data);
    }

}
