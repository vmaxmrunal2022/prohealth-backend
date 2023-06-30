<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Traits\AuditTrait;

class TraditionalNetworkController extends Controller
{
    use AuditTrait;


    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $recordcheck=DB::table('RX_NETWORK_NAMES')->where('NETWORK_ID',$request->network_id)->first();




        if ($request->add_new) {

            $validator = Validator::make($request->all(), [
                'network_id' => ['required', 'max:10',
                 Rule::unique('RX_NETWORK_NAMES')->where(function ($q) {
                    $q->whereNotNull('NETWORK_ID');
                })], 
                "network_name" => ['required', 'max:35'],
                "min_rx_qty" => ['nullable'],
                "max_rx_qty" => ['nullable','gt:min_rx_qty'],
                "min_rx_days" => ['nullable'],
                "max_rx_days" => ['nullable','gt:min_rx_days'],
                ],[
                    'max_rx_qty.gt' => 'Max Qty must be greater than Min Qty',
                    'max_rx_days.gt' => 'Max Day must be greater than Min Day',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } 

            // if($recordcheck){
            //     return $this->respondWithToken($this->token(), 'Network ID Already Exists', $recordcheck);
            // }

            else{

                $rx_networknames = DB::table('RX_NETWORK_NAMES')->insert(
                    [
                        'NETWORK_ID' => $request->network_id,
                        'NETWORK_NAME' => $request->network_name,
                        'DATE_TIME_CREATED' => $createddate,
                        'DEFAULT_PRICE_SCHEDULE_OVRD' => $request->default_price_schedule_ovrd,
                        'DEFAULT_BILLING_TYPE' => $request->default_billing_type,
                        'DEFAULT_CAP_AMOUNT' => $request->default_cap_amount,
                        'DEFAULT_COMM_CHARGE_PAID' => $request->default_comm_charge_paid,
                        'DEFAULT_COMM_CHARGE_REJECT' => $request->default_comm_charge_reject,
                        'GPI_EXCEPTION_LIST_OVRD' => $request->gpi_exception_list_ovrd,
                        'NDC_EXCEPTION_LIST_OVRD' => $request->ndc_exception_list_ovrd,
                        'WITHHOLD_PAID_AMT' => $request->withhold_paid_amt,
                        'WITHHOLD_PAID_PERCENT' => $request->withhold_paid_percent,
                        'WITHHOLD_U_AND_C_FLAG' => $request->withhold_u_and_c_flag,
                        'WITHHOLD_ACTIVE_FLAG' => $request->withhold_active_flag,
                        'MIN_RX_QTY' => $request->min_rx_qty,
                        'MAX_RX_QTY' => $request->max_rx_qty,
                        'MIN_RX_DAYS'=>$request->min_rx_days,
                        'MAX_RX_DAYS'=>$request->max_rx_days,
                        'MAX_REFILLS'=>$request->max_refills,
                        'MAINT_DRUG_LIST_OPT'=>$request->maint_drug_list_opt,
                        'MAINT_DRUG_LIST'=>$request->maint_drug_list,
                        'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                        'MAX_FILLS_OPT'=>$request->max_fills_opt,
                        'MAX_RETAIL_FILLS'=>$request->max_retail_fills,
                        'MAINT_COPAY_SCHED'=>$request->maint_copay_sched,
                        'MAINT_PRICE_SCHED'=>$request->maint_price_sched,
                        'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                        'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                        'PRICING_OVRD_LIST_ID'=>$request->pricing_ovrd_list_id,
                        'MAINT_MIN_RX_QTY'=>$request->maint_min_rx_qty,
                        'MAINT_MAX_RX_QTY'=>$request->maint_max_rx_qty,
                        'MAINT_MIN_RX_DAYS'=>$request->maint_min_rx_days,
                        'MAINT_MAX_RX_DAYS'=>$request->maint_max_rx_days,
                        'MAINT_QTY_DSUP_COMPARE_RULE'=>$request->maint_qty_dsup_compare_rule,
                        
    
    
                    ]);
                $record = DB::table('RX_NETWORK_NAMES')->where('network_id', $request->network_id)->first();
                if($record){
                    $record_snapshot = json_encode($record);
                    $save_audit = $this->auditMethod('IN', $record_snapshot, 'RX_NETWORK_NAMES');
                }

                $traditional_list_obj = json_decode(json_encode($request->traditional_form, true));

                if (!empty($request->traditional_form)) {
                    $traditional_list = $traditional_list_obj[0];
    
                    foreach ($traditional_list_obj as $key => $traditional_list) {
                      $rx_networks = DB::table('RX_NETWORKS')->insert(
                        [
                            'NETWORK_ID' => $request->network_id,
                            'PHARMACY_NABP' => $traditional_list->pharmacy_nabp,
                            'PRICE_SCHEDULE_OVRD' => $traditional_list->price_schedule_ovrd,
                            'PARTICIPATION_OVRD' => $traditional_list->participation_ovrd,
                            'DATE_TIME_CREATED' => $createddate,
                            'DATE_TIME_MODIFIED' => $createddate,
                            'EFFECTIVE_DATE' => $traditional_list->effective_date,
                            'TERMINATION_DATE' => $traditional_list->termination_date,
        
                        ]);
                    }

                    $child_recs = DB::table('RX_NETWORKS')->where( 'NETWORK_ID', $request->network_id)->get();
                    if($child_recs){
                         foreach($child_recs as $rec){
                            $record_snapshot = json_encode($rec);
                            $save_audit = $this->auditMethod('IN', $record_snapshot, 'RX_NETWORKS');
                         }
                    }
                    
                }
    
                if ($rx_networknames) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $rx_networknames);
                }

            }

        }
         else {
            $validator = Validator::make($request->all(), [
                'network_id' => ['required', 'max:10',
                 Rule::unique('RX_NETWORK_NAMES')->where(function ($q) use($request) {
                    $q->whereNotNull('NETWORK_ID');
                    $q->where('NETWORK_ID','!=',$request->network_id);
                })], 
                "network_name" => ['required', 'max:35'],
                "min_rx_qty" => ['nullable'],
                "max_rx_qty" => ['nullable','gt:min_rx_qty'],
                "min_rx_days" => ['nullable'],
                "max_rx_days" => ['nullable','gt:min_rx_days'],
                ],[
                    'max_rx_qty.gt' => 'Max Qty must be greater than Min Qty',
                    'max_rx_days.gt' => 'Max Day must be greater than Min Day',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } 
            $benefitcode = DB::table('RX_NETWORK_NAMES')
                ->where('network_id', $request->network_id)
                ->update(
                    [
                    'NETWORK_NAME' => $request->network_name,
                    'DATE_TIME_CREATED' => $createddate,
                    'DEFAULT_PRICE_SCHEDULE_OVRD' => $request->default_price_schedule_ovrd,
                    'DEFAULT_BILLING_TYPE' => $request->default_billing_type,
                    'DEFAULT_CAP_AMOUNT' => $request->default_cap_amount,
                    'DEFAULT_COMM_CHARGE_PAID' => $request->default_comm_charge_paid,
                    'DEFAULT_COMM_CHARGE_REJECT' => $request->default_comm_charge_reject,
                    'GPI_EXCEPTION_LIST_OVRD' => $request->gpi_exception_list_ovrd,
                    'NDC_EXCEPTION_LIST_OVRD' => $request->ndc_exception_list_ovrd,
                    'WITHHOLD_PAID_AMT' => $request->withhold_paid_amt,
                    'WITHHOLD_PAID_PERCENT' => $request->withhold_paid_percent,
                    'WITHHOLD_U_AND_C_FLAG' => $request->withhold_u_and_c_flag,
                    'WITHHOLD_ACTIVE_FLAG' => $request->withhold_active_flag,
                    'MIN_RX_QTY' => $request->min_rx_qty,
                    'MAX_RX_QTY' => $request->max_rx_qty,
                    'MIN_RX_DAYS'=>$request->min_rx_days,
                    'MAX_RX_DAYS'=>$request->max_rx_days,
                    'MAX_REFILLS'=>$request->max_refills,
                    'MAINT_DRUG_LIST_OPT'=>$request->maint_drug_list_opt,
                    'MAINT_DRUG_LIST'=>$request->maint_drug_list,
                    'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                    'MAX_FILLS_OPT'=>$request->max_fills_opt,
                    'MAX_RETAIL_FILLS'=>$request->max_retail_fills,
                    'MAINT_COPAY_SCHED'=>$request->maint_copay_sched,
                    'MAINT_PRICE_SCHED'=>$request->maint_price_sched,
                    'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                    'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                    'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                    'PRICING_OVRD_LIST_ID'=>$request->pricing_ovrd_list_id,
                    'MAINT_MIN_RX_QTY'=>$request->maint_min_rx_qty,
                    'MAINT_MAX_RX_QTY'=>$request->maint_max_rx_qty,
                    'MAINT_MIN_RX_DAYS'=>$request->maint_min_rx_days,
                    'MAINT_MAX_RX_DAYS'=>$request->maint_max_rx_days,
                    'MAINT_QTY_DSUP_COMPARE_RULE'=>$request->maint_qty_dsup_compare_rule,
                    ]
                );

            $record = DB::table('RX_NETWORK_NAMES')->where('network_id', $request->network_id)->first();
            if($record){
                $record_snapshot = json_encode($record);
                $save_audit = $this->auditMethod('UP', $record_snapshot, 'RX_NETWORK_NAMES');
            }


            $data = DB::table('RX_NETWORKS')->where('NETWORK_ID', $request->network_id)->delete();


            $traditional_list_obj = json_decode(json_encode($request->traditional_form, true));

            if (!empty($request->traditional_form)) {
                $traditional_list = $traditional_list_obj[0];

                foreach ($traditional_list_obj as $key => $traditional_list) {

                    $update_rx_networks = DB::table('RX_NETWORKS')->insert(
                        [
                            'NETWORK_ID' => $request->network_id,
                            'PHARMACY_NABP' => $traditional_list->pharmacy_nabp,
                            'PRICE_SCHEDULE_OVRD' => $traditional_list->price_schedule_ovrd,
                            'PARTICIPATION_OVRD' => $traditional_list->participation_ovrd,
                            'DATE_TIME_CREATED' => $createddate,
                            'DATE_TIME_MODIFIED' => $createddate,
                            'EFFECTIVE_DATE' => $traditional_list->effective_date,
                            'TERMINATION_DATE' => $traditional_list->termination_date,
        
                        ]
                    );
                }
                $child_recs = DB::table('RX_NETWORKS')->where( 'NETWORK_ID', $request->network_id)->get();
                if($child_recs){
                    foreach($child_recs as $rec){
                        $record_snapshot = json_encode($rec);
                        $save_audit = $this->auditMethod('UP', $record_snapshot, 'RX_NETWORKS');
                    }
                }

            }
            if ($benefitcode) {
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
            }


            
        }
    
    }


    public function dropdown (Request $request){

        $data=DB::table('RX_NETWORK_NAMES')
        ->select('NETWORK_ID','NETWORK_NAME')
        ->get();

        return $this->respondWithToken($this->token(), 'Data fetched successfully', $data);


    }
    public function dropdownNew (Request $request){
        $searchQuery = $request->search;
        $data=  DB::table('RX_NETWORK_NAMES')
                    ->select('NETWORK_ID','NETWORK_NAME')
                    ->when($searchQuery, function ($query) use ($searchQuery) {
                        $query->where(DB::raw('UPPER(NETWORK_ID)'), 'like', '%' . strtoupper($searchQuery) . '%');
                        $query->orWhere(DB::raw('UPPER(NETWORK_NAME)'), 'like', '%' . strtoupper($searchQuery) . '%');
                    })
                    ->paginate(100);

        return $this->respondWithToken($this->token(), 'Data fetched successfully', $data);

    }


    public function all(Request $request)
    {

        if ($request->pharmacy_nabp) {

            $ndc = DB::table('RX_NETWORK_NAMES')
                ->join('RX_NETWORKS', 'RX_NETWORK_NAMES.NETWORK_ID', '=', 'RX_NETWORK_NAMES.NETWORK_ID')
                ->where('RX_NETWORKS.PHARMACY_NABP', $request->pharmacy_nabp)->get();

            if ($ndc) {
                return $this->respondWithToken($this->token(), '', $ndc);
            }
        } else {

            return $this->respondWithToken($this->token(), 'No Data Found');
        }
    }




    public function search(Request $request)
    {
        $ndc =  DB::table('RX_NETWORK_NAMES')
        // ->where('NETWORK_ID', 'like', '%' . $request->search . '%')
        ->where(DB::raw('UPPER(RX_NETWORK_NAMES.NETWORK_ID)'), 'like', '%' . strtoupper($request->search) . '%')
        ->orWhere('NETWORK_NAME', 'like', '%' . $request->search . '%')
        ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function TraditionalNetworkIdsDropdwon(Request $request)
    {
        $ndc = DB::table('RX_NETWORK_NAMES')
            ->select('NETWORK_ID', 'NETWORK_NAME')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
    public function TraditionalNetworkIdsDropdwonNew(Request $request)
    {
        $searchQuery = $request->search;
        $ndc = DB::table('RX_NETWORK_NAMES')
            ->select('NETWORK_ID', 'NETWORK_NAME')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(DB::raw('UPPER(NETWORK_ID)'), 'like', '%' . strtoupper($searchQuery) . '%');
                $query->orWhere(DB::raw('UPPER(NETWORK_NAME)'), 'like', '%' . strtoupper($searchQuery) . '%');
             })
            ->paginate(100);

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    
    public function ProviderIdsearch(Request $request)
    {
        $priceShedule = DB::table('PRICE_SCHEDULE')
            ->where('PRICE_SCHEDULE', 'like', '%' . $request->search . '%')
            ->orWhere('PRICE_SCHEDULE_NAME', 'like', '%' . $request->search . '%')
            ->orWhere('COPAY_SCHEDULE', 'like', '%' . $request->search . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $priceShedule);
    }




    public function getList($ndcid)
    {
        $ndc = DB::table('RX_NETWORKS')
            ->join('RX_NETWORK_NAMES', 'RX_NETWORKS.NETWORK_ID', '=', 'RX_NETWORK_NAMES.NETWORK_ID')
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'RX_NETWORKS.PHARMACY_NABP')
            // ->where('RX_NETWORK_NAMES.NETWORK_NAME', 'like', '%' . $ndcid . '%')
            ->orWhere('RX_NETWORKS.NETWORK_ID', $ndcid)
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('RX_NETWORKS')
            ->join('RX_NETWORK_NAMES', 'RX_NETWORK_NAMES.NETWORK_ID', '=', 'RX_NETWORKS.NETWORK_ID')
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'RX_NETWORKS.PHARMACY_NABP')
            ->where('PHARMACY_TABLE.PHARMACY_NABP', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function traditionalNetworkDelete(Request $request){
        if(isset($request->network_id) ) {

            $record = DB::table('RX_NETWORK_NAMES')->where('network_id', $request->network_id)->first();
            if($record){
                $record_snapshot = json_encode($record);
                $save_audit = $this->auditMethod('DE', $record_snapshot, 'RX_NETWORK_NAMES');
            } 

            $network_id = DB::table('RX_NETWORK_NAMES')->where('network_id', $request->network_id)->delete();


            $child_recs = DB::table('RX_NETWORKS')->where( 'NETWORK_ID', $request->network_id)->get();
            if($child_recs){
                foreach($child_recs as $rec){
                    $record_snapshot = json_encode($rec);
                    $save_audit = $this->auditMethod('DE', $record_snapshot, 'RX_NETWORKS');
                }
            }

            $Rx_networks = DB::table('RX_NETWORKS')->where('NETWORK_ID', $request->network_id)->delete();

            if ($network_id) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}