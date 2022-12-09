<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NDCExceptionController extends Controller
{

    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {



            $accum_benfit_stat_names = DB::table('RX_NETWORK_RULE_NAMES')->insert(
                [
                    'rx_network_rule_id' => strtoupper( $request->rx_network_rule_id ),
                    

                ]
            );


            $accum_benfit_stat = DB::table('RX_NETWORKS' )->insert(
                [
                    'network_id' => strtoupper( $request->network_id ),
                    'pharmacy_nabp'=>$request->pharmacy_nabp,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,

                ]
            );
            $benefitcode = DB::table('RX_NETWORK_NAMES')->get();


        } else {


            $benefitcode = DB::table('RX_NETWORK_RULE_NAMES' )
            ->where( 'rx_network_rule_id', $request->rx_network_rule_id )


            ->update(
                [
                    'rx_network_rule_id' =>  $request->rx_network_rule_id ,
                    'rx_network_rule_name'=>$request->rx_network_rule_name,

                ]
            );

            $accum_benfit_stat = DB::table( 'RX_NETWORK_RULES' )
            ->where('rx_network_rule_id', $request->rx_network_rule_id )
            ->update(
                [
                    'rx_network_rule_id' => $request->rx_network_rule_id,
                    'rx_network_rule_id_number'=>$request->rx_network_rule_id_number,
                    'price_schedule_ovrd'=>$request->price_schedule_ovrd,
                   'exclude_rule'=>$request->exclude_rule,
                  

                ]
            );

            $benefitcode = DB::table('RX_NETWORK_RULES')->where('rx_network_rule_id', 'like', $request->rx_network_rule_id )->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }




    public function search(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
                ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('NDC_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('NDC_EXCEPTION_LISTS')
                    ->select('NDC_EXCEPTION_LISTS.*', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST as exception_list', 'NDC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
                    ->where('NDC_EXCEPTION_LISTS.NDC', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
