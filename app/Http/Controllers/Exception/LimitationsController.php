<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class LimitationsController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('LIMITATIONS_LIST')
                ->select('LIMITATIONS_LIST', 'LIMITATIONS_LIST_NAME','EFFECTIVE_DATE')
                ->where('LIMITATIONS_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('LIMITATIONS_LIST_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

         return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($id){

        $limitationDetails = DB::table('LIMITATIONS_LIST')
        ->where('LIMITATIONS_LIST',$id)
        ->first();
        return $this->respondWithToken($this->token(), '', $limitationDetails);


    }

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );

        $recordcheck = DB::table('LIMITATIONS_LIST')
        ->where('limitations_list', strtoupper($request->limitations_list))
        ->first();


        if ( $request->has('new') ) {


            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Limitation List ID Already Exists', $recordcheck);


            }

            else{

                
                $insert = DB::table('LIMITATIONS_LIST')->insert(
                    [
                        'LIMITATIONS_LIST' => $request->limitations_list,
                        'LIMITATIONS_LIST_NAME'=>$request->limitations_list_name,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'MIN_RX_QTY'=>$request->min_rx_qty,
                        'MAX_RX_QTY'=>$request->max_rx_qty,
                        'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                        'MIN_PRICE'=>$request->min_price,
                        'MIN_PRICE_OPT'=>$request->min_price_opt,
                        'MAX_PRICE'=>$request->max_price,
                        'MAX_PRICE_OPT'=>$request->max_price_opt,
                        'MIN_RX_DAYS'=>$request->min_rx_days,
                        'MAX_RX_DAYS'=>$request->max_rx_days,
                        'MIN_CTL_DAYS'=>$request->min_ctl_days,
                        'MAX_CTL_DAYS'=>$request->max_ctl_days,
                        'MAX_DAYS_PER_FILL'=>$request->max_days_per_fill,
                        'MAX_DOSE'=>$request->max_dose,
                        'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                        'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                        'MIN_AGE'=>$request->min_age,
                        'MAX_AGE'=>$request->max_age,
                        'MAX_RXS_PATIENT'=>$request->max_rxs_patient,
                        'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                        'MAX_PRICE_PATIENT'=>$request->max_price_patient,
                        'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                        'MAX_REFILLS'=>$request->max_refills,
                        'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                        'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                        'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                        'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                        'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                        'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                        'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                        'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                        'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                        'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                        'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                        'DAYS_SUPPLY_OPT_MULTIPLIER'=>$request->days_supply_opt_multiplier,
                        'MERGE_DEFAULTS'=>$request->merge_defaults,
                        'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                        'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                        'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                        'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                        'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                        'VALID_RELATION_CODE'=>$request->valid_relation_code,
                        'SEX_RESTRICTION'=>$request->sex_restriction,
                        'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                        'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                        'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                        'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                        'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                        'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                        'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                        'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                        'DATE_TIME_CREATED'=>$createddate,
                        

                     
                    ]
                );
                return $this->respondWithToken( $this->token(), 'Record Added Successfully',$insert);
    
    

            }

            
           
        } else {


           

            $update = DB::table('LIMITATIONS_LIST' )
            ->where('limitations_list', strtoupper($request->limitations_list ))
            ->update(
                [
                    'LIMITATIONS_LIST_NAME'=>$request->limitations_list_name,
                    'EFFECTIVE_DATE'=>$request->effective_date,
                    'TERMINATION_DATE'=>$request->termination_date,
                    'MIN_RX_QTY'=>$request->min_rx_qty,
                    'MAX_RX_QTY'=>$request->max_rx_qty,
                    'QTY_DSUP_COMPARE_RULE'=>$request->qty_dsup_compare_rule,
                    'MIN_PRICE'=>$request->min_price,
                    'MIN_PRICE_OPT'=>$request->min_price_opt,
                    'MAX_PRICE'=>$request->max_price,
                    'MAX_PRICE_OPT'=>$request->max_price_opt,
                    'MIN_RX_DAYS'=>$request->min_rx_days,
                    'MAX_RX_DAYS'=>$request->max_rx_days,
                    'MIN_CTL_DAYS'=>$request->min_ctl_days,
                    'MAX_CTL_DAYS'=>$request->max_ctl_days,
                    'MAX_DAYS_PER_FILL'=>$request->max_days_per_fill,
                    'MAX_DOSE'=>$request->max_dose,
                    'DRUG_COV_START_DAYS'=>$request->drug_cov_start_days,
                    'ACUTE_DOSING_DAYS'=>$request->acute_dosing_days,
                    'MIN_AGE'=>$request->min_age,
                    'MAX_AGE'=>$request->max_age,
                    'MAX_RXS_PATIENT'=>$request->max_rxs_patient,
                    'MAX_RXS_TIME_FLAG'=>$request->max_rxs_time_flag,
                    'MAX_PRICE_PATIENT'=>$request->max_price_patient,
                    'MAX_PRICE_TIME_FLAG'=>$request->max_price_time_flag,
                    'MAX_REFILLS'=>$request->max_refills,
                    'RETAIL_MAX_FILLS_OPT'=>$request->retail_max_fills_opt,
                    'MAX_QTY_PER_FILL'=>$request->max_qty_per_fill,
                    'STARTER_DOSE_DAYS'=>$request->starter_dose_days,
                    'STARTER_DOSE_BYPASS_DAYS'=>$request->starter_dose_bypass_days,
                    'STARTER_DOSE_MAINT_BYPASS_DAYS'=>$request->starter_dose_maint_bypass_days,
                    'MAINT_DOSE_UNITS_DAY'=>$request->maint_dose_units_day,
                    'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                    'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                    'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                    'MAX_DAYS_OVER_TIME'=>$request->max_days_over_time,
                    'MAX_DAYS_SUPPLY_OPT'=>$request->max_days_supply_opt,
                    'DAYS_SUPPLY_OPT_MULTIPLIER'=>$request->days_supply_opt_multiplier,
                    'MERGE_DEFAULTS'=>$request->merge_defaults,
                    'MAIL_ORDER_MIN_RX_DAYS'=>$request->mail_order_min_rx_days,
                    'MAIL_ORDER_MAX_RX_DAYS'=>$request->mail_order_max_rx_days,
                    'MAIL_ORD_MAX_DAYS_SUPPLY_OPT'=>$request->mail_ord_max_days_supply_opt,
                    'MAIL_ORDER_MAX_REFILLS'=>$request->mail_order_max_refills,
                    'MAIL_ORD_MAX_FILLS_OPT'=>$request->mail_ord_max_fills_opt,
                    'VALID_RELATION_CODE'=>$request->valid_relation_code,
                    'SEX_RESTRICTION'=>$request->sex_restriction,
                    'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                    'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                    'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                    'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                    'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                    'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                    'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                    'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                    'DATE_TIME_CREATED'=>$createddate,
                    
                ]
            );


            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

        }


    }
}
