<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperProviderNetworkController extends Controller
{

    public function dropDown(Request $request)
    {

        $ndclist =  DB::table('SUPER_RX_NETWORK_NAMES')
            ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->get();
        return $this->respondWithToken($this->token(), 'Data Fetched Succssfully', $ndclist);
    }

    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $recordcheck=DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id',strtoupper($request->super_rx_network_id))->first();



        if ( $request->add_new ==1 ) {

            if($recordcheck){
                return $this->respondWithToken($this->token(), 'This Record is  already exists in the system..!!!', $recordcheck);

            }

            else{

                $accum_benfit_stat_names = DB::table('SUPER_RX_NETWORK_NAMES')->insert(
                    [
                        'super_rx_network_id' => strtoupper( $request->super_rx_network_id ),
                        'super_rx_network_id_name'=>strtoupper( $request->super_rx_network_id_name ),
    
                    ]
                );
    
    
                $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS' )->insert(
                    [
                        'super_rx_network_id' => strtoupper( $request->super_rx_network_id),
                        'rx_network_id'=>$request->rx_network_id,
                        'effective_date'=>$request->effective_date,   
                        'comm_charge_paid'=>$request->comm_charge_paid,
                        'comm_charge_reject'=>$request->comm_charge_reject,
                        'days_supply_opt'=>$request->days_supply_opt,
                        'effective_date'=>$request->effective_date,
                        'max_fills_opt'=>$request->max_fills_opt,
                        'max_retail_fills'=>$request->max_retail_fills,
                        'max_rx_qty'=>$request->max_rx_qty,
                        'min_rx_qty'=>$request->min_rx_qty,
                        'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                        'rx_network_id'=>$request->rx_network_id,
                        'rx_network_type'=>$request->rx_network_type,
                        'starter_dose_bypass_days'=>$request->starter_dose_bypass_days,
                        'starter_dose_days'=>$request->starter_dose_days,
                        'starter_dose_maint_bypass_days'=>$request->starter_dose_maint_bypass_days,
                        'super_rx_network_priority'=>$request->super_rx_network_priority,
                        'termination_date'=>$request->termination_date,
                        'min_rx_days'=>$request->min_rx_days,
                        'max_rx_days'=>$request->max_rx_days,

                        
                        
                    ]
                );

            }

            if ($accum_benfit_stat) {
                return $this->respondWithToken($this->token(), 'Recored Added Successfully', $accum_benfit_stat);
            }


            $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS')->insert(
                [
                    'super_rx_network_id' => strtoupper($request->super_rx_network_id),
                    'rx_network_id' => $request->rx_network_id,
                    'effective_date' => $request->effective_date,
                ]
            );

            $benefitcode = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%' . $request->super_rx_network_id . '%')->first();


        } else if($request->add_new == 0) {


            $benefitcode = DB::table('SUPER_RX_NETWORK_NAMES')
                ->where('super_rx_network_id', $request->super_rx_network_id)


                ->update(
                    [
                        'super_rx_network_id' =>  strtoupper($request->super_rx_network_id),
                        'super_rx_network_id_name' => strtoupper($request->super_rx_network_id_name),

                    ]
                );

            $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS')

            ->where('super_rx_network_id', $request->super_rx_network_id )
            ->update(
                [
                    'rx_network_id'=>$request->rx_network_id,
                    'effective_date'=>$request->effective_date,   
                    'comm_charge_paid'=>$request->comm_charge_paid,
                    'comm_charge_reject'=>$request->comm_charge_reject,
                    'days_supply_opt'=>$request->days_supply_opt,
                    'effective_date'=>$request->effective_date,
                    'max_fills_opt'=>$request->max_fills_opt,
                    'max_retail_fills'=>$request->max_retail_fills,
                    'max_rx_qty'=>$request->max_rx_qty,
                    'min_rx_qty'=>$request->min_rx_qty,
                    'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                    'rx_network_id'=>$request->rx_network_id,
                    'rx_network_type'=>$request->rx_network_type,
                    'starter_dose_bypass_days'=>$request->starter_dose_bypass_days,
                    'starter_dose_days'=>$request->starter_dose_days,
                    'starter_dose_maint_bypass_days'=>$request->starter_dose_maint_bypass_days,
                    'super_rx_network_priority'=>$request->super_rx_network_priority,
                    'termination_date'=>$request->termination_date,
                    'min_rx_days'=>$request->min_rx_days,
                    'max_rx_days'=>$request->max_rx_days,
                   
                  

                ]
            );


            $benefitcode = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%'.$request->super_rx_network_id .'%')->first();
            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$benefitcode);


        }

    }
    public function search(Request $request)
    {
        $ndc = DB::table('SUPER_RX_NETWORK_NAMES')
            ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID')
            ->select('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_PRIORITY', 'SUPER_RX_NETWORKS.EFFECTIVE_DATE', 'SUPER_RX_NETWORKS.RX_NETWORK_TYPE', 'SUPER_RX_NETWORKS.PRICE_SCHEDULE_OVRD')
            ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();



        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function networkList($ndcid)
    {




        $ndclist =  DB::table('SUPER_RX_NETWORK_NAMES')
            ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')
            ->first();


        return $this->respondWithToken($this->token(), '', $ndclist);
    }



    public function getDetails($ndcid)
    {

        $ndc =  DB::table('SUPER_RX_NETWORK_NAMES')
            ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . strtoupper($ndcid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
