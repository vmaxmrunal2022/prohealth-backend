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



                if($recordCheck){
                    return $this->respondWithToken($this->token(), 'Record Already Exists', $recordCheck);
                

                }
                    $validator = Validator::make($request->all(), [
                        "rx_network_rule_id" => ['required', 'max:10', Rule::unique('RX_NETWORK_RULES')->where(function ($q) {
                            $q->whereNotNull('rx_network_rule_id');
                        })],
                        "rx_network_rule_name" => ['max:35'],
                        // "network_name" => ['required'],
                        "min_rx_qty" => ['max:6', 'numeric'],
                        "max_rx_qty" => ['max:6', 'numeric'],
                        "price_schedule_ovrd" => ['max:16'],
                        "min_rx_days" => ['max:16'],
                        "max_rx_days" => ['max:6'],
                        "gpi_exception_list_ovrd" => ['max:6'],
                        "ndc_exception_list_ovrd" => ['max:6'],
                        "max_retail_fills" => ['max:6'],
                        "max_fills_opt" => ['max:1'],
                        "default_comm_charge_paid" => ['max:3'],
                        "default_comm_charge_reject" => ['max:3'],
                        "starter_dose_days" => ['max:3'],
                        "starter_dose_bypass_days" => ['max:3'],
                        "starter_dose_maint_bypass_days" => ['max:3'],
                    ]);
                    if ($validator->fails()) {
                        return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), 'false');
                    } else {
                        $network_rule_names = DB::table('RX_NETWORK_RULE_NAMES')->insert(
                            [
                                'rx_network_rule_id' =>$request->rx_network_rule_id,
                                'rx_network_rule_name' => $request->rx_network_rule_name,
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
    
    
                       
                        $flexible_network_list = json_decode(json_encode($request->flexible_form, true));

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
                                        'EXCHANGE_CODE'=>$flexible_list->exchange_code,
                                        'PRICE_SCHEDULE_OVRD' => $flexible_list->price_schedule_ovrd,
                                        'EXCLUDE_RULE' => $flexible_list->exclude_rule,
                                        'DATE_TIME_CREATED'=>$createddate,
                                        'DATE_TIME_MODIFIED'=>$createddate,
                                        'PHARMACY_STATUS' => $flexible_list->pharmacy_status,
                                        'EFFECTIVE_DATE' => $flexible_list->effective_date,
                                        'TERMINATION_DATE' =>$flexible_list->termination_date,
                                    ]
                                );
                           
            
            
                            }
                        }
            
            
                        if ($Network_rules) {
                            return $this->respondWithToken($this->token(), 'Record Added Successfully', $Network_rules);
                        }
                
              
                }
            } else {
                $validator = Validator::make($request->all(), [
                    "rx_network_rule_id" => ['required', 'max:10'],
                    "rx_network_rule_name" => ['max:35'],
                    "min_rx_qty" => ['max:6', 'numeric'],
                    "max_rx_qty" => ['max:6', 'numeric'],
                    "price_schedule_ovrd" => ['max:16'],
                    "min_rx_days" => ['max:16'],
                    "max_rx_days" => ['max:6'],
                    "gpi_exception_list_ovrd" => ['max:6'],
                    "ndc_exception_list_ovrd" => ['max:6'],
                    "max_retail_fills" => ['max:6'],
                    "max_fills_opt" => ['max:1'],
                    "default_comm_charge_paid" => ['max:3'],
                    "default_comm_charge_reject" => ['max:3'],
                    "starter_dose_days" => ['max:3'],
                    "starter_dose_bypass_days" => ['max:3'],
                    "starter_dose_maint_bypass_days" => ['max:3'],
                ]);
                if ($validator->fails()) {
                    return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), 'false');
                } else if($request->add_new == 0) {

                    $network_rule_names = DB::table('RX_NETWORK_RULE_NAMES')
                        ->where('rx_network_rule_id',$request->rx_network_rule_id)
                        ->update(
                            [
                                'rx_network_rule_name' =>$request->rx_network_rule_name,
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
                            'EXCHANGE_CODE'=>$flixible_list->exchange_code,
                            'PRICE_SCHEDULE_OVRD' => $flixible_list->price_schedule_ovrd,
                            'EXCLUDE_RULE' => $flixible_list->exclude_rule,
                            'DATE_TIME_CREATED'=>$createddate,
                            'DATE_TIME_MODIFIED'=>$createddate,
                            'PHARMACY_STATUS' => $flixible_list->pharmacy_status,
                            'EFFECTIVE_DATE' => $flixible_list->effective_date,
                            'TERMINATION_DATE' =>$flixible_list->termination_date,
                        ]
                    );

                }

               

            }


            if ($Network_rules) {
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $Network_rules);
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



    public function search(Request $request)

    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES')
            ->where('RX_NETWORK_RULE_ID', 'like', '%' .$request->search. '%')
            ->orWhere('RX_NETWORK_RULE_NAME', 'like', '%' . $request->search. '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function flexibledropdown(Request $request)

    {
        $ndc = DB::table('RX_NETWORK_RULE_NAMES') ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }




    public function RuleIdsearch(Request $request)

    {
        $ndc = DB::table('RX_NETWORK_RULES')
            ->where('RX_NETWORK_RULE_ID_NUMBER', 'like', '%' . $request->search. '%')
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