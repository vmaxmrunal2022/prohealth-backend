<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrcedureCodeListController extends Controller
 {

    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        $effective_date = date('Ymd', strtotime($request->effective_date));
        $terminate_date = date('Ymd', strtotime($request->termination_date));


                $recordcheck = DB::table('PROC_CODE_LIST_NAMES')
                ->where('proc_code_list_id', strtoupper($request->proc_code_list_id))
                ->first();

               

        if ( $request->has( 'new' ) ) {


            if($recordcheck){

                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $recordcheck);


            }else{

                $accum_benfit_stat_names = DB::table( 'PROC_CODE_LIST_NAMES' )->insert(
                    [
                        'proc_code_list_id' => strtoupper($request->proc_code_list_id ),
                        'description'=>$request->description
    
                    ]
                );
    
                $accum_benfit_stat = DB::table('PROC_CODE_LISTS')->insert(
                    [
                        'proc_code_list_id'=>strtoupper($request->proc_code_list_id),
                        'procedure_code'=>$request->procedure_code,
                        'effective_date'=>$effective_date,
                        'termination_date'=>$terminate_date,
    
                    ]
                );


                if ($accum_benfit_stat) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully!!!', $accum_benfit_stat);
                }
    
    

            }

           
        } else {

            $benefitcode = DB::table( 'PROC_CODE_LIST_NAMES' )
            ->where('proc_code_list_id', strtoupper($request->proc_code_list_id))

            ->update(
                [
                    'description'=>$request->description,

                ]
            );


            $accum_benfit_stat = DB::table('PROC_CODE_LISTS' )
            ->where('proc_code_list_id', $request->proc_code_list_id )
            ->where('procedure_code',$request->procedure_code)
            ->update(
                [
                    'effective_date'=>$effective_date,
                    'termination_date'=>$terminate_date,                  

                ]
            );


            $benefitcode = DB::table( 'PROC_CODE_LISTS' )->where( 'proc_code_list_id', 'like', $request->proc_code_list_id )->first();

        }

        return $this->respondWithToken( $this->token(), 'Record Updated Successfully', $benefitcode );
    }

    public function get( Request $request )
 {
        ;
        $providerCodeList = DB::table( 'PROC_CODE_LIST_NAMES' )
        ->where( 'PROC_CODE_LIST_ID', 'like', '%'.strtoupper( $request->search ).'%' )
        ->orWhere( 'DESCRIPTION', 'like', '%'.strtoupper( $request->search ).'%' )
        ->get();
        return $this->respondWithToken( $this->token(), '', $providerCodeList );
    }

    public function getProcCodeList( Request $request )
 {
        $providerCodeList = DB::table( 'PROC_CODE_LISTS' )
        ->join( 'PROC_CODE_LIST_NAMES', 'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID', '=', 'PROC_CODE_LISTS.PROC_CODE_LIST_ID' )
        ->where( 'PROC_CODE_LISTS.PROC_CODE_LIST_ID', 'like', '%'.strtoupper( $request->search ).'%' )
        ->get();
        return $this->respondWithToken( $this->token(), '', $providerCodeList );
    }
}
