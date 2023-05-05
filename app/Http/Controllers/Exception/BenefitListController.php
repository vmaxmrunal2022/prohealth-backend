<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class BenefitListController extends Controller
{
   
    public function index(Request $request)
    {
        $ndc = DB::table('BENEFIT_CODES')->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function BenefitLists(Request $request){

        $ndc = DB::table('BENEFIT_LIST_NAMES')->get();

    return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function addcopy( Request $request ) {
        $createddate = date( 'y-m-d' );


        $recordcheck=DB::table('BENEFIT_LIST')
        ->where('benefit_list_id', strtoupper($request->benefit_list_id))
        ->first();



        if ( $request->has( 'new' ) ) {

            if($recordcheck){

                return $this->respondWithToken( $this->token(), 'Benefit List ID Already Exists',$recordcheck);


            }else{

                $accum_benfit_stat_names = DB::table('BENEFIT_LIST_NAMES')->insert(
                    [
                        'benefit_list_id' => strtoupper($request->benefit_list_id ),
                        'description'=>$request->description
                        
    
                    ]
                );
    
    
                $accum_benfit_stat = DB::table('BENEFIT_LIST')
                ->insert(
                    [
                        'BENEFIT_LIST_ID'=>strtoupper($request->benefit_list_id),
                        'BENEFIT_CODE'=>$request->benefit_code,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'USER_ID'=>'',
                        'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                        'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                        'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                        'MIN_AGE'=>$request->min_age,
                        'MAX_AGE'=>$request->max_age,
                        'MIN_PRICE'=>$request->min_price,
                        'MAX_PRICE'=>$request->max_price,
                        'MIN_PRICE_OPT'=>$request->max_price_opt,
                        'MAX_PRICE_OPT'=>$request->max_price_opt,
                        'VALID_RELATION_CODE'=>$request->valid_relation_code,
                        'SEX_RESTRICTION'=>$request->sex_restriction,
                        'MODULE_EXIT'=>$request->module_exit,
                        'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                        'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                        'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                        'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                        'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                        'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                        'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                        'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                        'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                        'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                        'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                        'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                        'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                        'BASE_APPLY_PERCENT_OPT'=>$request->base_apply_percent_opt,
                        'BASE_APPLY_PERCENT'=>$request->base_apply_percent,
                        'MAX_BASE_AMOUNT'=>$request->max_base_amount,
                        'APPLY_MM_CLAIM_MAX_OPT'=>$request->apply_mm_claim_max_opt,
                        'PRESCRIBER_EXCEPTIONS_FLAG'=>$request->prescriber_exceptions_flag,

    
                    ]
                );

            }



            return $this->respondWithToken( $this->token(), 'Record Added Successfully',$accum_benfit_stat);



        } else {


            $benefitcode = DB::table('BENEFIT_LIST_NAMES' )
            ->where('benefit_list_id', strtoupper($request->benefit_list_id ))


            ->update(
                [
                    'description'=>$request->description,

                ]
            );

            $accum_benfit_stat = DB::table( 'BENEFIT_LIST' )
            ->where('benefit_list_id', strtoupper($request->benefit_list_id ))
            ->update(
                [
                    'BENEFIT_CODE'=>$request->benefit_code,
                    'EFFECTIVE_DATE'=>$request->effective_date,
                    'TERMINATION_DATE'=>$request->termination_date,
                    'DATE_TIME_CREATED'=>$createddate,
                    'USER_ID_CREATED'=>'',
                    'USER_ID'=>'',
                    'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                    'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                    'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                    'MESSAGE'=>$request->message,
                    'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                    'MIN_AGE'=>$request->min_age,
                    'MAX_AGE'=>$request->max_age,
                    'MIN_PRICE'=>$request->min_price,
                    'MAX_PRICE'=>$request->max_price,
                    'MIN_PRICE_OPT'=>$request->max_price_opt,
                    'MAX_PRICE_OPT'=>$request->max_price_opt,
                    'VALID_RELATION_CODE'=>$request->valid_relation_code,
                    'SEX_RESTRICTION'=>$request->sex_restriction,
                    'MODULE_EXIT'=>$request->module_exit,
                    'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                    'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                    'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                    'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                    'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                    'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                    'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                    'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                    'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                    'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                    'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                    'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                    'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                    'BASE_APPLY_PERCENT_OPT'=>$request->base_apply_percent_opt,
                    'BASE_APPLY_PERCENT'=>$request->base_apply_percent,
                    'MAX_BASE_AMOUNT'=>$request->max_base_amount,
                    'APPLY_MM_CLAIM_MAX_OPT'=>$request->apply_mm_claim_max_opt,
                    'PRESCRIBER_EXCEPTIONS_FLAG'=>$request->prescriber_exceptions_flag,
                  
                  

                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$accum_benfit_stat);

        }


    }

    public function add(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('BENEFIT_LIST_NAMES')
        ->where('benefit_list_id',$request->benefit_list_id)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'benefit_list_id' => ['required', 'max:10', Rule::unique('BENEFIT_LIST_NAMES')->where(function ($q) {
                    $q->whereNotNull('benefit_list_id');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('BENEFIT_LIST')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('BENEFIT_LIST')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                'benefit_list_id' => ['required', 'max:10', Rule::unique('BENEFIT_LIST')->where(function ($q) {
                    $q->whereNotNull('benefit_list_id');
                })],

                "description" => ['max:36'],
                "BENEFIT_CODE"=>['max:10'],
                "EFFECTIVE_DATE"=>['max:10'],
                'TERMINATION_DATE'=>['max:15','min:5'],
                'PRICING_STRATEGY_ID'=>['max:10'],
                'ACCUM_BENE_STRATEGY_ID'=>['max:10'],
                'COPAY_STRATEGY_ID'=>['max:10'],
                'MESSAGE'=>['max:40'],
                'MESSAGE_STOP_DATE'=>['max:10'],
                'MIN_AGE'=>['max:11'],
                'MAX_AGE'=>['max:10'],
                'MIN_PRICE'=>['max:10'],
                'MAX_PRICE'=>['max:10'],
                'MIN_PRICE_OPT'=>['max:1'],
                'MAX_PRICE_OPT'=>['max:1'],
                'VALID_RELATION_CODE'=>['max:1'],
                'SEX_RESTRICTION'=>['max:6'],
                'MODULE_EXIT'=>['max:1'],
                'REJECT_ONLY_MSG_FLAG'=>['max:1'],
                'MAX_QTY_OVER_TIME'=>['max:6'],
                'MAX_RX_QTY_OPT'=>['max:1'],
                'COVERAGE_START_DAYS'=>['max:6'],
                'RX_QTY_OPT_MULTIPLIER'=>['max:6'],
                'DAYS_PER_DISABILITY'=>['max:3'],
                'MAX_PRICE_PER_DAY'=>['min:2|max:12'],
                'MAX_PRICE_PER_DAY_OPT'=>['max:1'],
                'MAX_PRICE_PER_DIAG'=>['min:2','max:12'],
                'MAX_PRICE_PER_DIAG_OPT'=>['max:1'],
                'MAX_PRICE_PER_DIAG_PERIOD'=>['max:1'],
                'MAX_PRICE_PER_DIAG_MULT'=>['max:3','min:0'],
                'DAYS_PER_DISABILITY_OPT'=>['max:1'],
                'BASE_APPLY_PERCENT_OPT'=>['max:6'],
                'MAX_BASE_AMOUNT'=>['max:12'],
                'APPLY_MM_CLAIM_MAX_OPT'=>['max:1'],
                'PRESCRIBER_EXCEPTIONS_FLAG'=>['max:1'],

            
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Benefit List  Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('BENEFIT_LIST_NAMES')->insert(
                    [
                        'benefit_list_id' => $request->benefit_list_id,
                        'description'=>$request->description,
                        
                    ]
                );
    
                $add = DB::table('BENEFIT_LIST')
                ->insert(
                    [
                        'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                        'BENEFIT_CODE'=>$request->benefit_code,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'USER_ID'=>'',
                        'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                        'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                        'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                        'MIN_AGE'=>$request->min_age,
                        'MAX_AGE'=>$request->max_age,
                        'MIN_PRICE'=>$request->min_price,
                        'MAX_PRICE'=>$request->max_price,
                        'MIN_PRICE_OPT'=>$request->max_price_opt,
                        'MAX_PRICE_OPT'=>$request->max_price_opt,
                        'VALID_RELATION_CODE'=>$request->valid_relation_code,
                        'SEX_RESTRICTION'=>$request->sex_restriction,
                        'MODULE_EXIT'=>$request->module_exit,
                        'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                        'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                        'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                        'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                        'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                        'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                        'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                        'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                        'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                        'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                        'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                        'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                        'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                        'BASE_APPLY_PERCENT_OPT'=>$request->base_apply_percent_opt,
                        'BASE_APPLY_PERCENT'=>$request->base_apply_percent,
                        'MAX_BASE_AMOUNT'=>$request->max_base_amount,
                        'APPLY_MM_CLAIM_MAX_OPT'=>$request->apply_mm_claim_max_opt,
                        'PRESCRIBER_EXCEPTIONS_FLAG'=>$request->prescriber_exceptions_flag,

    
                    ]);
                   
    
                $add = DB::table('BENEFIT_LIST')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                "description" => ['max:36'],
                "BENEFIT_CODE"=>['max:10'],
                "EFFECTIVE_DATE"=>['max:10'],
                'TERMINATION_DATE'=>['max:15','min:5'],
                'PRICING_STRATEGY_ID'=>['max:10'],
                'ACCUM_BENE_STRATEGY_ID'=>['max:10'],
                'COPAY_STRATEGY_ID'=>['max:10'],
                'MESSAGE'=>['max:40'],
                'MESSAGE_STOP_DATE'=>['max:10'],
                'MIN_AGE'=>['max:11'],
                'MAX_AGE'=>['max:10'],
                'MIN_PRICE'=>['max:10'],
                'MAX_PRICE'=>['max:10'],
                'MIN_PRICE_OPT'=>['max:1'],
                'MAX_PRICE_OPT'=>['max:1'],
                'VALID_RELATION_CODE'=>['max:1'],
                'SEX_RESTRICTION'=>['max:6'],
                'MODULE_EXIT'=>['max:1'],
                'REJECT_ONLY_MSG_FLAG'=>['max:1'],
                'MAX_QTY_OVER_TIME'=>['max:6'],
                'MAX_RX_QTY_OPT'=>['max:1'],
                'COVERAGE_START_DAYS'=>['max:6'],
                'RX_QTY_OPT_MULTIPLIER'=>['max:6'],
                'DAYS_PER_DISABILITY'=>['max:3'],
                'MAX_PRICE_PER_DAY'=>['min:2|max:12'],
                'MAX_PRICE_PER_DAY_OPT'=>['max:1'],
                'MAX_PRICE_PER_DIAG'=>['min:2','max:12'],
                'MAX_PRICE_PER_DIAG_OPT'=>['max:1'],
                'MAX_PRICE_PER_DIAG_PERIOD'=>['max:1'],
                'MAX_PRICE_PER_DIAG_MULT'=>['max:3','min:0'],
                'DAYS_PER_DISABILITY_OPT'=>['max:1'],
                'BASE_APPLY_PERCENT_OPT'=>['max:6'],
                'MAX_BASE_AMOUNT'=>['max:12'],
                'APPLY_MM_CLAIM_MAX_OPT'=>['max:1'],
                'PRESCRIBER_EXCEPTIONS_FLAG'=>['max:1'],


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }
    
                $update_names = DB::table('BENEFIT_LIST_NAMES')
                ->where('benefit_list_id', $request->benefit_list_id )
                ->first();
                    
    
                $checkGPI = DB::table('BENEFIT_LIST')
                    ->where('benefit_list_id', $request->benefit_list_id)
                    ->where('benefit_code',$request->benefit_code)
                    ->where('effective_date',$request->effective_date)
                    ->get()
                    ->count();
                    // dd($checkGPI);
                // if result >=1 then update BENEFIT_LIST table record
                //if result 0 then add BENEFIT_LIST record

    
                if ($checkGPI <= "0") {
                    $update = DB::table('BENEFIT_LIST')
                    ->insert(
                        [
                            'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                            'BENEFIT_CODE'=>$request->benefit_code,
                            'EFFECTIVE_DATE'=>$request->effective_date,
                            'TERMINATION_DATE'=>$request->termination_date,
                            'DATE_TIME_CREATED'=>$createddate,
                            'USER_ID_CREATED'=>'',
                            'USER_ID'=>'',
                            'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                            'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                            'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                            'MESSAGE'=>$request->message,
                            'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                            'MIN_AGE'=>$request->min_age,
                            'MAX_AGE'=>$request->max_age,
                            'MIN_PRICE'=>$request->min_price,
                            'MAX_PRICE'=>$request->max_price,
                            'MIN_PRICE_OPT'=>$request->max_price_opt,
                            'MAX_PRICE_OPT'=>$request->max_price_opt,
                            'VALID_RELATION_CODE'=>$request->valid_relation_code,
                            'SEX_RESTRICTION'=>$request->sex_restriction,
                            'MODULE_EXIT'=>$request->module_exit,
                            'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                            'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                            'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                            'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                            'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                            'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                            'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                            'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                            'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                            'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                            'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                            'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                            'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                            'BASE_APPLY_PERCENT_OPT'=>$request->base_apply_percent_opt,
                            'BASE_APPLY_PERCENT'=>$request->base_apply_percent,
                            'MAX_BASE_AMOUNT'=>$request->max_base_amount,
                            'APPLY_MM_CLAIM_MAX_OPT'=>$request->apply_mm_claim_max_opt,
                            'PRESCRIBER_EXCEPTIONS_FLAG'=>$request->prescriber_exceptions_flag,
    
        
                        ]);
                   

                $update = DB::table('BENEFIT_LIST')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                } else {
  

                    $add_names = DB::table('BENEFIT_LIST_NAMES')
                    ->where('benefit_list_id',$request->benefit_list_id)
                    ->update(
                        [
                            'description'=>$request->description,
                            
                        ]
                    );

                    $update = DB::table('BENEFIT_LIST' )
                    ->where('benefit_list_id', $request->benefit_list_id)
                    ->where('benefit_code',$request->benefit_code)
                    ->where('effective_date',$request->effective_date)  
                    ->update(
                        [
                            'BENEFIT_CODE'=>$request->benefit_code,
                            'EFFECTIVE_DATE'=>$request->effective_date,
                            'TERMINATION_DATE'=>$request->termination_date,
                            'DATE_TIME_CREATED'=>$createddate,
                            'USER_ID_CREATED'=>'',
                            'USER_ID'=>'',
                            'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                            'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                            'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                            'MESSAGE'=>$request->message,
                            'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                            'MIN_AGE'=>$request->min_age,
                            'MAX_AGE'=>$request->max_age,
                            'MIN_PRICE'=>$request->min_price,
                            'MAX_PRICE'=>$request->max_price,
                            'MIN_PRICE_OPT'=>$request->max_price_opt,
                            'MAX_PRICE_OPT'=>$request->max_price_opt,
                            'VALID_RELATION_CODE'=>$request->valid_relation_code,
                            'SEX_RESTRICTION'=>$request->sex_restriction,
                            'MODULE_EXIT'=>$request->module_exit,
                            'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                            'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                            'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                            'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                            'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                            'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                            'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                            'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                            'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                            'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                            'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                            'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                            'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                            'BASE_APPLY_PERCENT_OPT'=>$request->base_apply_percent_opt,
                            'BASE_APPLY_PERCENT'=>$request->base_apply_percent,
                            'MAX_BASE_AMOUNT'=>$request->max_base_amount,
                            'APPLY_MM_CLAIM_MAX_OPT'=>$request->apply_mm_claim_max_opt,
                            'PRESCRIBER_EXCEPTIONS_FLAG'=>$request->prescriber_exceptions_flag,
                          
        
                        ]

                    
                    );
                    $update = DB::table('BENEFIT_LIST')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                }
    
               

            }

           
        }
    }



    public function search(Request $request)
    {
        $ndc = DB::table('BENEFIT_LIST_NAMES')
        ->where('BENEFIT_LIST_ID', 'like', '%' .$request->search. '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getBLList($ndcid)
    {
        $ndclist = DB::table('BENEFIT_LIST')
        ->join('BENEFIT_LIST_NAMES', 'BENEFIT_LIST_NAMES.BENEFIT_LIST_ID', '=', 'BENEFIT_LIST.BENEFIT_LIST_ID')

                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('BENEFIT_LIST.BENEFIT_LIST_ID', $ndcid)
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getBLItemDetails($ndcid,$ndcid2)
    {
        $ndc = DB::table('BENEFIT_LIST')
        ->select('BENEFIT_LIST.*',
        'NAMES.DESCRIPTION',
        'BENEFIT_LIST.PRICING_STRATEGY_ID as pricing_strategy_description',
        'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_starategy_description',
        'CODES.DESCRIPTION AS benefit_code_description',
        'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_starategy_description')
        
        ->leftjoin('BENEFIT_LIST_NAMES AS NAMES', 'NAMES.BENEFIT_LIST_ID', '=', 'BENEFIT_LIST.BENEFIT_LIST_ID')
        ->leftjoin('BENEFIT_CODES AS CODES','CODES.BENEFIT_CODE','=','BENEFIT_LIST.BENEFIT_CODE')
        ->leftjoin('PRICING_STRATEGY as pricing_strategies','pricing_strategies.PRICING_STRATEGY_ID','=','BENEFIT_LIST.PRICING_STRATEGY_ID')
        ->leftjoin('ACCUM_BENE_STRATEGY_NAMES','ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID','=','BENEFIT_LIST.ACCUM_BENE_STRATEGY_ID')
        ->leftjoin('COPAY_STRATEGY_NAMES','COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID','=','BENEFIT_LIST.COPAY_STRATEGY_ID')


        ->where('BENEFIT_LIST.BENEFIT_LIST_ID',$ndcid)
        ->where('BENEFIT_LIST.BENEFIT_CODE',$ndcid2)

        // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
