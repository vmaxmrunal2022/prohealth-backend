<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SuperBenefitControler extends Controller
{      



    

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

    public function addnew(Request $request)
    {
        $createddate = date('d-M-y');
        $validation = DB::table('SUPER_BENEFIT_LIST_NAMES')
            ->where('super_benefit_list_id', $request->super_benefit_list_id)
            ->get();

        if ($request->new) {
            $validator = Validator::make($request->all(), [
                // 'physician_list' => ['required', 'max:10', Rule::unique('SUPER_BENEFIT_LIST_NAMES')->where(function ($q) {
                //     $q->whereNotNull('physician_list');
                // })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('SUPER_BENEFIT_LIST_NAMES')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                // "exception_name" => ['required', 'max:36'],
                // "physician_id" => ['required', 'max:10'],
                // // "PHARMACY_NABP"=>['max:10'],
                // "physician_status" => ['max:10'],
                // "DATE_TIME_CREATED" => ['max:10'],
                // "DATE_TIME_MODIFIED" => ['max:10']



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if (!$request->updateForm) {

                    $ifExist = DB::table('SUPER_BENEFIT_LIST_NAMES')
                        ->where(DB::raw('UPPER(super_benefit_list_id)'), strtoupper($request->super_benefit_list_id))
                        ->get();
                    if (count($ifExist) >= 1) {
                        return $this->respondWithToken($this->token(), [["Prescriber List ID already exists"]], '', false);
                    }
                } else {
                }
                if ($request->physician_list && $request->physician_id) {
                    $count = DB::table('SUPER_BENEFIT_LIST_NAMES')
                        ->where(DB::raw('UPPER(super_benefit_list_id)'), strtoupper($request->super_benefit_list_id))
                        ->get()
                        ->count();
                    if ($count <= 0) {
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
                    } else {
                        $updateProviderExceptionData = DB::table('SUPER_BENEFIT_LIST_NAMES')
                            ->where('super_benefit_list_id', $request->super_benefit_list_id)
                            ->update([
                                'description' => $request->description,
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);
                        $countValidation = DB::table('SUPER_BENEFIT_LISTS')
                            ->where(DB::raw('UPPER(super_benefit_list_id)'), strtoupper($request->super_benefit_list_id))
                            ->where(DB::raw('UPPER(effective_date)'), strtoupper($request->effective_date))
                            ->get();

                        if (count($countValidation) >= 1) {
                            return $this->respondWithToken(
                                $this->token(),
                                [['Prescriber ID already exists']],
                                [['Physician ID already exists']],
                                false
                            );
                        } else {
                            $addProviderValidationData = DB::table('SUPER_BENEFIT_LISTS')
                            ->insert(
                                [
                                    'SUPER_BENEFIT_LIST_ID'=>$request->super_benefit_list_id,
                                    'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                                    'EFFECTIVE_DATE'=>$request->effective_date,
                                    'TERMINATION_DATE'=>$request->termination_date,
                                    'ACCUM_BENEFIT_STRATEGY_ID'=>$request->accum_benefit_strategy_id,
                                    'DATE_TIME_CREATED'=>$createddate,
                                    
                                    
                                ]);
                            // $reecord = DB::table('SUPER_BENEFIT_LIST_NAMES')
                            //     ->join('SUPER_BENEFIT_LISTS', 'SUPER_BENEFIT_LIST_NAMES.physician_list', '=', 'SUPER_BENEFIT_LISTS.physician_list')
                            //     ->where('SUPER_BENEFIT_LISTS.physician_list', $request->physician_list)
                            //     ->where('SUPER_BENEFIT_LISTS.physician_id', $request->physician_id)
                            //     ->first();
                            return $this->respondWithToken(
                                $this->token(),
                                'Record Added successfully',
                                $addProviderValidationData,
                            );
                        }
                    }
                }
            }
        } else {
            $updateProviderExceptionData = DB::table('SUPER_BENEFIT_LIST_NAMES')
                ->where('super_benefit_list_id', $request->super_benefit_list_id)
                ->update([
                    'description' => $request->description,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $countValidation = DB::table('SUPER_BENEFIT_LISTS')
                ->where(DB::raw('UPPER(super_benefit_list_id)'), strtoupper($request->super_benefit_list_id))
                ->where(DB::raw('UPPER(effective_date)'), strtoupper($request->effective_date))
                // ->where('pharmacy_status', $request->pharmacy_status)
                ->update([
                                    'TERMINATION_DATE'=>$request->termination_date,
                                    'ACCUM_BENEFIT_STRATEGY_ID'=>$request->accum_benefit_strategy_id,
                                    'DATE_TIME_CREATED'=>$createddate,
                ]);

            return $this->respondWithToken(
                $this->token(),
                'Record Updated successfully',
                $countValidation,
            );
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


