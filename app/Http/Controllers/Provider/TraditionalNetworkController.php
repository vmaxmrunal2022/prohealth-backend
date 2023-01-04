<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraditionalNetworkController extends Controller
{



    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {



            $accum_benfit_stat_names = DB::table('RX_NETWORK_NAMES')->insert(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'network_name' => $request->network_name,


                ]
            );


            $accum_benfit_stat = DB::table('RX_NETWORKS' )->insert(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'pharmacy_nabp'=>$request->pharmacy_nabp,
                    'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,

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

             $ndc = DB::table('RX_NETWORK_NAMES')
        ->join('RX_NETWORKS', 'RX_NETWORK_NAMES.NETWORK_ID', '=', 'RX_NETWORK_NAMES.NETWORK_ID')
                ->get();



    return $this->respondWithToken($this->token(), '', $ndc);
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
