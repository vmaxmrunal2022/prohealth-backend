<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcedureController extends Controller
{


    public function addcopy( Request $request ) {

        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {

            $exist = DB::table('PROCEDURE_EXCEPTION_LISTS')
            ->where('procedure_exception_list',strtoupper($request->procedure_exception_list))->first();

            if($exist){
                return $this->respondWithToken( $this->token(), 'Procedure List ID Already Exists');
            }


            $accum_benfit_stat_names = DB::table('PROCEDURE_EXCEPTION_NAMES')->insert(
                [
                    'procedure_exception_list' => strtoupper($request->procedure_exception_list),

                    'exception_name'=>$request->exception_name,
                    

                ]
            );

            $accum_benfit_stat = DB::table('PROCEDURE_EXCEPTION_LISTS' )
            ->insert(
                [
                    'procedure_exception_list'=>strtoupper($request->procedure_exception_list),
                    'accum_bene_strategy_id'=>$request->accum_bene_strategy_id,
                    'benefit_code'=>$request->benefit_code,
                    'copay_strategy_id'=>$request->copay_strategy_id,
                    'coverage_start_days'=>$request->coverage_start_days,
                    'diagnosis_id'=>$request->diagnosis_id,
                    'diagnosis_list'=>$request->diagnosis_list,
                    'max_price'=>$request->max_price,
                    'max_price_opt'=>$request->max_price_opt,
                    'module_exit'=>$request->module_exit,
                    'new_claim_status'=>$request->new_claim_status,
                    'physician_list'=>$request->physician_list,
                    'physician_specialty_list'=>$request->physician_specialty_list,
                    'pricing_strategy_id'=>$request->pricing_strategy_id,
                    'proc_code_list_id'=>$request->proc_code_list_id,
                    'process_rule'=>$request->process_rule,
                    'provider_type'=>$request->provider_type,
                    'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    'rx_qty_opt_multiplier'=>$request->rx_qty_opt_multiplier,
                    'service_modifier'=>$request->service_modifier,
                    'service_type'=>$request->service_type,
                    'sex_restriction'=>$request->sex_restriction,
                    'valid_relation_code'=>$request->valid_relation_code,
                    'effective_date' => $request->effective_date,
                    'termination_date' => $request->termination_date,
                    'message_stop_date' => $request->message_stop_date,
                    'message'=>$request->message,
                    'min_price'=>$request->min_price,
                    'max_age'=>$request->max_age,
                    'min_age'=>$request->min_age,
                    'max_qty_over_time'=>$request->max_qty_over_time,
                    'ucr'=>$request->ucr,
                ]
            );
            return $this->respondWithToken( $this->token(), 'Record Added Successfully',$accum_benfit_stat);


        } else {

            $exceptipon_names = DB::table('PROCEDURE_EXCEPTION_NAMES')
            ->where('procedure_exception_list',strtoupper($request->procedure_exception_list))
            // ->where('new_claim_status', $request->new_claim_status )
            ->update(
                [
                    'exception_name'=>$request->exception_name,

                ]);


            $exception = DB::table('PROCEDURE_EXCEPTION_LISTS')
            ->where('procedure_exception_list',strtoupper($request->procedure_exception_list))
            ->update(
                [
                    'accum_bene_strategy_id'=>$request->accum_bene_strategy_id,
                    'benefit_code'=>$request->benefit_code,
                    'copay_strategy_id'=>$request->copay_strategy_id,
                    'coverage_start_days'=>$request->coverage_start_days,
                    'diagnosis_id'=>$request->diagnosis_id,
                    'diagnosis_list'=>$request->diagnosis_list,
                    'max_age'=>$request->max_age,
                    'max_price'=>$request->max_price,
                    'max_price_opt'=>$request->max_price_opt,
                    'module_exit'=>$request->module_exit,
                    'new_claim_status'=>$request->new_claim_status,
                    'physician_list'=>$request->physician_list,
                    'physician_specialty_list'=>$request->physician_specialty_list,
                    'pricing_strategy_id'=>$request->pricing_strategy_id,
                    'proc_code_list_id'=>$request->proc_code_list_id,
                    'process_rule'=>$request->process_rule,
                    'provider_type'=>$request->provider_type,
                    'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                    'rx_qty_opt_multiplier'=>$request->rx_qty_opt_multiplier,
                    'service_modifier'=>$request->service_modifier,
                    'service_type'=>$request->service_type,
                    'sex_restriction'=>$request->sex_restriction,
                    'valid_relation_code'=>$request->valid_relation_code,
                    'effective_date' => $request->effective_date,
                    'termination_date' => $request->termination_date,
                    'message_stop_date' => $request->message_stop_date,
                    'message'=>$request->message,
                    'min_price'=>$request->min_price,
                    'max_age'=>$request->max_age,
                    'min_age'=>$request->min_age,
                    'max_qty_over_time'=>$request->max_qty_over_time,
                    'ucr'=>$request->ucr,

                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$exception);


        }


    }

    public function add(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('PROCEDURE_EXCEPTION_NAMES')
        ->where('procedure_exception_list',$request->procedure_exception_list)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'procedure_exception_list' => ['required', 'max:10', Rule::unique('PROCEDURE_EXCEPTION_NAMES')->where(function ($q) {
                    $q->whereNotNull('procedure_exception_list');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "exception_name" => ['max:36'],
                "NEW_CLAIM_STATUS"=>['max:2'],
                "SERVICE_TYPE"=>['max:1'],
                'PROCESS_RULE'=>['max:15','min:5'],
                'PHYSICIAN_LIST'=>['max:10'],
                'PHYSICIAN_SPECIALTY_LIST'=>['max:10'],
                'DIAGNOSIS_LIST'=>['max:10'],
                'PRICING_STRATEGY_ID'=>['max:11'],
                'ACCUM_BENE_STRATEGY_ID'=>['max:11'],
                'COPAY_STRATEGY_ID'=>['max:10'],
                'MESSAGE'=>['max:10'],
                'MESSAGE_STOP_DATE'=>['max:10'],
                'MIN_AGE'=>['max:6'],
                'MAX_AGE'=>['max:6'],
                'MIN_PRICE'=>['max:6'],
                'MAX_PRICE'=>['max:6'],
                'MIN_PRICE_OPT'=>['max:6'],
                'MAX_PRICE_OPT'=>['max:6'],
                'VALID_RELATION_CODE'=>['max:6'],
                'SEX_RESTRICTION'=>['max:6'],
                'MODULE_EXIT'=>['max:6'],
                'DATE_TIME_CREATED'=>['max:6'],
                'USER_ID_CREATED'=>['max:6'],
                'DATE_TIME_MODIFIED'=>['min:2','max:12'],
                'USER_ID'=>['min:2','max:12'],
                'EFFECTIVE_DATE'=>['max:6'],
                'TERMINATION_DATE'=>['max:12','min:2'],
                'REJECT_ONLY_MSG_FLAG'=>['max:12','min:2'],
                'MAX_QTY_OVER_TIME'=>['max:12|min:2'],
                'COVERAGE_START_DAYS'=>['max:6'],
                'BENEFIT_CODE'=>['max:6'],
                'SERVICE_MODIFIER'=>['max:2'],
                'DIAGNOSIS_ID'=>['max:1'],
                'MAX_RX_QTY_OPT'=>['max:1'],
                'PROVIDER_TYPE'=>['max:1'],
                'UCR'=>['numeric|max:6'],
                'PROC_CODE_LIST_ID'=>['numeric|max:6'],
                'RX_QTY_OPT_MULTIPLIER'=>['numeric|max:6'],


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Procedure Exception Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('PROCEDURE_EXCEPTION_NAMES')->insert(
                    [
                        'procedure_exception_list' => $request->procedure_exception_list,
                        'exception_name'=>$request->exception_name,
                        
                    ]
                );
    
                $add = DB::table('PROCEDURE_EXCEPTION_LISTS')
                ->insert(
                    [
                        'procedure_exception_list'=>$request->procedure_exception_list,
                        'accum_bene_strategy_id'=>$request->accum_bene_strategy_id,
                        'benefit_code'=>$request->benefit_code,
                        'copay_strategy_id'=>$request->copay_strategy_id,
                        'coverage_start_days'=>$request->coverage_start_days,
                        'diagnosis_id'=>$request->diagnosis_id,
                        'diagnosis_list'=>$request->diagnosis_list,
                        'max_price'=>$request->max_price,
                        'max_price_opt'=>$request->max_price_opt,
                        'module_exit'=>$request->module_exit,
                        'new_claim_status'=>$request->new_claim_status,
                        'physician_list'=>$request->physician_list,
                        'physician_specialty_list'=>$request->physician_specialty_list,
                        'pricing_strategy_id'=>$request->pricing_strategy_id,
                        'proc_code_list_id'=>$request->proc_code_list_id,
                        'process_rule'=>$request->process_rule,
                        'provider_type'=>$request->provider_type,
                        'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                        'rx_qty_opt_multiplier'=>$request->rx_qty_opt_multiplier,
                        'service_modifier'=>$request->service_modifier,
                        'service_type'=>$request->service_type,
                        'sex_restriction'=>$request->sex_restriction,
                        'valid_relation_code'=>$request->valid_relation_code,
                        'effective_date' => $request->effective_date,
                        'termination_date' => $request->termination_date,
                        'message_stop_date' => $request->message_stop_date,
                        'message'=>$request->message,
                        'min_price'=>$request->min_price,
                        'max_age'=>$request->max_age,
                        'min_age'=>$request->min_age,
                        'max_qty_over_time'=>$request->max_qty_over_time,
                        'ucr'=>$request->ucr,
                    ]);
                   
    
                $add = DB::table('PROCEDURE_EXCEPTION_LISTS')->where('procedure_exception_list', 'like', '%' . $request->procedure_exception_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                "exception_name" => ['max:36'],
                "NEW_CLAIM_STATUS"=>['max:2'],
                "SERVICE_TYPE"=>['max:1'],
                'PROCESS_RULE'=>['max:15','min:5'],
                'PHYSICIAN_LIST'=>['max:10'],
                'PHYSICIAN_SPECIALTY_LIST'=>['max:10'],
                'DIAGNOSIS_LIST'=>['max:10'],
                'PRICING_STRATEGY_ID'=>['max:11'],
                'ACCUM_BENE_STRATEGY_ID'=>['max:11'],
                'COPAY_STRATEGY_ID'=>['max:10'],
                'MESSAGE'=>['max:10'],
                'MESSAGE_STOP_DATE'=>['max:10'],
                'MIN_AGE'=>['max:6'],
                'MAX_AGE'=>['max:6'],
                'MIN_PRICE'=>['max:6'],
                'MAX_PRICE'=>['max:6'],
                'MIN_PRICE_OPT'=>['max:6'],
                'MAX_PRICE_OPT'=>['max:6'],
                'VALID_RELATION_CODE'=>['max:6'],
                'SEX_RESTRICTION'=>['max:6'],
                'MODULE_EXIT'=>['max:6'],
                'DATE_TIME_CREATED'=>['max:6'],
                'USER_ID_CREATED'=>['max:6'],
                'DATE_TIME_MODIFIED'=>['min:2','max:12'],
                'USER_ID'=>['min:2','max:12'],
                'EFFECTIVE_DATE'=>['max:6'],
                'TERMINATION_DATE'=>['max:12','min:2'],
                'REJECT_ONLY_MSG_FLAG'=>['max:12','min:2'],
                'MAX_QTY_OVER_TIME'=>['max:12|min:2'],
                'COVERAGE_START_DAYS'=>['max:6'],
                'BENEFIT_CODE'=>['max:6'],
                'SERVICE_MODIFIER'=>['max:2'],
                'DIAGNOSIS_ID'=>['max:1'],
                'MAX_RX_QTY_OPT'=>['max:1'],
                'PROVIDER_TYPE'=>['max:1'],
                'UCR'=>['numeric|max:6'],
                'PROC_CODE_LIST_ID'=>['numeric|max:6'],
                'RX_QTY_OPT_MULTIPLIER'=>['numeric|max:6'],


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }
    
                $update_names = DB::table('PROCEDURE_EXCEPTION_NAMES')
                ->where('procedure_exception_list', $request->procedure_exception_list )
                ->first();
                    
    
                $checkGPI = DB::table('PROCEDURE_EXCEPTION_LISTS')
                ->where('procedure_exception_list',$request->procedure_exception_list)
                ->where('proc_code_list_id',$request->proc_code_list_id)
                ->where('service_modifier',$request->service_modifier)
                ->where('benefit_code',$request->benefit_code)
                ->where('diagnosis_list',$request->diagnosis_list)
                ->where('provider_type',$request->provider_type)
                ->where('service_type',$request->service_type)
                ->where('effective_date',$request->effective_date)

                    ->get()
                    ->count();
                    // dd($checkGPI);
                // if result >=1 then update NDC_EXCEPTION_LISTS table record
                //if result 0 then add NDC_EXCEPTION_LISTS record

    
                if ($checkGPI <= "0") {
                    $update = DB::table('PROCEDURE_EXCEPTION_LISTS')
                    ->insert(
                        [
                            'procedure_exception_list'=>$request->procedure_exception_list,
                            'accum_bene_strategy_id'=>$request->accum_bene_strategy_id,
                            'benefit_code'=>$request->benefit_code,
                            'copay_strategy_id'=>$request->copay_strategy_id,
                            'coverage_start_days'=>$request->coverage_start_days,
                            'diagnosis_id'=>$request->diagnosis_id,
                            'diagnosis_list'=>$request->diagnosis_list,
                            'max_price'=>$request->max_price,
                            'max_price_opt'=>$request->max_price_opt,
                            'module_exit'=>$request->module_exit,
                            'new_claim_status'=>$request->new_claim_status,
                            'physician_list'=>$request->physician_list,
                            'physician_specialty_list'=>$request->physician_specialty_list,
                            'pricing_strategy_id'=>$request->pricing_strategy_id,
                            'proc_code_list_id'=>$request->proc_code_list_id,
                            'process_rule'=>$request->process_rule,
                            'provider_type'=>$request->provider_type,
                            'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                            'rx_qty_opt_multiplier'=>$request->rx_qty_opt_multiplier,
                            'service_modifier'=>$request->service_modifier,
                            'service_type'=>$request->service_type,
                            'sex_restriction'=>$request->sex_restriction,
                            'valid_relation_code'=>$request->valid_relation_code,
                            'effective_date' => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'message_stop_date' => $request->message_stop_date,
                            'message'=>$request->message,
                            'min_price'=>$request->min_price,
                            'max_age'=>$request->max_age,
                            'min_age'=>$request->min_age,
                            'max_qty_over_time'=>$request->max_qty_over_time,
                            'ucr'=>$request->ucr,
                        ]);
                    

                $update = DB::table('PROCEDURE_EXCEPTION_LISTS')->where('procedure_exception_list', 'like', '%' . $request->procedure_exception_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                } else {
  

                    $add_names = DB::table('PROCEDURE_EXCEPTION_NAMES')
                    ->where('procedure_exception_list',$request->procedure_exception_list)
                    ->update(
                        [
                            'exception_name'=>$request->exception_name,
                            
                        ]
                    );

                    $update = DB::table('PROCEDURE_EXCEPTION_LISTS' )
                    ->where('procedure_exception_list',$request->procedure_exception_list)
                ->where('proc_code_list_id',$request->proc_code_list_id)
                ->where('service_modifier',$request->service_modifier)
                ->where('benefit_code',$request->benefit_code)
                ->where('diagnosis_list',$request->diagnosis_list)
                ->where('provider_type',$request->provider_type)
                ->where('service_type',$request->service_type)
                ->where('effective_date',$request->effective_date)
                    ->update(
                        [
                            'accum_bene_strategy_id'=>$request->accum_bene_strategy_id,
                            'copay_strategy_id'=>$request->copay_strategy_id,
                            'coverage_start_days'=>$request->coverage_start_days,
                            'diagnosis_list'=>$request->diagnosis_list,
                            'max_age'=>$request->max_age,
                            'max_price'=>$request->max_price,
                            'max_price_opt'=>$request->max_price_opt,
                            'module_exit'=>$request->module_exit,
                            'new_claim_status'=>$request->new_claim_status,
                            'physician_list'=>$request->physician_list,
                            'physician_specialty_list'=>$request->physician_specialty_list,
                            'pricing_strategy_id'=>$request->pricing_strategy_id,
                            'proc_code_list_id'=>$request->proc_code_list_id,
                            'process_rule'=>$request->process_rule,
                            'reject_only_msg_flag'=>$request->reject_only_msg_flag,
                            'rx_qty_opt_multiplier'=>$request->rx_qty_opt_multiplier,
                            'sex_restriction'=>$request->sex_restriction,
                            'valid_relation_code'=>$request->valid_relation_code,
                            'termination_date' => $request->termination_date,
                            'message_stop_date' => $request->message_stop_date,
                            'message'=>$request->message,
                            'min_price'=>$request->min_price,
                            'min_age'=>$request->min_age,
                            'max_qty_over_time'=>$request->max_qty_over_time,
                            'ucr'=>$request->ucr,
                            
        
                        ]
                    );
                    $update = DB::table('PROCEDURE_EXCEPTION_LISTS')->where('procedure_exception_list', 'like', '%' . $request->ndc_exception_list . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                }
    
               

            }

           
        }
    }

    public function AllPhysicainLists(Request $request){

        $ndc = DB::table('PHYSICIAN_EXCEPTIONS')
        ->select('physician_list','exception_name')
        ->get();

         return $this->respondWithToken($this->token(), '', $ndc);

    }


   

    public function search(Request $request)
    {
        $ndc = DB::table('PROCEDURE_EXCEPTION_NAMES')
                ->select('PROCEDURE_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('PROCEDURE_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getPCList($ndcid)
    {
        $ndclist = DB::table('PROCEDURE_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->join('PROCEDURE_EXCEPTION_NAMES','PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST','=','PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST')
                ->where('PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST', 'like',$ndcid)
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getPCItemDetails(Request $request)
    {
        $ndc = DB::table('PROCEDURE_EXCEPTION_LISTS')

                    ->select('PROCEDURE_EXCEPTION_LISTS.*', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST as exception_list', 'PROCEDURE_EXCEPTION_NAMES.EXCEPTION_NAME as exception_name',
                    'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_strategy_description',
                    'CODES.DESCRIPTION AS benefit_code_description',
                    'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_description',
                    'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_NAME as pricing_strategy_description',
                    'SERVICE_TYPES.description as service_type_description',
                    'DIAGNOSIS_EXCEPTIONS.EXCEPTION_NAME as diagnosis_list_description',
                    'DIAGNOSIS_CODES.DESCRIPTION as diagnosis_code_description',
                    'PROVIDER_TYPES.DESCRIPTION as provider_type_description',
                    'PROC_CODE_LIST_NAMES.DESCRIPTION as proc_code_description',
                    'SPECIALTY_EXCEPTIONS.EXCEPTION_NAME as physician_speciality_list_description',
                    'SERVICE_MODIFIERS.DESCRIPTION as service_modifier_description',
                    'PHYSICIAN_EXCEPTIONS.EXCEPTION_NAME as physician_list_description',
                    
                    )

                    ->leftjoin('PROCEDURE_EXCEPTION_NAMES', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST', '=', 'PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST')
                    ->leftjoin('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'PROCEDURE_EXCEPTION_LISTS.ACCUM_BENE_STRATEGY_ID')
                    ->leftjoin('BENEFIT_CODES AS CODES','CODES.BENEFIT_CODE','=','PROCEDURE_EXCEPTION_LISTS.BENEFIT_CODE')
                    ->leftjoin('COPAY_STRATEGY_NAMES','COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID','=','PROCEDURE_EXCEPTION_LISTS.COPAY_STRATEGY_ID')
                    ->leftjoin('PRICING_STRATEGY_NAMES','PRICING_STRATEGY_NAMES.PRICING_STRATEGY_ID','=','PROCEDURE_EXCEPTION_LISTS.PRICING_STRATEGY_ID')
                    ->leftjoin('SERVICE_TYPES','SERVICE_TYPES.SERVICE_TYPE','=','PROCEDURE_EXCEPTION_LISTS.SERVICE_TYPE')
                    ->leftjoin('DIAGNOSIS_EXCEPTIONS','DIAGNOSIS_EXCEPTIONS.DIAGNOSIS_LIST','=','PROCEDURE_EXCEPTION_LISTS.DIAGNOSIS_LIST')
                    ->leftjoin('DIAGNOSIS_CODES','DIAGNOSIS_CODES.DIAGNOSIS_ID','=','PROCEDURE_EXCEPTION_LISTS.DIAGNOSIS_ID')
                    ->leftjoin('PROVIDER_TYPES','PROVIDER_TYPES.PROVIDER_TYPE','=','PROCEDURE_EXCEPTION_LISTS.PROVIDER_TYPE')
                    ->leftjoin('PROC_CODE_LIST_NAMES','PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID','=','PROCEDURE_EXCEPTION_LISTS.PROC_CODE_LIST_ID')
                    ->leftjoin('SPECIALTY_EXCEPTIONS','SPECIALTY_EXCEPTIONS.SPECIALTY_LIST','=','PROCEDURE_EXCEPTION_LISTS.physician_specialty_list')
                    ->leftjoin('SERVICE_MODIFIERS','SERVICE_MODIFIERS.SERVICE_MODIFIER','=','PROCEDURE_EXCEPTION_LISTS.SERVICE_MODIFIER')
                    ->leftjoin('PHYSICIAN_EXCEPTIONS','PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST','=','PROCEDURE_EXCEPTION_LISTS.PHYSICIAN_LIST')
                    ->where('PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST', $request->id1)  
                    ->where('PROCEDURE_EXCEPTION_LISTS.PROC_CODE_LIST_ID', $request->id2)  

                    // ->where('PROCEDURE_EXCEPTION_LISTS.PROC_CODE_LIST_ID', 'like', '%' . strtoupper($ndcid2). '%')
                    // ->Where('PROCEDURE_EXCEPTION_LISTS.','like','%'.$ndcid2)

                    
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
    public function delete_procedure_code(Request $request)
    {
        if (isset($request->procedure_exception_list) && ($request->accum_bene_strategy_id)) {
            $all_exceptions_lists =  DB::table('PROCEDURE_EXCEPTION_LISTS')
                ->where('PROCEDURE_EXCEPTION_LIST', $request->procedure_exception_list)
                ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->exception_name)) {

            $exception_delete =  DB::table('PROCEDURE_EXCEPTION_NAMES')
                ->where('EXCEPTION_NAME', $request->exception_name)
                ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}
