<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraditionalNetworkController extends Controller
{



    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->add_new) {

            $accum_benfit_stat_names = DB::table('RX_NETWORK_NAMES')->insert(
                [
                    'NETWORK_ID' => strtoupper( $request->network_id ),
                    'NETWORK_NAME' => $request->network_name,
                    'DATE_TIME_CREATED'=>$createddate,
                    'DEFAULT_PRICE_SCHEDULE_OVRD'=>$request->default_price_schedule_ovrd,
                    'DEFAULT_BILLING_TYPE'=>$request->default_billing_type,
                    'DEFAULT_CAP_AMOUNT'=>$request->default_cap_amount,
                    'DEFAULT_COMM_CHARGE_PAID'=>$request->deafult_comm_charge_paid,
                    'DEFAULT_COMM_CHARGE_REJECT'=>$request->default_comm_charge_reject,
                    'GPI_EXCEPTION_LIST_OVRD'=>$request->gpi_exception_list_ovrd,
                    'NDC_EXCEPTION_LIST_OVRD'=>$request->ndc_exception_list_ovrd,
                    'WITHHOLD_PAID_AMT'=>$request->withhold_paid_amt,
                    'WITHHOLD_PAID_PERCENT'=>$request->withhold_paid_percent,
                    'WITHHOLD_U_AND_C_FLAG'=>$request->withhold_u_and_c_flag,
                    'WITHHOLD_ACTIVE_FLAG'=>$request->withhold_active_flag,
                    'MIN_RX_QTY'=>$request->min_rx_qty,
                    'MAX_RX_QTY'=>$request->max_rx_qty,


                ]
            );


            $accum_benfit_stat = DB::table('RX_NETWORKS' )->insert(
                [
                    'NETWORK_ID' => strtoupper( $request->network_id ),
                    'PHARMACY_NABP'=>$request->pharmacy_nabp,
                    'PRICE_SCHEDULE_OVRD'=>$request->price_schedule_ovrd,
                    'PARTICIPATION_OVRD'=>$request->participation_ovrd,
                    'DATE_TIME_CREATED'=>$createddate,
                    'DATE_TIME_MODIFIED'=>$createddate,
                    'EFFECTIVE_DATE'=>$request->effective_date,
                    'TERMINATION_DATE'=>$request->termination_date,

                ]
            );
            $benefitcode = DB::table( 'RX_NETWORKS' )->where( 'network_id', 'like', $request->network_id )->first();


        } else {


            $benefitcode = DB::table('RX_NETWORK_NAMES' )
            ->where( 'network_id', $request->network_id )
            ->update(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'network_name' => $request->network_name,

                ]
            );

            $accum_benfit_stat = DB::table( 'RX_NETWORKS' )
            ->where('pharmacy_nabp', $request->pharmacy_nabp )
            ->update(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'pharmacy_nabp'=>$request->pharmacy_nabp,
                    'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,


                ]
            );


            $benefitcode = DB::table( 'RX_NETWORKS' )->where( 'network_id', 'like', $request->network_id )->first();


        }


        return $this->respondWithToken( $this->token(), 'Successfully added', $benefitcode );
    }


    public function all(Request $request)

    {

        if($request->pharmacy_nabp){

            $ndc = DB::table('RX_NETWORK_NAMES')
            ->join('RX_NETWORKS', 'RX_NETWORK_NAMES.NETWORK_ID', '=', 'RX_NETWORK_NAMES.NETWORK_ID')
            ->where('RX_NETWORKS.PHARMACY_NABP',$request->pharmacy_nabp)->get();
    
            if($ndc){
                return $this->respondWithToken($this->token(), '', $ndc);
    
            }

        }

       
        else{

            return $this->respondWithToken($this->token(), 'No Data Found');


        }
    }


   



    public function search(Request $request)

    {
      $ndc  = DB::select("SELECT * FROM RX_NETWORK_NAMES");

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    
    public function ProviderIdsearch(Request $request)
    {
        $priceShedule = DB::table('PRICE_SCHEDULE')
            ->where('PRICE_SCHEDULE', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PRICE_SCHEDULE_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('COPAY_SCHEDULE', 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $priceShedule);
    }




    public function getList ($ndcid)
    {
        $ndc =DB::table('RX_NETWORK_NAMES')
        ->join('RX_NETWORKS', 'RX_NETWORKS.NETWORK_ID', '=', 'RX_NETWORK_NAMES.NETWORK_ID')
        ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'RX_NETWORKS.PHARMACY_NABP')
        ->where('RX_NETWORK_NAMES.NETWORK_NAME', 'like', '%' .$ndcid. '%')
        ->orWhere('RX_NETWORKS.NETWORK_ID', 'like', '%' .$ndcid. '%')
        ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function getDetails( $ndcid ) {
        $ndc = DB::table('RX_NETWORKS' )
        ->join('RX_NETWORK_NAMES', 'RX_NETWORK_NAMES.NETWORK_ID', '=', 'RX_NETWORKS.NETWORK_ID')
        ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'RX_NETWORKS.PHARMACY_NABP')
        ->where( 'PHARMACY_TABLE.PHARMACY_NABP', 'like', '%' .$ndcid. '%' )
        ->first();

        return $this->respondWithToken( $this->token(), '', $ndc );

    }

}
