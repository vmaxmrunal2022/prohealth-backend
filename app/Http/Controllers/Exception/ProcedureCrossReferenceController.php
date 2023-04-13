<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

class ProcedureCrossReferenceController extends Controller
{
    public function ProcedureCodes(Request $request){

        $codes = DB::table('PROCEDURE_CODES')
        ->select('PROCEDURE_CODES.PROCEDURE_CODE','PROCEDURE_CODES.DESCRIPTION')
         ->get();

        return $this->respondWithToken($this->token(), '', $codes);

    }

    public function search( Request $request )
    {
           
           $entity_names = DB::table( 'ENTITY_NAMES' )
           ->where( 'ENTITY_USER_ID', 'like', '%'.$request->search.'%' )
           ->orWhere( 'ENTITY_USER_NAME', 'like', '%'.$request->search.'%' )
           ->get();
           return $this->respondWithToken( $this->token(), '', $entity_names);
    }


    public function List($id){

        $details=DB::table('PROCEDURE_XREF')
        // ->select('PROCEDURE_XREF.*','PROCEDURE_CODES1.DESCRIPTION as sub_procedure_code_description','PROCEDURE_CODES2.DESCRIPTION as hist_procedure_code_description')
        ->join('ENTITY_NAMES','ENTITY_NAMES.ENTITY_USER_ID','=','PROCEDURE_XREF.PROCEDURE_XREF_ID')
        // ->join('PROCEDURE_CODES as PROCEDURE_CODES1','PROCEDURE_CODES1.PROCEDURE_CODE','=','PROCEDURE_XREF.sub_procedure_code')
        // ->join('PROCEDURE_CODES as PROCEDURE_CODES2','PROCEDURE_CODES2.PROCEDURE_CODE','=','PROCEDURE_XREF.hist_procedure_code')
        ->where('PROCEDURE_XREF_ID',$id)

         ->get();

        return $this->respondWithToken($this->token(), '', $details);


    }

    public function getDetails($date,$subpro,$hispro,$id){

        $details=DB::table('PROCEDURE_XREF')
        ->select('PROCEDURE_XREF.*','PROCEDURE_CODES1.DESCRIPTION as sub_procedure_code_description','PROCEDURE_CODES2.DESCRIPTION as hist_procedure_code_description','ENTITY_NAMES.ENTITY_USER_NAME')
        ->join('ENTITY_NAMES','ENTITY_NAMES.ENTITY_USER_ID','=','PROCEDURE_XREF.PROCEDURE_XREF_ID')
        ->join('PROCEDURE_CODES as PROCEDURE_CODES1','PROCEDURE_CODES1.PROCEDURE_CODE','=','PROCEDURE_XREF.sub_procedure_code')
        ->join('PROCEDURE_CODES as PROCEDURE_CODES2','PROCEDURE_CODES2.PROCEDURE_CODE','=','PROCEDURE_XREF.hist_procedure_code')
        ->where('PROCEDURE_XREF.EFFECTIVE_DATE',$date)
        ->where('PROCEDURE_XREF.SUB_PROCEDURE_CODE',$subpro)
        ->where('PROCEDURE_XREF.HIST_PROCEDURE_CODE',$hispro)
        ->where('PROCEDURE_XREF.PROCEDURE_XREF_ID',$id)

         ->first();

        return $this->respondWithToken($this->token(), '', $details);


    }

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );

        $recordcheck = DB::table('PROCEDURE_XREF')
        ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
        ->first();


        if ( $request->has('new') ) {


            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Procedure Cross Reference List ID is Already Existed', $recordcheck);


            }

            else{

                $insert1 = DB::table('ENTITY_NAMES')->insert(
                    [
                        'ENTITY_TYPE' => 'PROCEDURE_XREF',
                        'ENTITY_USER_ID'=>$request->procedure_xref_id,
                        'ENTITY_USER_NAME'=>$request->entity_user_name,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'USER_ID'=>'',
                        'DATE_TIME_MODIFIED'=>$createddate,
                        
                     
                    ]
                );


                
                
                $insert = DB::table('PROCEDURE_XREF')->insert(
                    [
                        'PROCEDURE_XREF_ID' => $request->procedure_xref_id,
                        'SUB_PROCEDURE_CODE'=>$request->sub_procedure_code,
                        'HIST_PROCEDURE_CODE'=>$request->hist_procedure_code,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'TOOTH_OPT'=>$request->tooth_opt,
                        'SURFACE_OPT'=>$request->surface_opt,
                        'QUADRANT_OPT'=>$request->quadrant_opt,
                        'NEW_DRUG_STATUS'=>$request->new_drug_status,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                        
                    ]
                );
                return $this->respondWithToken( $this->token(), 'Record Added Successfully',$insert);
    
    

            }

            
           
        } else {


            $update1 = DB::table('ENTITY_NAMES' )
            ->where('ENTITY_USER_ID', $request->procedure_xref_id)
            ->update(
                [
                        'ENTITY_TYPE' => 'PROCEDURE_XREF',
                        'ENTITY_USER_NAME'=>$request->entity_user_name,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'USER_ID'=>'',
                        'DATE_TIME_MODIFIED'=>$createddate,
                    
                ]
            );


           

            $update = DB::table('PROCEDURE_XREF' )
            ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id )
            ->update(
                [
                        'SUB_PROCEDURE_CODE'=>$request->sub_procedure_code,
                        'HIST_PROCEDURE_CODE'=>$request->hist_procedure_code,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'TOOTH_OPT'=>$request->tooth_opt,
                        'SURFACE_OPT'=>$request->surface_opt,
                        'QUADRANT_OPT'=>$request->quadrant_opt,
                        'NEW_DRUG_STATUS'=>$request->new_drug_status,
                        'MESSAGE'=>$request->message,
                        'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                    
                ]
            );


            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

        }


    }
}
