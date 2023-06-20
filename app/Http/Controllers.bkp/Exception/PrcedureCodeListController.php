<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class PrcedureCodeListController extends Controller
 {

    public function addcopy( Request $request ) {
        $createddate = date( 'y-m-d' );

        $effective_date = date('Ymd', strtotime($request->effective_date));
        $terminate_date = date('Ymd', strtotime($request->termination_date));


                $recordcheck = DB::table('PROC_CODE_LIST_NAMES')
                ->where('proc_code_list_id', $request->proc_code_list_id)
                ->first();

               

        if ( $request->has( 'new' ) ) {


            if($recordcheck){

                return $this->respondWithToken($this->token(), 'Procedure Code List ID Already Exists', $recordcheck);


            }else{

                $accum_benfit_stat_names = DB::table( 'PROC_CODE_LIST_NAMES' )->insert(
                    [
                        'proc_code_list_id' => $request->proc_code_list_id ,
                        'description'=>$request->description
    
                    ]
                );
    
                $accum_benfit_stat = DB::table('PROC_CODE_LISTS')->insert(
                    [
                        'proc_code_list_id'=>$request->proc_code_list_id,
                        'procedure_code'=>$request->procedure_code,
                        'effective_date'=>$effective_date,
                        'termination_date'=>$terminate_date,
    
                    ]
                );


                if ($accum_benfit_stat) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $accum_benfit_stat);
                }
    
    

            }

           
        } else {

            $benefitcode = DB::table( 'PROC_CODE_LIST_NAMES' )
            ->where('proc_code_list_id', $request->proc_code_list_id)

            ->update(
                [
                    'description'=>$request->description,

                ]
            );


            $accum_benfit_stat = DB::table('PROC_CODE_LISTS' )
            ->where('proc_code_list_id', $request->proc_code_list_id )
            // ->where('procedure_code',$request->procedure_code)
            ->update(
                [

                    'procedure_code'=>$request->procedure_code,

                    'effective_date'=>$effective_date,
                    'termination_date'=>$terminate_date,                  

                ]
            );


            $benefitcode = DB::table( 'PROC_CODE_LISTS' )->where( 'proc_code_list_id', 'like', $request->proc_code_list_id )->first();

        }

        return $this->respondWithToken( $this->token(), 'Record Updated Successfully', $benefitcode );
    }


    public function add(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('PROC_CODE_LIST_NAMES')
        ->where('proc_code_list_id',$request->proc_code_list_id)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'proc_code_list_id' => ['required', 'max:10', Rule::unique('PROC_CODE_LIST_NAMES')->where(function ($q) {
                    $q->whereNotNull('proc_code_list_id');
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

                "description"=>['required','max:36'],
                "effective_date"=>['required','max:10'],
                'termination_date'=>['required','max:10','after:effective_date'],
                'procedure_code'=>['required','max:10'],
                

            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Procedure Exception Already Exists', $validation, true, 200, 1);
                }

                $effectiveDate=$request->effective_date;
                $terminationDate=$request->termination_date;
                $overlapExists = DB::table('PROC_CODE_LISTS')
                ->where('PROC_CODE_LIST_ID', $request->proc_code_list_id)
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
                    return $this->respondWithToken($this->token(), 'For Same  Procedure Code ,dates cannot overlap.', $validation, true, 200, 1);
                }

                $add_names = DB::table('PROC_CODE_LIST_NAMES')->insert(
                    [
                        'proc_code_list_id' => $request->proc_code_list_id,
                        'description'=>$request->description,
                        
                    ]
                );
    
                $add = DB::table('PROC_CODE_LISTS')
                ->insert(
                    [
                        'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                        'PROCEDURE_CODE'=>$request->procedure_code,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>$createddate,
                        
                        
                    ]);
                   
    
                $add = DB::table('PROC_CODE_LISTS')->where('proc_code_list_id', 'like', '%' . $request->proc_code_list_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [
                "proc_code_list_id" => ['required','max:36'],
                "description"=>['required','max:36'],
                "effective_date"=>['required','max:10'],
                'termination_date'=>['required','max:10','after:effective_date'],
                'procedure_code'=>['required','max:10'],
                
                

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


                
            if($request->update_new == 0){

                $effectiveDate=$request->effective_date;
                $terminationDate=$request->termination_date;
                $overlapExists = DB::table('PROC_CODE_LISTS')
                ->where('PROC_CODE_LIST_ID', $request->proc_code_list_id)
                ->where('procedure_code',$request->procedure_code)
                ->where('effective_date','!=',$request->effective_date)
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
                    return $this->respondWithToken($this->token(), [['For Same  Procedure Code , dates cannot overlap.']], '', 'false');
                }

                $add_names = DB::table('PROC_CODE_LIST_NAMES')
                ->where('proc_code_list_id',$request->proc_code_list_id)
                ->update(
                    [
                        'description'=>$request->description,
                        
                    ]
                );

                $update = DB::table('PROC_CODE_LISTS' )
                ->where('proc_code_list_id', $request->proc_code_list_id )
                ->where('procedure_code',$request->procedure_code)
                ->where('effective_date',$request->effective_date)
                ->update(
                    [
                        'TERMINATION_DATE'=>$request->termination_date,
                        
    
                    ]
                );
                $update = DB::table('PROC_CODE_LISTS')->where('proc_code_list_id', 'like', '%' . $request->proc_code_list_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
            }elseif($request->update_new == 1){

                $checkGPI = DB::table('PROC_CODE_LISTS')
                ->where('proc_code_list_id', $request->proc_code_list_id )
                ->where('procedure_code',$request->procedure_code)
                ->where('effective_date',$request->effective_date)
                ->get();
                if(count($checkGPI) >= 1){
                    // return $this->respondWithToken($this->token(), 'Record Already  Exists',$checkGPI,'false');
                    return $this->respondWithToken($this->token(), [['For Same  Procedure Code , dates cannot overlap.']], '', 'false');
                }else{

                    $effectiveDate=$request->effective_date;
                    $terminationDate=$request->termination_date;
                    $overlapExists = DB::table('PROC_CODE_LISTS')
                    ->where('PROC_CODE_LIST_ID', $request->proc_code_list_id)
                    ->where('procedure_code',$request->procedure_code)
                    // ->where('effective_date','!=',$request->effective_date)
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
                        return $this->respondWithToken($this->token(), [['For Same  Procedure Code , dates cannot overlap.']], '', 'false');
                    }
                    $update = DB::table('PROC_CODE_LISTS')
                        ->insert(
                            [
                                'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                                'PROCEDURE_CODE'=>$request->procedure_code,
                                'EFFECTIVE_DATE'=>$request->effective_date,
                                'TERMINATION_DATE'=>$request->termination_date,
                                'DATE_TIME_CREATED'=>$createddate,
                            ]);
                    $update = DB::table('PROC_CODE_LISTS')->where('proc_code_list_id', 'like', '%' . $request->proc_code_list_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $update); 
                }
                
            }


                // $update_names = DB::table('PROC_CODE_LIST_NAMES')
                // ->where('proc_code_list_id', $request->proc_code_list_id )
                // ->first();
                    
    
                // $checkGPI = DB::table('PROC_CODE_LISTS')
                // ->where('proc_code_list_id', $request->proc_code_list_id )
                // ->where('procedure_code',$request->procedure_code)
                // ->where('effective_date',$request->effective_date)
                // ->get()
                // ->count();


                // $effective_date_check = DB::table('PROC_CODE_LISTS')
                // ->where('proc_code_list_id', $request->proc_code_list_id )
                // ->where('procedure_code',$request->procedure_code)
                // ->where('effective_date',$request->effective_date)
                // ->where('termination_date',$request->termination_date)
                // ->get()
                // ->count();


                


                
                //     // dd($checkGPI);
                // // if result >=1 then update NDC_EXCEPTION_LISTS table record
                // //if result 0 then add NDC_EXCEPTION_LISTS record

                // if($effective_date_check == 1){

                //     $add_names = DB::table('PROC_CODE_LIST_NAMES')
                //     ->where('proc_code_list_id',$request->proc_code_list_id)
                //     ->update(
                //         [
                //             'description'=>$request->description,
                            
                //         ]
                //     );

                //     $update = DB::table('PROC_CODE_LISTS' )
                //     ->where('proc_code_list_id', $request->proc_code_list_id )
                //     ->where('procedure_code',$request->procedure_code)
                //     ->where('effective_date',$request->effective_date)
                //     ->update(
                //         [
                //             'TERMINATION_DATE'=>$request->termination_date,
                            
        
                //         ]
                //     );
                //     $update = DB::table('PROC_CODE_LISTS')->where('proc_code_list_id', 'like', '%' . $request->proc_code_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                   


                // }else if($checkGPI == 1)
                // {

                //     return $this->respondWithToken($this->token(), 'Record Already  Exists',$checkGPI);


                // }
                // else{

                //     if ($checkGPI <= "0") {
                //         $update = DB::table('PROC_CODE_LISTS')
                //         ->insert(
                //             [
                //                 'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                //                 'PROCEDURE_CODE'=>$request->procedure_code,
                //                 'EFFECTIVE_DATE'=>$request->effective_date,
                //                 'TERMINATION_DATE'=>$request->termination_date,
                //                 'DATE_TIME_CREATED'=>$createddate,
                                
                                
                //             ]);
                        
    
                //     $update = DB::table('PROC_CODE_LISTS')->where('proc_code_list_id', 'like', '%' . $request->proc_code_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
    
      
    
                        
                //     }

                // }

            }
           
        }
    }

    public function get( Request $request )
   {
        $providerCodeList = DB::table( 'PROC_CODE_LIST_NAMES' )
        // ->where( 'PROC_CODE_LIST_ID', 'like', '%'.$request->search.'%' )
        ->whereRaw('LOWER(PROC_CODE_LIST_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
        ->orWhere( 'DESCRIPTION', 'like', '%'.$request->search.'%' )
        ->get();
        return $this->respondWithToken( $this->token(), '', $providerCodeList );
    }

    public function getAll( Request $request )
    {
        $providerCodeList = DB::table( 'PROC_CODE_LIST_NAMES')->get();
        return $this->respondWithToken( $this->token(), '', $providerCodeList );
    }

    public function getProcCodeList( Request $request )
 {

    // dd($request->procedure_code);
        $providerCodeList = DB::table( 'PROC_CODE_LISTS' )
        ->join( 'PROC_CODE_LIST_NAMES', 'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID', '=', 'PROC_CODE_LISTS.PROC_CODE_LIST_ID' )
        // ->join('PROCEDURE_CODES','PROCEDURE_CODES.PROCEDURE_CODE','=','PROC_CODE_LISTS.PROCEDURE_CODE')
        ->where( 'PROC_CODE_LISTS.PROC_CODE_LIST_ID', $request->proc_code_list_id)
        // ->where( 'PROC_CODE_LISTS.procedure_code', $request->procedure_code)

        // ->select('PROC_CODE_LISTS.proc_code_list_id',
        // 'PROC_CODE_LISTS.procedure_code',
        // 'PROC_CODE_LISTS.effective_date',
        // 'PROC_CODE_LISTS.termination_date',
        // 'PROC_CODE_LISTS.date_time_created',
        // 'PROC_CODE_LISTS.user_id_created',
        // 'PROC_CODE_LISTS.user_id',
        // 'PROC_CODE_LISTS.date_time_modified',
        // 'PROC_CODE_LIST_NAMES.description',
        // 'PROCEDURE_CODES.DESCRIPTION as procedure_code_description'
        // )
        ->get();
        return $this->respondWithToken( $this->token(), '', $providerCodeList );
    }


    public function getDetails( Request $request )
    {
   
       // dd($request->procedure_code);
           $providerCodeList = DB::table( 'PROC_CODE_LISTS' )
           ->join( 'PROC_CODE_LIST_NAMES', 'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID', '=', 'PROC_CODE_LISTS.PROC_CODE_LIST_ID' )
           // ->join('PROCEDURE_CODES','PROCEDURE_CODES.PROCEDURE_CODE','=','PROC_CODE_LISTS.PROCEDURE_CODE')
           ->where( 'PROC_CODE_LISTS.PROC_CODE_LIST_ID', $request->proc_code_list_id)
           ->where( 'PROC_CODE_LISTS.procedure_code', $request->procedure_code)
           ->where( 'PROC_CODE_LISTS.effective_date', $request->effective_date)

          
           ->first();
           return $this->respondWithToken( $this->token(), '', $providerCodeList );
       }


       public function produrecodelistdelete(Request $request)
    {
        // return $request->all();
        if (isset($request->proc_code_list_id) && ($request->procedure_code)) {
            $all_exceptions_lists =  DB::table('PROC_CODE_LISTS')
                ->where('PROC_CODE_LIST_ID', $request->proc_code_list_id)
                ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->proc_code_list_id)) {

            $exception_delete =  DB::table('PROC_CODE_LIST_NAMES')
                ->where('PROC_CODE_LIST_ID', $request->proc_code_list_id)
                ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
    // dd($request->all());




    
}
