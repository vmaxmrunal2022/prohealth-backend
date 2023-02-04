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


            $accum_benfit_stat_names = DB::table('NDC_EXCEPTIONS')->insert(
                [
                    'ndc_exception_list' => $request->ndc_exception_list,
                    'exception_name'=>$request->exception_name,
                    
                ]
            );

            $accum_benfit_stat = DB::table('NDC_EXCEPTION_LISTS' )->insert(
                [
                    'ndc_exception_list' => $request->ndc_exception_list,
                    'min_rx_qty'=>$request->min_rx_qty,

                    'acute_dosing_days'=>$request->acute_dosing_days,
                    'alternate_copay_sched'=>$request->alternate_copay_sched,
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
                  
                    'gen_inc_exc_ind'=>$request->gen_inc_exc_ind,
                    'generic_copay_amt'=>$request->generic_copay_amt,
                    'generic_indicator'=>$request->generic_indicator,
                    'mail_ord_max_days_supply_opt'=>$request->mail_ord_max_days_supply_opt,
                    'mail_ord_max_fills_opt'=>$request->mail_ord_max_fills_opt,
                    'mail_order_max_refills'=>$request->mail_order_max_refills,
                    'mail_order_max_rx_days'=>$request->mail_order_max_rx_days,
                    'mail_order_min_rx_days'=>$request->mail_order_min_rx_days,
                    'maint_dose_units_day'=>$request->maint_dose_units_day,
                    'maintenance_drug'=>$request->maintenance_drug,
                    'max_age'=>$request->max_age,
                    'max_ctl_days'=>$request->max_ctl_days,
                    'max_days_over_time'=>$request->max_days_over_time,
                    'max_days_per_fill'=>$request->max_days_per_fill,
                    'max_days_supply_opt'=>$request->max_days_supply_opt,
                    'max_dose'=>$request->max_dose,
                    'max_price'=>$request->max_price,
                    'max_price_opt'=>$request->max_price_opt,
                    'max_price_patient'=>$request->max_price_patient,
                    'max_price_time_flag'=>$request->max_price_time_flag,
                    'max_qty_over_time'=>$request->max_qty_over_time,
                    'max_qty_per_fill'=>$request->max_qty_per_fill,
                    'max_refills'=>$request->max_refills,
                    'max_rx_days'=>$request->max_rx_days,
                    'max_rx_qty'=>$request->max_rx_qty,
                    'max_rx_qty_opt'=>$request->max_rx_qty_opt,
                    'max_rxs_patient'=>$request->max_rxs_patient,
                    'max_rxs_time_flag'=>$request->max_rxs_time_flag,
                    'maximum_allowable_cost'=>$request->maximum_allowable_cost,
                    'merge_defaults'=>$request->merge_defaults,
                    'message'=>$request->message,
                    'message_stop_date'=>$request->message_stop_date,
                    'min_age'=>$request->min_age,
                    'min_rx_days'=>$request->min_rx_days,
                    'min_rx_qty'=>$request->min_rx_qty,
                    'module_exit'=>$request->module_exit,
                    // 'ndc_exception_list'=>$request->ndc_exception_list,
                    'new_drug_status'=>$request->new_drug_status,
                    'pharmacy_list'=>$request->pharmacy_list,
                    'physician_list'=>$request->physician_list,
                    'physician_specialty_list'=>$request->physician_specialty_list,
                    'pkg_determine_id'=>$request->pkg_determine_id,
                    'preferred_product_ndc'=>$request->preferred_product_ndc,
                    'process_rule'=>$request->process_rule,
                    'qty_dsup_compare_rule'=>$request->qty_dsup_compare_rule,
                    'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    'retail_max_fills_opt'=>$request->retail_max_fills_opt,
                    'rx_qty_opt_multiplier'=>$request->rx_qty_opt_multiplier,
                    'sex_restriction'=>$request->sex_restriction,
                    'starter_dose_bypass_days'=>$request->starter_dose_bypass_days,
                    'starter_dose_days'=>$request->starter_dose_days,
                    'starter_dose_maint_bypass_days'=>$request->starter_dose_maint_bypass_days,
                    'valid_relation_code'=>$request->valid_relation_code,
                 
                ]
            );
            $benefitcode = DB::table('NDC_EXCEPTION_LISTS')->where('ndc_exception_list', 'like', '%'.$request->ndc_exception_list .'%')->first();


        } else {

            $benefitcode = DB::table('NDC_EXCEPTION_LISTS' )
            ->where('ndc', $request->ndc )
            ->update(
                [
                    'min_rx_qty'=>$request->min_rx_qty,

                    'acute_dosing_days'=>$request->acute_dosing_days,
                    'alternate_copay_sched'=>$request->alternate_copay_sched,
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
                  
                    'gen_inc_exc_ind'=>$request->gen_inc_exc_ind,
                    'generic_copay_amt'=>$request->generic_copay_amt,
                    'generic_indicator'=>$request->generic_indicator,
                    'mail_ord_max_days_supply_opt'=>$request->mail_ord_max_days_supply_opt,
                    'mail_ord_max_fills_opt'=>$request->mail_ord_max_fills_opt,
                    'mail_order_max_refills'=>$request->mail_order_max_refills,
                    'mail_order_max_rx_days'=>$request->mail_order_max_rx_days,
                    'mail_order_min_rx_days'=>$request->mail_order_min_rx_days,
                    'maint_dose_units_day'=>$request->maint_dose_units_day,
                    'maintenance_drug'=>$request->maintenance_drug,
                    'max_age'=>$request->max_age,
                    'max_ctl_days'=>$request->max_ctl_days,
                    'max_days_over_time'=>$request->max_days_over_time,
                    'max_days_per_fill'=>$request->max_days_per_fill,
                    'max_days_supply_opt'=>$request->max_days_supply_opt,
                    'max_dose'=>$request->max_dose,
                    'max_price'=>$request->max_price,
                    'max_price_opt'=>$request->max_price_opt,
                    'max_price_patient'=>$request->max_price_patient,
                    'max_price_time_flag'=>$request->max_price_time_flag,
                    'max_qty_over_time'=>$request->max_qty_over_time,
                    'max_qty_per_fill'=>$request->max_qty_per_fill,
                    'max_refills'=>$request->max_refills,
                    'max_rx_days'=>$request->max_rx_days,
                    'max_rx_qty'=>$request->max_rx_qty,
                    'max_rx_qty_opt'=>$request->max_rx_qty_opt,
                    'max_rxs_patient'=>$request->max_rxs_patient,
                    'max_rxs_time_flag'=>$request->max_rxs_time_flag,
                    'maximum_allowable_cost'=>$request->maximum_allowable_cost,
                    'merge_defaults'=>$request->merge_defaults,
                    'message'=>$request->message,
                    'message_stop_date'=>$request->message_stop_date,
                    'min_age'=>$request->min_age,
                    'min_rx_days'=>$request->min_rx_days,
                    'min_rx_qty'=>$request->min_rx_qty,
                    'module_exit'=>$request->module_exit,
                    // 'ndc_exception_list'=>$request->ndc_exception_list,
                    'new_drug_status'=>$request->new_drug_status,
                    'pharmacy_list'=>$request->pharmacy_list,
                    'physician_list'=>$request->physician_list,
                    'physician_specialty_list'=>$request->physician_specialty_list,
                    'pkg_determine_id'=>$request->pkg_determine_id,
                    'preferred_product_ndc'=>$request->preferred_product_ndc,
                    'process_rule'=>$request->process_rule,
                    'qty_dsup_compare_rule'=>$request->qty_dsup_compare_rule,
                    'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    'retail_max_fills_opt'=>$request->retail_max_fills_opt,
                    'rx_qty_opt_multiplier'=>$request->rx_qty_opt_multiplier,
                    'sex_restriction'=>$request->sex_restriction,
                    'starter_dose_bypass_days'=>$request->starter_dose_bypass_days,
                    'starter_dose_days'=>$request->starter_dose_days,
                    'starter_dose_maint_bypass_days'=>$request->starter_dose_maint_bypass_days,
                    'valid_relation_code'=>$request->valid_relation_code,
                  

                ]
            );

            $benefitcode = DB::table('NDC_EXCEPTION_LISTS')->where('ndc', 'like', '%'.$request->ndc .'%')->first();


            $accum_benfit_stat = DB::table('NDC_EXCEPTIONS' )
            ->where('ndc_exception_list', $request->ndc_exception_list )
            ->update(
                [
                    'ndc_exception_list' => $request->ndc_exception_list,
                    'exception_name'=>$request->exception_name,



                ]
            );

            $benefitcode = DB::table('NDC_EXCEPTIONS')->where('ndc_exception_list', 'like', '%'.$request->ndc_exception_list .'%')->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }


    public function ndcList(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
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

    public function getNdcDropDown(){
        $data = DB::table('NDC_EXCEPTIONS')->get();
        return $this->respondWithToken($this->token(),'',$data);
    }
}
