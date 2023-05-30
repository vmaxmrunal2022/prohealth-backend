<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class FlexibleNetworkController extends Controller
{
    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $recordCheck = DB::table('RX_NETWORK_RULES')->where('rx_network_rule_id', $request->rx_network_rule_id)->first();


        if ($request->add_new == 1) {

        // return $request->all();

            // if($recordCheck){
            //     return $this->respondWithToken($this->token(), 'Record Already Exists', $recordCheck);


            // }
            $validator = Validator::make($request->all(), [
                "rx_network_rule_id" => [
                    'required',
                    'max:10', Rule::unique('RX_NETWORK_RULE_NAMES')->where(function ($q) {
                        $q->whereNotNull('rx_network_rule_id');
                    })
                ],
                "rx_network_rule_name" => ['required','max:35'],
                // // "network_name" => ['required'],
                "min_rx_qty" => ['nullable'],
                "max_rx_qty" => ['nullable','gt:min_rx_qty'],
                // "price_schedule_ovrd" => ['max:16'],
                "min_rx_days" =>['nullable'],
                "max_rx_days" =>['nullable','gt:min_rx_days'],
                // "gpi_exception_list_ovrd" => ['max:6'],
                // "ndc_exception_list_ovrd" => ['max:6'],
                // "max_retail_fills" => ['max:6'],
                // "max_fills_opt" => ['max:1'],
                // "default_comm_charge_paid" => ['max:3'],
                // "default_comm_charge_reject" => ['max:3'],
                // "starter_dose_days" => ['max:3'],
                // "starter_dose_bypass_days" => ['max:3'],
                // "starter_dose_maint_bypass_days" => ['max:3'],
            ],[
                // 'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_rx_qty.gt' => 'Max Qty must be greater than Min Qty',
                'max_rx_days.gt' => 'Max Day must be greater than Min Day',
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), 'false');
            } else {
                $network_rule_names = DB::table('RX_NETWORK_RULE_NAMES')->insert(
                    [
                        'RX_NETWORK_RULE_ID' => $request->rx_network_rule_id,
                        'RX_NETWORK_RULE_NAME' => $request->rx_network_rule_name,
                        'DEFAULT_PRICE_SCHEDULE_OVRD' => $request->default_price_schedule_ovrd,
                        'DEFAULT_BILLING_TYPE' => $request->default_billing_type,
                        'DEFAULT_CAP_AMOUNT' => $request->default_cap_amount,
                        'DEFAULT_COMM_CHARGE_PAID' => $request->default_comm_charge_paid,
                        'DEFAULT_COMM_CHARGE_REJECT' => $request->default_comm_charge_reject,
                        'GPI_EXCEPTION_LIST_OVRD' => $request->gpi_exception_list_ovrd,
                        'NDC_EXCEPTION_LIST_OVRD' => $request->ndc_exception_list_ovrd,
                        'HIGHEST_RULE_ID_NUMBER' => $request->highest_rule_id_number,
                        'WITHHOLD_PAID_AMT' => $request->withhold_paid_amt,
                        'WITHHOLD_PAID_PERCENT' => $request->withhold_paid_percent,
                        'WITHHOLD_U_AND_C_FLAG' => $request->withhold_u_and_c_flag,
                        'WITHHOLD_ACTIVE_FLAG' => $request->withhold_active_flag,
                        'MIN_RX_QTY' => $request->min_rx_qty,
                        'MAX_RX_QTY' => $request->max_rx_qty,
                        'MIN_RX_DAYS' => $request->min_rx_days,
                        'MAX_RX_DAYS' => $request->max_rx_days,
                        'MAX_REFILLS' => $request->max_refills,
                        'MAINT_DRUG_LIST_OPT' => $request->maint_drug_list_opt,
                        'MAINT_DRUG_LIST' => $request->maint_drug_list,
                        'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                        'MAX_FILLS_OPT' => $request->max_fills_opt,
                        'MAX_RETAIL_FILLS' => $request->max_retail_fills,
                        'MAINT_COPAY_SCHED' => $request->maint_copay_sched,
                        'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                        'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                        'PRICING_OVRD_LIST_ID' => $request->pricing_ovrd_list_id,
                        'MAINT_MIN_RX_QTY' => $request->maint_min_rx_qty,
                        'MAINT_MAX_RX_QTY' => $request->maint_max_rx_qty,
                        'MAINT_MIN_RX_DAYS' => $request->maint_min_rx_days,
                        'MAINT_MAX_RX_DAYS' => $request->maint_max_rx_days,
                        'MAINT_QTY_DSUP_COMPARE_RULE' => $request->maint_qty_dsup_compare_rule
                    ]
                );



                $flexible_network_list = json_decode(json_encode($request->flexible_form, true));

                // dd($flexible_network_list);

                if (!empty($request->flexible_form)) {
                    $flexible_list = $flexible_network_list[0];


                    foreach ($flexible_network_list as $key => $flexible_list) {

                        $Network_rules = DB::table('RX_NETWORK_RULES')->insert(
                            [
                                'RX_NETWORK_RULE_ID' => $request->rx_network_rule_id,
                                'RX_NETWORK_RULE_ID_NUMBER' => $flexible_list->rx_network_rule_id_number,
                                'PHARMACY_CHAIN' => $flexible_list->pharmacy_chain,
                                'STATE' => $flexible_list->state,
                                'COUNTY' => $flexible_list->county,
                                'ZIP_CODE' => $flexible_list->zip_code,
                                'AREA_CODE' => $flexible_list->area_code,
                                'EXCHANGE_CODE' => $flexible_list->exchange_code,
                                'PRICE_SCHEDULE_OVRD' => $flexible_list->price_schedule_ovrd,
                                'EXCLUDE_RULE' => $flexible_list->exclude_rule,
                                'DATE_TIME_CREATED' => $createddate,
                                'DATE_TIME_MODIFIED' => $createddate,
                                'PHARMACY_STATUS' => $flexible_list->pharmacy_status,
                                'EFFECTIVE_DATE' => $flexible_list->effective_date,
                                'TERMINATION_DATE' => $flexible_list->termination_date,
                            ]
                        );



                    }
                }


                if ($network_rule_names) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $network_rule_names);
                }


            }
        } elseif ($request->add_new == 0) {
            $validator = Validator::make($request->all(), [
                "rx_network_rule_id" => [
                    'required',
                    'max:10', Rule::unique('RX_NETWORK_RULE_NAMES')->where(function ($q) use($request) {
                        $q->whereNotNull('rx_network_rule_id');
                        $q->where('rx_network_rule_id','!=', $request->rx_network_rule_id);
                    })
                ],
                "rx_network_rule_name" => ['required','max:35'],
                "min_rx_qty" => ['nullable', ],
                "max_rx_qty" => ['nullable', 'gt:min_rx_qty'],
                // "price_schedule_ovrd" => ['max:16'],
                "min_rx_days" => ['nullable', ],
                "max_rx_days" =>['nullable', 'gt:min_rx_days'],
                // "gpi_exception_list_ovrd" => ['max:6'],
                // "ndc_exception_list_ovrd" => ['max:6'],
                // "max_retail_fills" => ['max:6'],
                // "max_fills_opt" => ['max:1'],
                // "default_comm_charge_paid" => ['max:3'],
                // "default_comm_charge_reject" => ['max:3'],
                // "starter_dose_days" => ['max:3'],
                // "starter_dose_bypass_days" => ['max:3'],
                // "starter_dose_maint_bypass_days" => ['max:3'],
            ],[
                // 'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_rx_qty.gt' => 'Max Qty must be greater than Min Qty',
                'max_rx_days.gt' => 'Max Day must be greater than Min Day',
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), 'false');
            } else if ($request->add_new == 0) {

                $network_rule_names = DB::table('RX_NETWORK_RULE_NAMES')
                    ->where('rx_network_rule_id', $request->rx_network_rule_id)
                    ->update(
                        [
                            'RX_NETWORK_RULE_NAME' => $request->rx_network_rule_name,
                            'DEFAULT_PRICE_SCHEDULE_OVRD' => $request->default_price_schedule_ovrd,
                            'DEFAULT_BILLING_TYPE' => $request->default_billing_type,
                            'DEFAULT_CAP_AMOUNT' => $request->default_cap_amount,
                            'DEFAULT_COMM_CHARGE_PAID' => $request->default_comm_charge_paid,
                            'DEFAULT_COMM_CHARGE_REJECT' => $request->default_comm_charge_reject,
                            'GPI_EXCEPTION_LIST_OVRD' => $request->gpi_exception_list_ovrd,
                            'NDC_EXCEPTION_LIST_OVRD' => $request->ndc_exception_list_ovrd,
                            'HIGHEST_RULE_ID_NUMBER' => $request->highest_rule_id_number,
                            'WITHHOLD_PAID_AMT' => $request->withhold_paid_amt,
                            'WITHHOLD_PAID_PERCENT' => $request->withhold_paid_percent,
                            'WITHHOLD_U_AND_C_FLAG' => $request->withhold_u_and_c_flag,
                            'WITHHOLD_ACTIVE_FLAG' => $request->withhold_active_flag,
                            'MIN_RX_QTY' => $request->min_rx_qty,
                            'MAX_RX_QTY' => $request->max_rx_qty,
                            'MIN_RX_DAYS' => $request->min_rx_days,
                            'MAX_RX_DAYS' => $request->max_rx_days,
                            'MAX_REFILLS' => $request->max_refills,
                            'MAINT_DRUG_LIST_OPT' => $request->maint_drug_list_opt,
                            'MAINT_DRUG_LIST' => $request->maint_drug_list,
                            'QTY_DSUP_COMPARE_RULE' => $request->qty_dsup_compare_rule,
                            'MAX_FILLS_OPT' => $request->max_fills_opt,
                            'MAX_RETAIL_FILLS' => $request->max_retail_fills,
                            'MAINT_COPAY_SCHED' => $request->maint_copay_sched,
                            'STARTER_DOSE_DAYS' => $request->starter_dose_days,
                            'STARTER_DOSE_BYPASS_DAYS' => $request->starter_dose_bypass_days,
                            'STARTER_DOSE_MAINT_BYPASS_DAYS' => $request->starter_dose_maint_bypass_days,
                            'PRICING_OVRD_LIST_ID' => $request->pricing_ovrd_list_id,
                            'MAINT_MIN_RX_QTY' => $request->maint_min_rx_qty,
                            'MAINT_MAX_RX_QTY' => $request->maint_max_rx_qty,
                            'MAINT_MIN_RX_DAYS' => $request->maint_min_rx_days,
                            'MAINT_MAX_RX_DAYS' => $request->maint_max_rx_days,
                            'MAINT_QTY_DSUP_COMPARE_RULE' => $request->maint_qty_dsup_compare_rule
                        ]
                    );
                $data = DB::table('RX_NETWORK_RULES')->where('RX_NETWORK_RULE_ID', $request->rx_network_rule_id)->delete();

                $flixible_list_obj = json_decode(json_encode($request->flexible_form, true));

                if (!empty($request->flexible_form)) {
                    $flixible_list = $flixible_list_obj[0];

                    foreach ($flixible_list_obj as $key => $flixible_list) {


                        $Network_rules = DB::table('RX_NETWORK_RULES')->insert(
                            [
                                'RX_NETWORK_RULE_ID' => $request->rx_network_rule_id,
                                'RX_NETWORK_RULE_ID_NUMBER' => $flixible_list->rx_network_rule_id_number,
                                'PHARMACY_CHAIN' => $flixible_list->pharmacy_chain,
                                'STATE' => $flixible_list->state,
                                'COUNTY' => $flixible_list->county,
                                'ZIP_CODE' => $flixible_list->zip_code,
                                'AREA_CODE' => $flixible_list->area_code,
                                'EXCHANGE_CODE' => $flixible_list->exchange_code,
                                'PRICE_SCHEDULE_OVRD' => $flixible_list->price_schedule_ovrd,
                                'EXCLUDE_RULE' => $flixible_list->exclude_rule,
                                'DATE_TIME_CREATED' => $createddate,
                                'DATE_TIME_MODIFIED' => $createddate,
                                'PHARMACY_STATUS' => $flixible_list->pharmacy_status,
                                'EFFECTIVE_DATE' => $flixible_list->effective_date,
                                'TERMINATION_DATE' => $flixible_list->termination_date,
                            ]
                        );

                    }



                }


                if ($network_rule_names) {
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $network_rule_names);
                }



            }
        }
    }


    public function all(Request $request)
    {

        $ndc = DB::table('RX_NETWORK_RULE_NAMES')
            ->join('RX_NETWORK_RULES', 'RX_NETWORK_RULE_NAMES.RX_NETWORK_RULE_ID', '=', 'RX_NETWORK_RULE_NAMES.RX_NETWORK_RULE_ID')
            ->where('RX_NETWORK_RULES.PHARMACY_CHAIN', $request->pharmacy_chain)->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }




    public function getDetails($ndcid)
    {
        $ndc = DB::table('RX_NETWORK_RULES')
            ->join('RX_NETWORK_RULE_NAMES', 'RX_NETWORK_RULE_NAMES.RX_NETWORK_RULE_ID', '=', 'RX_NETWORK_RULES.RX_NETWORK_RULE_ID')
            // ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'RX_NETWORKS.PHARMACY_NABP')


            ->where('RX_NETWORK_RULES.RX_NETWORK_RULE_ID', $ndcid)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function NdcExceptionNames(Request $request)
    {

        $ndcnames = DB::table('NDC_EXCEPTIONS')->get();

        return $this->respondWithToken($this->token(), '', $ndcnames);

    }

    public function GpiExceptionNames(Request $request)
    {

        $gpinames = DB::table('GPI_EXCEPTIONS')->get();

        return $this->respondWithToken($this->token(), '', $gpinames);

    }



    public function search(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES')
            ->where('RX_NETWORK_RULE_ID', 'like', '%' . $request->search . '%')
            ->orWhere('RX_NETWORK_RULE_NAME', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getflixibleNetworks(Request $request)
    {


        $form_data = DB::table('RX_NETWORK_RULE_NAMES')
            ->where('RX_NETWORK_RULE_ID', $request->rx_network_rule_id)
            ->first();

        $flexible_network_data = DB::table('RX_NETWORK_RULES')
            ->where('RX_NETWORK_RULE_ID', $request->rx_network_rule_id)
            ->get();


        $merged = [
            'form_data' => $form_data,
            'flexible_network_data' => $flexible_network_data
        ];
        return $this->respondWithToken($this->token(), '', $merged);
    }



    public function flexibledropdown(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES')->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }




    public function RuleIdsearch(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_RULES')
            ->where('RX_NETWORK_RULE_ID_NUMBER', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getList($ndcid)
    {
        $ndc = DB::table('RX_NETWORK_RULES')
            ->Where('RX_NETWORK_RULE_ID', $ndcid)
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}