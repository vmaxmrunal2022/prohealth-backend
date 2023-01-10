<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GPIExceptionController extends Controller
{

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {


            $accum_benfit_stat_names = DB::table('GPI_EXCEPTIONS')->insert(
                [
                    'gpi_exception_list' => strtoupper( $request->gpi_exception_list ),
                    'exception_name'=>$request->exception_name,


                ]
            );

            $accum_benfit_stat = DB::table('GPI_EXCEPTION_LISTS' )->insert(
                [
                    'gpi_exception_list' => strtoupper($request->gpi_exception_list),
                    'generic_product_id'=>$request->generic_product_id,
                    'min_rx_qty'=>$request->min_rx_qty,
                    'acute_dosing_days'=>$request->acute_dosing_days,
                    'alternate_price_schedule'=>$request->alternate_price_schedule,
                    'bga_inc_exc_ind'=>$request->bga_inc_exc_ind,
                    'bng_multi_inc_exc_ind'=>$request->bng_multi_inc_exc_ind,
                    'bng_sngl_inc_exc_ind'=>$request->bng_sngl_inc_exc_ind,
                    'brand_copay_amt'=>$request->brand_copay_amt,
                    'conversion_product_ndc'=>$request->conversion_product_ndc,
                    'copay_network_ovrd'=>$request->copay_network_ovrd,
                    'days_supply_opt_multiplier'=>$request->days_supply_opt_multiplier,
                    'denial_override'=>$request->denial_override,
                    'diagnosis_list'=>$request->diagnosis_list,
                    'drug_cov_start_days'=>$request->drug_cov_start_days,
                    'effective_date'=>$request->effective_date,

                    'gen_inc_exc_ind'=>$request->gen_inc_exc_ind,
                    'generic_copay_amt'=>$request->generic_copay_amt,
                    'generic_product_id'=>$request->generic_product_id,
                    'message_stop_date'=>$request->message_stop_date,
                    'module_exit'=>$request->module_exit,
                    'new_drug_status'=>$request->new_drug_status,
                    'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    'sex_restriction'=>$request->sex_restriction,
                    'termination_date'=>$request->termination_date,

                ]
            );
            $benefitcode = DB::table('GPI_EXCEPTION_LISTS')->where('gpi_exception_list', 'like', '%'.$request->gpi_exception_list .'%')->first();


        } else {


            // dd($request->all())

            $benefitcode = DB::table('GPI_EXCEPTION_LISTS' )
            ->where('gpi_exception_list', $request->gpi_exception_list )
            ->update(
                [
                    'gpi_exception_list' => strtoupper($request->gpi_exception_list),
                    'min_rx_qty'=>$request->min_rx_qty,
                    'acute_dosing_days'=>$request->acute_dosing_days,
                    'alternate_price_schedule'=>$request->alternate_price_schedule,
                    'bga_inc_exc_ind'=>$request->bga_inc_exc_ind,
                    'bng_multi_inc_exc_ind'=>$request->bng_multi_inc_exc_ind,
                    'bng_sngl_inc_exc_ind'=>$request->bng_sngl_inc_exc_ind,
                    'brand_copay_amt'=>$request->brand_copay_amt,
                    'conversion_product_ndc'=>$request->conversion_product_ndc,
                    'copay_network_ovrd'=>$request->copay_network_ovrd,
                    'days_supply_opt_multiplier'=>$request->days_supply_opt_multiplier,
                    'denial_override'=>$request->denial_override,
                    'diagnosis_list'=>$request->diagnosis_list,
                    'drug_cov_start_days'=>$request->drug_cov_start_days,
                    'effective_date'=>$request->effective_date,

                    'gen_inc_exc_ind'=>$request->gen_inc_exc_ind,
                    'generic_copay_amt'=>$request->generic_copay_amt,
                    'generic_product_id'=>$request->generic_product_id,
                    'message_stop_date'=>$request->message_stop_date,
                    'module_exit'=>$request->module_exit,
                    'new_drug_status'=>$request->new_drug_status,
                    'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    'sex_restriction'=>$request->sex_restriction,
                    'termination_date'=>$request->termination_date,



                ]
            );

            $benefitcode = DB::table('GPI_EXCEPTION_LISTS')->where('gpi_exception_list', 'like', '%'.$request->gpi_exception_list .'%')->first();


            $accum_benfit_stat = DB::table('GPI_EXCEPTIONS' )
            ->where('gpi_exception_list', $request->gpi_exception_list )
            ->update(
                [
                    'gpi_exception_list' => $request->gpi_exception_list,
                    'exception_name'=>$request->exception_name,



                ]
            );

            $benefitcode = DB::table('GPI_EXCEPTIONS')->where('gpi_exception_list', 'like', '%'.$request->gpi_exception_list .'%')->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }



    public function search(Request $request)
    {
        $ndc = DB::table('GPI_EXCEPTIONS')
                ->select('GPI_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('GPI_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('GPI_EXCEPTION_LISTS')
                // ->select('gpi_exception_list', 'EXCEPTION_NAME')
                ->where('GPI_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('GPI_EXCEPTION_LISTS')
                    ->select('GPI_EXCEPTION_LISTS.*', 'GPI_EXCEPTIONS.GPI_EXCEPTION_LIST as exception_list', 'GPI_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->join('GPI_EXCEPTIONS', 'GPI_EXCEPTIONS.GPI_EXCEPTION_LIST', '=', 'GPI_EXCEPTION_LISTS.GPI_EXCEPTION_LIST')
                    ->where('GPI_EXCEPTION_LISTS.generic_product_id',$ndcid)
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function getGpiDropDown(){
        $data = DB::table('GPI_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(),'',$data);
    }
}
