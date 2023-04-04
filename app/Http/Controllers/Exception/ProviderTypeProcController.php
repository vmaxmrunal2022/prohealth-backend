<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ProviderTypeProcController extends Controller
{
    public function search(Request $request)
    {
        $data = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
                ->select('PROV_TYPE_PROC_ASSOC_ID', 'DESCRIPTION')
                ->where('PROV_TYPE_PROC_ASSOC_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->where('PROV_TYPE_PROC_ASSOC_ID', 'like', '%' . $request->search. '%')

                ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $data);
    }

    public function getList($id){

        $data = DB::table('PROV_TYPE_PROC_ASSOC')
        ->join('PROV_TYPE_PROC_ASSOC_NAMES','PROV_TYPE_PROC_ASSOC_NAMES.PROV_TYPE_PROC_ASSOC_ID','=','PROV_TYPE_PROC_ASSOC.PROV_TYPE_PROC_ASSOC_ID')
        ->where('PROV_TYPE_PROC_ASSOC.prov_type_proc_assoc_id', 'like', '%' . strtoupper($id) . '%')
        ->get();
        return $this->respondWithToken($this->token(), '', $data);


    }

    public function getDetails($id){

        $Details = DB::table('PROV_TYPE_PROC_ASSOC')


->select('PROV_TYPE_PROC_ASSOC.*',
'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_strategy_description',
'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name_description',
'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_NAME as pricing_strategy_name_description',
'PROC_CODE_LIST_NAMES.DESCRIPTION as proc_code_list_description',
'PROV_TYPE_PROC_ASSOC_NAMES.DESCRIPTION as description',
'SERVICE_MODIFIERS.DESCRIPTION as service_modifier_description',
'PROVIDER_TYPES.DESCRIPTION as provider_type_description'
)
        ->leftjoin('ACCUM_BENE_STRATEGY_NAMES','ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID','=','PROV_TYPE_PROC_ASSOC.ACCUM_BENE_STRATEGY_ID')
        ->leftjoin('COPAY_STRATEGY_NAMES','COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID','=','PROV_TYPE_PROC_ASSOC.COPAY_STRATEGY_ID')
        ->leftjoin('PRICING_STRATEGY_NAMES','PRICING_STRATEGY_NAMES.PRICING_STRATEGY_ID','=','PROV_TYPE_PROC_ASSOC.PRICING_STRATEGY_ID')
        ->leftjoin('PROC_CODE_LIST_NAMES','PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID','=','PROV_TYPE_PROC_ASSOC.PROC_CODE_LIST_ID')
        ->leftjoin('PROV_TYPE_PROC_ASSOC_NAMES','PROV_TYPE_PROC_ASSOC_NAMES.PROV_TYPE_PROC_ASSOC_ID','=','PROV_TYPE_PROC_ASSOC.PROV_TYPE_PROC_ASSOC_ID')
        ->leftjoin('SERVICE_MODIFIERS','SERVICE_MODIFIERS.SERVICE_MODIFIER','=','PROV_TYPE_PROC_ASSOC.SERVICE_MODIFIER')
        ->leftjoin('PROVIDER_TYPES','PROVIDER_TYPES.PROVIDER_TYPE','=','PROV_TYPE_PROC_ASSOC.PROVIDER_TYPE')

    
        ->where('PROV_TYPE_PROC_ASSOC.PROV_TYPE_PROC_ASSOC_ID',$id)
        ->first();
        return $this->respondWithToken($this->token(), '', $Details);


    }


    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );

        $recordcheck = DB::table('PROV_TYPE_PROC_ASSOC')
        ->where('prov_type_proc_assoc_id', strtoupper($request->prov_type_proc_assoc_id))
        ->first();


        if ( $request->has('new') ) {


            if($recordcheck){
                return $this->respondWithToken($this->token(), 'ProviderType Id already exists in the system..!!!', $recordcheck);


            }

            else{

                $insert1 = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')->insert(
                    [
                        'PROV_TYPE_PROC_ASSOC_ID' => strtoupper($request->prov_type_proc_assoc_id),
                        'DESCRIPTION'=>$request->description,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'USER_ID'=>'',
                        'DATE_TIME_MODIFIED'=>$createddate,
                        
                     
                    ]
                );


                
                
                $insert = DB::table('PROV_TYPE_PROC_ASSOC')->insert(
                    [
                        'PROV_TYPE_PROC_ASSOC_ID' => strtoupper($request->prov_type_proc_assoc_id),
                        'PROVIDER_TYPE'=>$request->provider_type,
                        'SERVICE_MODIFIER'=>$request->service_modifier,
                        'UCR'=>$request->ucr,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>'',
                        'USER_ID_CREATED'=>'',
                        'USER_ID'=>'',
                        'DATE_TIME_MODIFIED'=>'',
                        'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                        'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                        'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                        'MIN_AGE'=>$request->min_age,
                        'MAX_AGE'=>$request->max_age,
                        'MIN_PRICE'=>$request->min_price,
                        'MAX_PRICE'=>$request->max_price,
                        'MIN_PRICE_OPT'=>$request->min_price_opt,
                        'MAX_PRICE_OPT'=>$request->max_price_opt,
                        'VALID_RELATION_CODE'=>$request->valid_relation_code,
                        'SEX_RESTRICTION'=>$request->sex_restriction,
                        'MODULE_EXIT'=>$request->module_exit,
                        'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                        'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                        'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                        'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                        'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                        'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                        

                     
                    ]
                );
                return $this->respondWithToken( $this->token(), 'Record Added Successfully',$insert);
    
    

            }

            
           
        } else {


            $update1 = DB::table('PROV_TYPE_PROC_ASSOC_NAMES' )
            ->where('PROV_TYPE_PROC_ASSOC_ID', strtoupper($request->prov_type_proc_assoc_id ))
            ->update(
                [
                    'DESCRIPTION'=>$request->description,
                    'DATE_TIME_CREATED'=>'',
                    'USER_ID_CREATED'=>'',
                    'USER_ID'=>''
                   
                    
                ]
            );


           

            $update = DB::table('PROV_TYPE_PROC_ASSOC' )
            ->where('PROV_TYPE_PROC_ASSOC_ID', strtoupper($request->prov_type_proc_assoc_id ))
            ->update(
                [
                    'PROVIDER_TYPE'=>$request->provider_type,
                    'SERVICE_MODIFIER'=>$request->service_modifier,
                    'UCR'=>$request->ucr,
                    'EFFECTIVE_DATE'=>$request->effective_date,
                    'TERMINATION_DATE'=>$request->termination_date,
                    'DATE_TIME_CREATED'=>'',
                    'USER_ID_CREATED'=>'',
                    'USER_ID'=>'',
                    'DATE_TIME_MODIFIED'=>'',
                    'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                    'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                    'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                    'MESSAGE'=>$request->message,
                    'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                    'MIN_AGE'=>$request->min_age,
                    'MAX_AGE'=>$request->max_age,
                    'MIN_PRICE'=>$request->min_price,
                    'MAX_PRICE'=>$request->max_price,
                    'MIN_PRICE_OPT'=>$request->min_price_opt,
                    'MAX_PRICE_OPT'=>$request->max_price_opt,
                    'VALID_RELATION_CODE'=>$request->valid_relation_code,
                    'SEX_RESTRICTION'=>$request->sex_restriction,
                    'MODULE_EXIT'=>$request->module_exit,
                    'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                    'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                    'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                    'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                    'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                    'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                    
                ]
            );


            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

        }


    }

}
