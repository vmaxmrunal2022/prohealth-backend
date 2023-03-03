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



        if ($request->add_new) {
            $validator = Validator::make($request->all(), [
                "rx_network_rule_id" => ['required', 'max:10', Rule::unique('RX_NETWORK_RULES')->where(function ($q) {
                    $q->whereNotNull('rx_network_rule_id');
                })],
                "rx_network_rule_name" => ['max:35'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), 'false');
            } else {
                $accum_benfit_stat_names = DB::table('RX_NETWORK_RULE_NAMES')->insert(
                    [
                        'rx_network_rule_id' => strtoupper($request->rx_network_rule_id),
                        'rx_network_rule_name' => strtoupper($request->rx_network_rule_name),
                        'gpi_exception_list_ovrd' => $request->gpi_exception_list_ovrd,
                        'ndc_exception_list_ovrd' => $request->ndc_exception_list_ovrd,
                        'default_comm_charge_paid' => $request->default_comm_charge_paid,
                        'default_comm_charge_reject' => $request->default_comm_charge_reject,
                        'min_rx_qty' => $request->min_rx_qty,
                        'max_rx_qty' => $request->max_rx_qty,
                        'min_rx_days' => $request->min_rx_days,
                        'max_rx_days' => $request->max_rx_days,
                        'max_retail_fills' => $request->max_retail_fills,
                        'max_fills_opt' => $request->max_fills_opt,
                        'starter_dose_days' => $request->starter_dose_days,
                        'starter_dose_bypass_days' => $request->starter_dose_bypass_days,
                        'starter_dose_maint_bypass_days' => $request->starter_dose_maint_bypass_days,
                        'pricing_ovrd_list_id' => $request->pricing_ovrd_list_id,
                    ]
                );


                $accum_benfit_stat = DB::table('RX_NETWORK_RULES')->insert(
                    [
                        'rx_network_rule_id' => strtoupper($request->rx_network_rule_id),
                        'rx_network_rule_id_number' => $request->rx_network_rule_id_number,
                        'pharmacy_chain' => $request->pharmacy_chain,
                        'state' => $request->state,
                        'county' => $request->county,
                        'zip_code' => $request->zip_code,
                        'area_code' => $request->area_code,
                        'price_schedule_ovrd' => $request->price_schedule_ovrd,
                        'exclude_rule' => $request->exclude_rule,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'exchange_code' => $request->exchange_code,
                        'pharmacy_status' => $request->pharmacy_status,
                    ]
                );
                $benefitcode = DB::table('RX_NETWORK_RULES')
                    ->where(DB::raw('UPPER(rx_network_rule_id)'), strtoupper($request->rx_network_rule_id))
                    ->first();
                return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "rx_network_rule_id" => ['required', 'max:10'],
                "rx_network_rule_name" => ['max:35'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), 'false');
            } else {

                $benefitcode = DB::table('RX_NETWORK_RULE_NAMES')
                    ->where(DB::raw('UPPER(rx_network_rule_id)'), strtoupper($request->rx_network_rule_id))
                    ->update(
                        [
                            'rx_network_rule_name' => strtoupper($request->rx_network_rule_name),
                            'gpi_exception_list_ovrd' => $request->gpi_exception_list_ovrd,
                            'ndc_exception_list_ovrd' => $request->ndc_exception_list_ovrd,
                            'default_comm_charge_paid' => $request->default_comm_charge_paid,
                            'default_comm_charge_reject' => $request->default_comm_charge_reject,
                            'min_rx_qty' => $request->min_rx_qty,
                            'max_rx_qty' => $request->max_rx_qty,
                            'min_rx_days' => $request->min_rx_days,
                            'max_rx_days' => $request->max_rx_days,
                            'max_retail_fills' => $request->max_retail_fills,
                            'max_fills_opt' => $request->max_fills_opt,
                            'starter_dose_days' => $request->starter_dose_days,
                            'starter_dose_bypass_days' => $request->starter_dose_bypass_days,
                            'starter_dose_maint_bypass_days' => $request->starter_dose_maint_bypass_days,
                            'pricing_ovrd_list_id' => $request->pricing_ovrd_list_id,
                        ]
                    );

                $accum_benfit_stat = DB::table('RX_NETWORK_RULES')
                    ->where(DB::raw('UPPER(rx_network_rule_id)'), strtoupper($request->rx_network_rule_id))
                    ->update(
                        [
                            'rx_network_rule_id' => strtoupper($request->rx_network_rule_id),
                            'rx_network_rule_id_number' => $request->rx_network_rule_id_number,
                            'pharmacy_chain' => $request->pharmacy_chain,
                            'state' => $request->state,
                            'county' => $request->county,
                            'zip_code' => $request->zip_code,
                            'area_code' => $request->area_code,
                            'price_schedule_ovrd' => $request->price_schedule_ovrd,
                            'exclude_rule' => $request->exclude_rule,
                            'exchange_code' => $request->exchange_code,
                            'pharmacy_status' => $request->pharmacy_status,
                        ]
                    );
                // $benefitcode = DB::table('RX_NETWORK_RULES')->where('rx_network_rule_id', 'like', $request->rx_network_rule_id)->first();
                $benefitcode = DB::table('RX_NETWORK_RULES')
                    ->where(DB::raw('UPPER(rx_network_rule_id)'), strtoupper($request->rx_network_rule_id))
                    ->first();
                return $this->respondWithToken($this->token(), 'Updated Successfully!!!', $benefitcode);
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



    public function search(Request $request)

    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES')
            ->where('RX_NETWORK_RULE_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('RX_NETWORK_RULE_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function RuleIdsearch(Request $request)

    {
        $ndc = DB::table('RX_NETWORK_RULES')
            ->where('RX_NETWORK_RULE_ID_NUMBER', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getList($ndcid)
    {
        $ndc = DB::table('RX_NETWORK_RULES')
            ->Where('RX_NETWORK_RULE_ID', 'like', '%' . $ndcid . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
