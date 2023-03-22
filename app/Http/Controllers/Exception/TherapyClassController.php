<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TherapyClassController extends Controller
{


    public function TherapyClassList(Request $request){

        $ndc = DB::table('TC_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {


            $accum_benfit_stat_names = DB::table('TC_EXCEPTIONS')->insert(
                [
                    'ther_class_exception_list' => strtoupper($request->ther_class_exception_list ),
                    'exception_name'=>$request->exception_name,
                    

                ]
            );

            $accum_benfit_stat = DB::table('TC_EXCEPTION_LISTS')->insert(
                [
                    'ther_class_exception_list' => strtoupper($request->ther_class_exception_list),
                    'acute_dosing_days'=>$request->acute_dosing_days,
                    'alternate_copay_sched'=>$request->alternate_copay_sched,
                    'alternate_price_schedule'=>$request->alternate_price_schedule,
                    'therapy_class'=>$request->therapy_class,
                    'message'=>$request->message,
                    // 'bga_inc_exc_ind'=>$request->bga_inc_exc_ind,
                    // 'bng_multi_inc_exc_ind'=>$request->bng_multi_inc_exc_ind,
                    // 'bng_sngl_inc_exc_ind'=>$request->bng_sngl_inc_exc_ind,
                    // 'brand_copay_amt'=>$request->brand_copay_amt,
                    // 'conversion_product_ndc'=>$request->conversion_product_ndc,
                    // 'copay_network_ovrd'=>$request->copay_network_ovrd,
                    // 'denial_override'=>$request->denial_override,
                    // 'diagnosis_list'=>$request->diagnosis_list,
                    // 'drug_cov_start_days'=>$request->drug_cov_start_days,
                    // 'effective_date'=>$request->effective_date,
                    // 'exception_list'=>$request->exception_list,
                    // 'gen_inc_exc_ind'=>$request->gen_inc_exc_ind,
                    // 'generic_copay_amt'=>$request->generic_copay_amt,
                    // 'message_stop_date'=>$request->message_stop_date,
                    // 'module_exit'=>$request->module_exit,
                    // 'new_drug_status'=>$request->new_drug_status,
                    // 'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    // 'sex_restriction'=>$request->sex_restriction,
                    // 'termination_date'=>$request->termination_date,

        
                 
                ]
            );
            // print_r($accum_benfit_stat);
            // exit();
            $benefitcode = DB::table('TC_EXCEPTION_LISTS')->where('ther_class_exception_list', 'like', '%'.$request->ther_class_exception_list .'%')->first();


        } else {


            // dd($request->all())

            $benefitcode = DB::table('TC_EXCEPTION_LISTS' )
            ->where('therapy_class', $request->therapy_class )
            ->update(
                [
                    'ther_class_exception_list' => strtoupper($request->ther_class_exception_list),
                    'acute_dosing_days'=>$request->acute_dosing_days,
                    'alternate_copay_sched'=>$request->alternate_copay_sched,
                    'alternate_price_schedule'=>$request->alternate_price_schedule,
                    'therapy_class'=>$request->therapy_class,
                    'message'=>$request->message,
                    // 'bga_inc_exc_ind'=>$request->bga_inc_exc_ind,
                    // 'bng_multi_inc_exc_ind'=>$request->bng_multi_inc_exc_ind,
                    // 'bng_sngl_inc_exc_ind'=>$request->bng_sngl_inc_exc_ind,
                    // 'brand_copay_amt'=>$request->brand_copay_amt,
                    // 'conversion_product_ndc'=>$request->conversion_product_ndc,
                    // 'copay_network_ovrd'=>$request->copay_network_ovrd,
                    // 'denial_override'=>$request->denial_override,
                    // 'diagnosis_list'=>$request->diagnosis_list,
                    // 'drug_cov_start_days'=>$request->drug_cov_start_days,
                    // 'effective_date'=>$request->effective_date,
                    // 'exception_list'=>$request->exception_list,
                    // 'gen_inc_exc_ind'=>$request->gen_inc_exc_ind,
                    // 'generic_copay_amt'=>$request->generic_copay_amt,
                    // 'message_stop_date'=>$request->message_stop_date,
                    // 'module_exit'=>$request->module_exit,
                    // 'new_drug_status'=>$request->new_drug_status,
                    // 'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    // 'sex_restriction'=>$request->sex_restriction,
                    // 'termination_date'=>$request->termination_date,

        

                ]
            );

            $benefitcode = DB::table('TC_EXCEPTION_LISTS')->where('ther_class_exception_list', 'like', '%'.$request->ther_class_exception_list .'%')->first();


            $accum_benfit_stat = DB::table('TC_EXCEPTIONS' )
            ->where('ther_class_exception_list', $request->ther_class_exception_list )
            ->update(
                [
                    'ther_class_exception_list' => strtoupper( $request->ther_class_exception_list ),
                    'exception_name'=>$request->exception_name,
                   
                  

                ]
            );

            $benefitcode = DB::table('TC_EXCEPTIONS')->where('ther_class_exception_list', 'like', '%'.$request->ther_class_exception_list .'%')->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }

    public function exceptionswithDesc(Request $request){

        $ndc = DB::table('TC_EXCEPTION_LISTS')
        ->select('TC_EXCEPTION_LISTS.THERAPY_CLASS','TC_EXCEPTIONS.EXCEPTION_NAME')
        ->join('TC_EXCEPTIONS','TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST','=','TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')
        ->get();
        return $this->respondWithToken($this->token(), '', $ndc);

    }
    public function search(Request $request)
    {
        $ndc = DB::table('TC_EXCEPTIONS')
                ->select('THER_CLASS_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('THER_CLASS_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getTCList($ndcid)
    {
        $ndclist = DB::table('TC_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('THER_CLASS_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getTCItemDetails($ndcid)
    {
        $ndc = DB::table('TC_EXCEPTION_LISTS')
                    ->select('TC_EXCEPTION_LISTS.*', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST as exception_list', 'TC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->join('TC_EXCEPTIONS', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST', '=', 'TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')
                    ->where('TC_EXCEPTION_LISTS.therapy_class', $ndcid)  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
