<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
           ->select('ENTITY_NAMES.ENTITY_USER_ID as procedure_xref_id','ENTITY_NAMES.entity_user_name')
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

    public function getDetails($PROCEDURE_XREF_ID,$SUB_PROCEDURE_CODE,$HIST_PROCEDURE_CODE,$EFFECTIVE_DATE){

        $details=DB::table('PROCEDURE_XREF')
        ->select('PROCEDURE_XREF.*','PROCEDURE_CODES1.DESCRIPTION as sub_procedure_code_description','PROCEDURE_CODES2.DESCRIPTION as hist_procedure_code_description','ENTITY_NAMES.ENTITY_USER_NAME')
        ->join('ENTITY_NAMES','ENTITY_NAMES.ENTITY_USER_ID','=','PROCEDURE_XREF.PROCEDURE_XREF_ID')
        ->join('PROCEDURE_CODES as PROCEDURE_CODES1','PROCEDURE_CODES1.PROCEDURE_CODE','=','PROCEDURE_XREF.sub_procedure_code')
        ->join('PROCEDURE_CODES as PROCEDURE_CODES2','PROCEDURE_CODES2.PROCEDURE_CODE','=','PROCEDURE_XREF.hist_procedure_code')
        ->where('PROCEDURE_XREF.EFFECTIVE_DATE',$EFFECTIVE_DATE)
        ->where('PROCEDURE_XREF.SUB_PROCEDURE_CODE',$SUB_PROCEDURE_CODE)
        ->where('PROCEDURE_XREF.HIST_PROCEDURE_CODE',$HIST_PROCEDURE_CODE)
        ->where('PROCEDURE_XREF.PROCEDURE_XREF_ID',$PROCEDURE_XREF_ID)

         ->first();

        return $this->respondWithToken($this->token(), '', $details);


    }

    public function addcopy( Request $request ) {

       

        $createddate = date( 'y-m-d' );

        $recordcheck = DB::table('PROCEDURE_XREF')
        ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
        ->first();


        if ( $request->has('new') ) {


            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Procedure Cross Reference List ID is Already Exists', $recordcheck);


            }

            else{

                $insert1 = DB::table('ENTITY_NAMES')
                ->insert(
                    [
                        'ENTITY_TYPE' => 'PROCEDURE_XREF',
                        'ENTITY_USER_ID'=>$request->procedure_xref_id,
                        'ENTITY_USER_NAME'=>$request->entity_user_name,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'DATE_TIME_MODIFIED'=>$createddate,
                     
                    ]
                );


                
                
                $insert = DB::table('PROCEDURE_XREF')
                
                ->insert(
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

    public function add(Request $request)
    {
// dd('test');
        $createddate = date( 'y-m-d' );
        $validation = DB::table('ENTITY_NAMES')
        ->where('ENTITY_USER_ID',$request->procedure_xref_id)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'procedure_xref_id' => ['required', 'max:10', Rule::unique('ENTITY_NAMES')->where(function ($q) {
                    $q->whereNotNull('procedure_xref_id');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('ENTITY_NAMES')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "entity_user_name" => ['max:36'],
                "date_time_created"=>['max:10'],
                "procedure_xref_id" => ['required','max:36'],
                'effective_date'=>['required'],
                'termination_date'=>['required','after:effective_date'],  
               
            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Procedure Cross Reference List Id Already Exists', $validation, true, 200, 1);
                }
                $effectiveDate=$request->effective_date;
                $terminationDate=$request->termination_date;
                $overlapExists = DB::table('PROCEDURE_XREF')
                ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
                ->where(function ($query) use ($effectiveDate, $terminationDate) {
                    $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                        ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                        ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                            $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                                ->where('TERMINATION_DATE', '>=', $terminationDate);
                        });
                })
                ->exists();
                if ($overlapExists) {
                    // return redirect()->back()->withErrors(['overlap' => 'Date overlap detected.']);
                    return $this->respondWithToken($this->token(), 'For Same Submitted Procedure Code And History Procedure Code,dates cannot overlap.', $validation, true, 200, 1);
                }
                $add_names = DB::table('ENTITY_NAMES')
                ->insert(
                    [
                        'ENTITY_TYPE' => 'PROCEDURE_XREF',
                        'ENTITY_USER_ID'=>$request->procedure_xref_id,
                        'ENTITY_USER_NAME'=>$request->entity_user_name,
                        'DATE_TIME_CREATED'=>$createddate,
                        'USER_ID_CREATED'=>'',
                        'DATE_TIME_MODIFIED'=>$createddate,
                        
                    ]
                );
    
                $add = DB::table('PROCEDURE_XREF')
                ->insert(
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

                   
                $add = DB::table('PROCEDURE_XREF')->where('PROCEDURE_XREF_ID', 'like', '%' . $request->procedure_xref_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [
                // 'procedure_xref_id' => ['required', 'max:10', Rule::unique('ENTITY_NAMES')->where(function ($q) {
                //     $q->whereNotNull('procedure_xref_id');
                // })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('ENTITY_NAMES')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "entity_user_name" => ['max:36'],
                "date_time_created"=>['max:10'],
                // "procedure_xref_id" => ['required','max:36'],
                'effective_date'=>['required'],
                'termination_date'=>['required','after:effective_date'],  
               
            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }

                
                // $effectiveDate=$request->effective_date;
                // $terminationDate=$request->termination_date;
                // $overlapExists = DB::table('PROCEDURE_XREF')
                // ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
                // ->where('EFFECTIVE_DATE', '!=',$effectiveDate)
                // ->where(function ($query) use ($effectiveDate, $terminationDate) {
                //     $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                //         ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                //         ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                //             $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                //                 ->where('TERMINATION_DATE', '>=', $terminationDate);
                //         });
                // })
                // ->exists();
                // if ($overlapExists) {
                //     return $this->respondWithToken($this->token(), 'For Same Submitted Procedure Code And History Procedure Code,dates cannot overlap.', $validation, true, 200, 1);
                // }


                if($request->update_new == 0){

                    $update = DB::table('PROCEDURE_XREF' )
                    ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
                    ->where('SUB_PROCEDURE_CODE',$request->sub_procedure_code)
                    ->where('HIST_PROCEDURE_CODE',$request->hist_procedure_code)
                    ->where('EFFECTIVE_DATE',$request->effective_date)
                    ->update(
                        [
                               
                                'TERMINATION_DATE'=>$request->termination_date,
                                'TOOTH_OPT'=>$request->tooth_opt,
                                'SURFACE_OPT'=>$request->surface_opt,
                                'QUADRANT_OPT'=>$request->quadrant_opt,
                                'NEW_DRUG_STATUS'=>$request->new_drug_status,
                                'MESSAGE'=>$request->message,
                                'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                            
                        ]
                    );  
                    
                    $update = DB::table('PROCEDURE_XREF')->where('PROCEDURE_XREF_ID', 'like', '%' . $request->procedure_xref_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);

                }
                elseif($request->update_new == 1){
                    $checkGPI = DB::table('PROCEDURE_XREF')
                    ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
                    ->where('SUB_PROCEDURE_CODE',$request->sub_procedure_code)
                    ->where('HIST_PROCEDURE_CODE',$request->hist_procedure_code)
                    ->where('EFFECTIVE_DATE',$request->effective_date)
                    ->get();

                    if(count($checkGPI) >= 1){
                        return $this->respondWithToken($this->token(), [["Submitted Procedure Code already exists"]], '', 'false');
                    }else{
                        $update = DB::table('PROCEDURE_XREF')
                        ->insert(
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
                      
                        $update = DB::table('PROCEDURE_XREF')->where('PROCEDURE_XREF_ID', 'like', '%' . $request->procedure_xref_id . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }
                }
    
                // $update_names = DB::table('ENTITY_NAMES')
                // ->where('ENTITY_USER_ID', $request->procedure_xref_id)
                // ->first();
                    
    
                // $checkGPI = DB::table('PROCEDURE_XREF')
                //     ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
                //     ->where('SUB_PROCEDURE_CODE',$request->sub_procedure_code)
                //     ->where('HIST_PROCEDURE_CODE',$request->hist_procedure_code)
                //     ->where('EFFECTIVE_DATE',$request->effective_date)
                //     ->get()
                //     ->count();
                //     // dd($checkGPI);
                // // if result >=1 then update NDC_EXCEPTION_LISTS table record
                // //if result 0 then add NDC_EXCEPTION_LISTS record

    
                // if ($checkGPI <= "0") {
                   
                //     $update = DB::table('PROCEDURE_XREF')
                //     ->insert(
                //         [
                //             'PROCEDURE_XREF_ID' => $request->procedure_xref_id,
                //             'SUB_PROCEDURE_CODE'=>$request->sub_procedure_code,
                //             'HIST_PROCEDURE_CODE'=>$request->hist_procedure_code,
                //             'EFFECTIVE_DATE'=>$request->effective_date,
                //             'TERMINATION_DATE'=>$request->termination_date,
                //             'TOOTH_OPT'=>$request->tooth_opt,
                //             'SURFACE_OPT'=>$request->surface_opt,
                //             'QUADRANT_OPT'=>$request->quadrant_opt,
                //             'NEW_DRUG_STATUS'=>$request->new_drug_status,
                //             'MESSAGE'=>$request->message,
                //             'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                            
                //         ]
                //     );
                  
                // $update = DB::table('PROCEDURE_XREF')->where('PROCEDURE_XREF_ID', 'like', '%' . $request->procedure_xref_id . '%')->first();
                // return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                // } else {
  

                //     $add_names = DB::table('ENTITY_NAMES')
                //     ->where('ENTITY_USER_ID',$request->procedure_xref_id)
                //     ->update(
                //         [
                //             'entity_user_name'=>$request->entity_user_name,
                            
                //         ]
                //     );

                //     $update = DB::table('PROCEDURE_XREF' )
                //     ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
                //     ->where('SUB_PROCEDURE_CODE',$request->sub_procedure_code)
                //     ->where('HIST_PROCEDURE_CODE',$request->hist_procedure_code)
                //     ->where('EFFECTIVE_DATE',$request->effective_date)
                //     ->update(
                //         [
                               
                //                 'TERMINATION_DATE'=>$request->termination_date,
                //                 'TOOTH_OPT'=>$request->tooth_opt,
                //                 'SURFACE_OPT'=>$request->surface_opt,
                //                 'QUADRANT_OPT'=>$request->quadrant_opt,
                //                 'NEW_DRUG_STATUS'=>$request->new_drug_status,
                //                 'MESSAGE'=>$request->message,
                //                 'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                            
                //         ]
                //     );  
                    
                //     $update = DB::table('PROCEDURE_XREF')->where('PROCEDURE_XREF_ID', 'like', '%' . $request->procedure_xref_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                // }
    
               

            }

           
        }
    }

    public function delete(Request $request)
    {
       
        if (isset($request->procedure_xref_id) && isset($request->sub_procedure_code) && isset($request->hist_procedure_code) && isset($request->termination_date) && isset($request->effective_date)) {
     
            $all_exceptions_lists =  DB::table('PROCEDURE_XREF')
                ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
                ->where('SUB_PROCEDURE_CODE', $request->sub_procedure_code)
                ->where('HIST_PROCEDURE_CODE', $request->hist_procedure_code)
                ->where('EFFECTIVE_DATE', str_replace('-', '', $request->effective_date))
                ->where('TERMINATION_DATE',str_replace('-', '', $request->termination_date))
                ->delete();
              
           
            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->procedure_xref_id)) {
           
            $exception_delete =  DB::table('ENTITY_NAMES')
                ->where('ENTITY_USER_ID', $request->procedure_xref_id)
                ->delete();
            $all_exceptions_lists =  DB::table('PROCEDURE_XREF')
            ->where('PROCEDURE_XREF_ID', $request->procedure_xref_id)
            ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}
