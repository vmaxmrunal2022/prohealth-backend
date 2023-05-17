<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SuperBenefitControler extends Controller
{      



    public function addcopy( Request $request ) {
        $createddate = date( 'y-m-d' );

        $effective_date = date('Ymd', strtotime($request->effective_date));
        $terminate_date = date('Ymd', strtotime($request->termination_date));


        $recordcheck = DB::table('SUPER_BENEFIT_LIST_NAMES')
        ->where('super_benefit_list_id', strtoupper($request->super_benefit_list_id))
        ->first();


        if ( $request->has( 'new' ) ) {

            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Super Benefit List ID  Already Exists', $recordcheck);

            }

            else{


                $accum_benfit_stat_names = DB::table('SUPER_BENEFIT_LIST_NAMES')->insert(
                    [
                        'super_benefit_list_id' => strtoupper( $request->super_benefit_list_id ),
                        'description'=>$request->description
                        
    
                    ]
                );
    
    
                $accum_benfit_stat = DB::table('SUPER_BENEFIT_LISTS' )->insert(
                    [
                        'super_benefit_list_id'=>strtoupper( $request->super_benefit_list_id ),
    
                        'benefit_list_id'=>$request->benefit_list_id,
                        'accum_benefit_strategy_id'=>$request->accum_benefit_strategy_id,
                        'effective_date'=>$effective_date,
                        'termination_date'=>$terminate_date,
    
    
                    ]
                );
    
                if ($accum_benfit_stat) {
                    return $this->respondWithToken($this->token(), 'Recored Added Successfully', $accum_benfit_stat);
                }

            }




         
        } else {


            $benefitcode = DB::table('SUPER_BENEFIT_LIST_NAMES' )
            ->where('super_benefit_list_id', $request->super_benefit_list_id )


            ->update(
                [
                    'description'=>$request->description,

                ]
            );

            $accum_benfit_stat = DB::table('SUPER_BENEFIT_LISTS' )
            ->where('super_benefit_list_id', $request->super_benefit_list_id )
            ->where('benefit_list_id',strtoupper($request->benefit_list_id))
            ->update(
                [
                    'accum_benefit_strategy_id'=>$request->accum_benefit_strategy_id,
                    'effective_date'=>$effective_date,
                    'termination_date'=>$terminate_date,
                  

                ]
            );

            if ($accum_benfit_stat) {
                return $this->respondWithToken($this->token(), 'Record  Updated Successfully', $accum_benfit_stat);
            }
        }


    }

    public function add(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('SUPER_BENEFIT_LIST_NAMES')
        ->where('super_benefit_list_id',$request->super_benefit_list_id)
        ->get();

        if ($request->add_new == 1) {
            

            $validator = Validator::make($request->all(), [
                'super_benefit_list_id' => ['required', 'max:10', Rule::unique('SUPER_BENEFIT_LIST_NAMES')->where(function ($q) {
                    $q->whereNotNull('super_benefit_list_id');
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
                "benefit_list_id"=>['required','max:36'],
                'accum_benefit_strategy_id'=>['max:10'],
                'effective_date'=>['required','max:10'],
                'termination_date'=>['required','max:10','after:effective_date'],

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
                $overlapExists = DB::table('SUPER_BENEFIT_LISTS')
                ->where('SUPER_BENEFIT_LIST_ID', $request->super_benefit_list_id)
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
                    return $this->respondWithToken($this->token(), 'For same Benefit List, dates cannot overlap.', $validation, true, 200, 1);
                }


                $add_names = DB::table('SUPER_BENEFIT_LIST_NAMES')->insert(
                    [
                        'SUPER_BENEFIT_LIST_ID' => $request->super_benefit_list_id,
                        'DESCRIPTION'=>$request->description,
                        
                    ]
                );
    
                $add = DB::table('SUPER_BENEFIT_LISTS')
                ->insert(
                    [
                        'SUPER_BENEFIT_LIST_ID'=>$request->super_benefit_list_id,
                        'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'ACCUM_BENEFIT_STRATEGY_ID'=>$request->accum_benefit_strategy_id,
                        'DATE_TIME_CREATED'=>$createddate,
                        
                        
                    ]);
                   
    
                $add = DB::table('SUPER_BENEFIT_LISTS')->where('super_benefit_list_id', 'like', '%' . $request->super_benefit_list_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [
                "super_benefit_list_id" => ['required','max:36'],
                "description"=>['required','max:36'],
                "benefit_list_id"=>['required','max:36'],
                'accum_benefit_strategy_id'=>['max:10'],
                'effective_date'=>['required','max:10'],
                'termination_date'=>['required','max:10','after:effective_date'],
                

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

                        $add_names = DB::table('SUPER_BENEFIT_LIST_NAMES')
                        ->where('super_benefit_list_id',$request->super_benefit_list_id)
                        ->update(
                            [
                                'description'=>$request->description,
                                
                            ]
                        );
    
                        $update = DB::table('SUPER_BENEFIT_LISTS' )
                        ->where('super_benefit_list_id', $request->super_benefit_list_id )
                        ->where('benefit_list_id',$request->benefit_list_id)
                        ->where('effective_date',$request->effective_date)
                        // ->where('termination_date',$request->termination_date)
    
                        ->update(
                            [
                                'TERMINATION_DATE'=>$request->termination_date,
                                'ACCUM_BENEFIT_STRATEGY_ID'=>$request->accum_benefit_strategy_id
                                
            
                            ]
                        );
                        $update = DB::table('SUPER_BENEFIT_LISTS')->where('super_benefit_list_id', 'like', '%' . $request->super_benefit_list_id . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);

                   
                    

              }
              elseif($request->update_new == 1){
                    
              
                $checkGPI = DB::table('SUPER_BENEFIT_LISTS')
                    ->where('super_benefit_list_id', $request->super_benefit_list_id )
                    ->where('benefit_list_id',$request->benefit_list_id)
                    ->where('effective_date',$request->effective_date)
                    // ->where('termination_date',$request->termination_date)
                    ->get();

                if(count($checkGPI) >= 1){
                    return $this->respondWithToken($this->token(), [["Benefit List ID already exists"]], '', 'false');
                }else{
                    $update = DB::table('SUPER_BENEFIT_LISTS')
                    ->insert(
                        [
                            'SUPER_BENEFIT_LIST_ID'=>$request->super_benefit_list_id,
                            'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                            'EFFECTIVE_DATE'=>$request->effective_date,
                            'TERMINATION_DATE'=>$request->termination_date,
                            'ACCUM_BENEFIT_STRATEGY_ID'=>$request->accum_benefit_strategy_id,
                            'DATE_TIME_CREATED'=>$createddate,
                            
                        ]);
                    $update = DB::table('SUPER_BENEFIT_LISTS')->where('super_benefit_list_id', 'like', '%' . $request->super_benefit_list_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                }

              }



             //   exit();
    
                // $update_names = DB::table('SUPER_BENEFIT_LIST_NAMES')
                // ->where('super_benefit_list_id', $request->super_benefit_list_id )
                // ->first();
                    
    
                // $checkGPI = DB::table('SUPER_BENEFIT_LISTS')
                // ->where('super_benefit_list_id', $request->super_benefit_list_id )
                // ->where('benefit_list_id',$request->benefit_list_id)
                // ->where('effective_date',$request->effective_date)
                // ->where('termination_date',$request->termination_date)
                // ->get()
                // ->count();
                //     // dd($checkGPI);
                // // if result >=1 then update NDC_EXCEPTION_LISTS table record
                // //if result 0 then add NDC_EXCEPTION_LISTS record

    
                // if ($checkGPI <= "0") {
                  

                //     // $effectiveDate=$request->effective_date;
                //     // $terminationDate=$request->termination_date;
                //     // $overlapExists = DB::table('SUPER_BENEFIT_LISTS')
                //     // ->where('SUPER_BENEFIT_LIST_ID', $request->procedure_xref_id)
                //     // ->where(function ($query) use ($effectiveDate, $terminationDate) {
                //     //     $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                //     //         ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                //     //         ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                //     //             $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                //     //                 ->where('TERMINATION_DATE', '>=', $terminationDate);
                //     //         });
                //     // })
                //     // ->exists();
                //     // if ($overlapExists) {
                //     //     return $this->respondWithToken($this->token(), 'For same Benefit List, dates cannot overlap.', $validation, true, 200, 1);
                //     // }

                //     $update = DB::table('SUPER_BENEFIT_LISTS')
                //     ->insert(
                //         [
                //             'SUPER_BENEFIT_LIST_ID'=>$request->super_benefit_list_id,
                //             'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                //             'EFFECTIVE_DATE'=>$request->effective_date,
                //             'TERMINATION_DATE'=>$request->termination_date,
                //             'ACCUM_BENEFIT_STRATEGY_ID'=>$request->accum_benefit_strategy_id,
                //             'DATE_TIME_CREATED'=>$createddate,
                            
                            
                //         ]);
                       

                //     $update = DB::table('SUPER_BENEFIT_LISTS')->where('super_benefit_list_id', 'like', '%' . $request->super_benefit_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                // } else {
                    
                //     // $effectiveDate=$request->effective_date;
                //     // $terminationDate=$request->termination_date;
                //     // $overlapExists = DB::table('SUPER_BENEFIT_LISTS')
                //     // ->where('SUPER_BENEFIT_LIST_ID', $request->super_benefit_list_id)
                //     // ->where(function ($query) use ($effectiveDate, $terminationDate) {
                //     //     $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                //     //         ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                //     //         ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                //     //             $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                //     //                 ->where('TERMINATION_DATE', '>=', $terminationDate);
                //     //         });
                //     // })
                //     // ->exists();
                //     // if ($overlapExists) {
                //     //     return $this->respondWithToken($this->token(), 'For same Benefit List, dates cannot overlap.', $validation, true, 200, 1);
                //     // }
                   
                //     $add_names = DB::table('SUPER_BENEFIT_LIST_NAMES')
                //     ->where('super_benefit_list_id',$request->super_benefit_list_id)
                //     ->update(
                //         [
                //             'description'=>$request->description,
                            
                //         ]
                //     );

                //     $update = DB::table('SUPER_BENEFIT_LISTS' )
                //     ->where('super_benefit_list_id', $request->super_benefit_list_id )
                //     ->where('benefit_list_id',$request->benefit_list_id)
                //     ->where('effective_date',$request->effective_date)
                //     ->where('termination_date',$request->termination_date)

                //     ->update(
                //         [
                //             'TERMINATION_DATE'=>$request->termination_date,
                //             'ACCUM_BENEFIT_STRATEGY_ID'=>$request->accum_benefit_strategy_id
                            
        
                //         ]
                //     );
                //     $update = DB::table('SUPER_BENEFIT_LISTS')->where('super_benefit_list_id', 'like', '%' . $request->super_benefit_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                // }
    
            }

           
        }
    }


    public function getNDCItemDetails($listid,$beneid,$effe)
    {
      
         $benefitLists = DB::table('SUPER_BENEFIT_LISTS')
         ->join('SUPER_BENEFIT_LIST_NAMES', 'SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID', '=', 'SUPER_BENEFIT_LIST_NAMES.SUPER_BENEFIT_LIST_ID')
         ->where('SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID',$listid) 
         ->where('SUPER_BENEFIT_LISTS.BENEFIT_LIST_ID',$beneid)
         ->where('SUPER_BENEFIT_LISTS.EFFECTIVE_DATE',$effe)
         ->first();

        return $this->respondWithToken($this->token(), '', $benefitLists);

    }



    public function get(Request $request)
    {
        $superBenefitNames = DB::table('SUPER_BENEFIT_LIST_NAMES')
                             ->where('SUPER_BENEFIT_LIST_ID','like','%'.strtoupper($request->search).'%')
                             ->orWhere('DESCRIPTION','like','%'.strtoupper($request->search).'%')
                             ->get();

        return $this->respondWithToken($this->token(),'',$superBenefitNames);
    }

    public function getBenefitCode(Request $request)
    {
        $benefitLists = DB::table('SUPER_BENEFIT_LISTS')
                        ->join('SUPER_BENEFIT_LIST_NAMES', 'SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID', '=', 'SUPER_BENEFIT_LIST_NAMES.SUPER_BENEFIT_LIST_ID')
                        ->where('SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID','like','%'.strtoupper($request->search).'%')
                        ->get();

        return $this->respondWithToken($this->token(),'',$benefitLists);
    }
    public function super_benefit_list_delete(Request $request)
    {
        if (isset($request->super_benefit_list_id) && ($request->benefit_list_id)) {
            $all_exceptions_lists =  DB::table('SUPER_BENEFIT_LISTS')
                ->where('SUPER_BENEFIT_LIST_ID', $request->super_benefit_list_id)
                ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->super_benefit_list_id)) {

            $exception_delete =  DB::table('SUPER_BENEFIT_LIST_NAMES')
                ->where('SUPER_BENEFIT_LIST_ID', $request->super_benefit_list_id)
                ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}


