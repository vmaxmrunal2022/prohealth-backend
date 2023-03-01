<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ReasonCodeExceptionController extends Controller
{



    public function add( Request $request ) {



        $createddate = date('y-m-d' );
        $effective_date = date('Ymd', strtotime($request->effective_date));
            $terminate_date = date('Ymd', strtotime($request->termination_date));


            $check = DB::table('REASON_CODE_LISTS')
            ->where('REASON_CODE_LIST_ID',strtoupper($request->reason_code_list_id))
            ->first();
            

        if ( $request->has( 'new' ) ) {

            if($check){
             return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $check);

            }
            else{

                $accum_benfit_stat = DB::table('REASON_CODE_LISTS')->insert(
                    [   'reason_code_list_id'=>strtoupper($request->reason_code_list_id),
                        'reject_code' => $request->reject_code,
                        'reason_code'=>$request->reason_code,
                        'effective_date'=>$effective_date,
                        'termination_date'=>$terminate_date,
                        'date_time_created'=>$createddate,
                        'date_time_modified'=>$createddate,
                    ]
                );
    
    
                $accum_benfit_stat = DB::table('REASON_CODE_LIST_NAMES')->insert(
                    [
    
                        'reason_code_list_id'=>strtoupper($request->reason_code_list_id),
                        'reason_code_name'=>$request->reason_code_name,
                        'date_time_created'=>$createddate,
    
    
                    ]
                );
    
                $benefitcode = DB::table('REASON_CODE_LISTS' )->where('reason_code_list_id', 'like', $request->reason_code_list_id)->first();
    
                return $this->respondWithToken( $this->token(), 'Record Added Successfully',$benefitcode);

            }


           


        }else{

          
            
    
            
            $createddate = DB::table('REASON_CODE_LISTS')
            ->where('reason_code_list_id', $request->reason_code_list_id )
            ->update(
                [
                    'reject_code' => $request->reject_code,
                    'reason_code'=>$request->reason_code,
                    'effective_date'=>$effective_date,
                    'termination_date'=>$terminate_date,
                    'date_time_created'=>$createddate,
                    'date_time_modified'=>$createddate,

                ]
            );


            $reason_code_names = DB::table('REASON_CODE_LIST_NAMES')
            ->where('reason_code_list_id', $request->reason_code_list_id )
            ->update(
                [
                    'reason_code_name'=>$request->reason_code_name,

                ]
            );

        $benefitcode = DB::table('REASON_CODE_LIST_NAMES')->where('reason_code_name', 'like', '%'.$request->reason_code_name .'%')->first();

        }

       
        return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$benefitcode);
    }

    
    
    public function search(Request $request)

    {
        $ndc =DB::table('REASON_CODE_LISTS')
        ->join('REASON_CODE_LIST_NAMES', 'REASON_CODE_LISTS.REASON_CODE_LIST_ID', '=', 'REASON_CODE_LIST_NAMES.REASON_CODE_LIST_ID')
                ->where('REASON_CODE_LIST_NAMES.REASON_CODE_LIST_ID', 'like', '%' .strtoupper($request->search). '%')
                ->orWhere('REASON_CODE_LISTS.REASON_CODE_LIST_ID', 'like', '%' .strtoupper($request->search). '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('REASON_CODE_LISTS')
                    // ->select('NDC_EXCEPTION_LISTS.*', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST as exception_list', 'NDC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->join('REASON_CODE_LIST_NAMES', 'REASON_CODE_LISTS.REASON_CODE_LIST_ID', '=', 'REASON_CODE_LIST_NAMES.REASON_CODE_LIST_ID')
                    ->where('REASON_CODE_LIST_NAMES.REASON_CODE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}