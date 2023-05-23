<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RvaListController extends Controller
{
    public function get(Request $request)
    {
        // return "hi";
        $rvaNames = DB::table('rva_names')
            // ->when($request->search, function ($query) use ($request) {
            //     return $query->where('rva_list_id', 'like', "%$request->search%");
            // })
            ->where(DB::raw('lower(rva_list_id)'), 'like', '%' . strtolower($request->search) . '%')
            // ->where('rva_list_id', 'like', "%$request->search%")
            ->get();

        return $this->respondWithToken($this->token(), '', $rvaNames);
    }

    public function RvaListDropdown(Request $request)
    {
        // return "hi";
        $rvaNames = DB::table('rva_names')
           
            ->get();

        return $this->respondWithToken($this->token(), '', $rvaNames);
    }

    public function getRvaList(Request $request)
    {
        $rvaLists = DB::table('rva_names')
            ->join('rva_list', 'rva_names.rva_list_id', 'rva_list.rva_list_id')
            ->where('rva_list.rva_list_id', $request->search)
            ->get();

        return $this->respondWithToken($this->token(), '', $rvaLists);
    }




    public function submitRva(Request $request)
    {
        $effective_date = date('Ymd', strtotime($request->effective_date));
        $termination_date = date('Ymd', strtotime($request->termination_date));


        $check_record = DB::table('RVA_LIST')->where('RVA_LIST_ID', $request->rva_list_id)->first();


        if ($request->add_new == 1) {


            if ($check_record) {
                return $this->respondWithToken($this->token(), 'RVA List ID is Already Exists', $check_record, true, 200, 1);
            }


            $add = DB::table('RVA_LIST')
                ->insert([
                    'RVA_LIST_ID' => strtoupper($request->rva_list_id),
                    'EFFECTIVE_DATE' => $effective_date,
                    'TERMINATION_DATE' => $termination_date,
                    'RVA_VALUE' => $request->rva_value,
                    'USER_ID' => $request->user_id,
                    'FORM_ID' => $request->form_id,
                ]);

            $add1 = DB::table('RVA_NAMES')
                ->insert([
                    'RVA_LIST_ID' => strtoupper($request->rva_list_id),
                    'DESCRIPTION' => $request->description,
                    'USER_ID' => $request->user_id,
                    'FORM_ID' => $request->form_id,
                ]);

            // $add = DB::table('mac_table')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
            return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
        } else if ($request->add_new == 0) {
            if (!$check_record) {
                return $this->respondWithToken($this->token(), 'Record Not Found', $check_record, false, 404, 0);
            }
            $updatecode = DB::table('RVA_LIST')
                ->where('RVA_LIST_ID', $request->rva_list_id)
                ->update(
                    [
                        'EFFECTIVE_DATE' => $effective_date,
                        'TERMINATION_DATE' => $termination_date,
                        'RVA_VALUE' => $request->rva_value,
                        'USER_ID' => $request->user_id,
                        'FORM_ID' => $request->form_id,
                    ]
                );
            $update1 = DB::table('RVA_NAMES')->where('RVA_LIST_ID', $request->rva_list_id)
                ->update([
                    'RVA_LIST_ID' => strtoupper($request->rva_list_id),
                    'DESCRIPTION' => $request->description,
                    'USER_ID' => $request->user_id,
                    'FORM_ID' => $request->form_id,
                ]);

            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updatecode);
        }
    }

     public function submitRvapast(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('RVA_NAMES')
        ->where('rva_list_id',$request->rva_list_id)

        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'procedure_ucr_id' => ['required', 'max:10', Rule::unique('procedure_ucr_names')->where(function ($q) {
                    $q->whereNotNull('procedure_ucr_id');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('procedure_ucr_names')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "description" => ['max:36'],
              



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'NDC Exception Already Exists', $validation, true, 200, 1);
                }

                $effectiveDate=$request->effective_date;
                $terminationDate=$request->termination_date;
                $overlapExists = DB::table('PROCEDURE_UCR_LIST')
                ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
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
                    return $this->respondWithToken($this->token(), 'For MAC , dates cannot overlap.', $validation, true, 200, 1);
                }

                $add_names = DB::table('procedure_ucr_names')->insert(
                    [
                        'procedure_ucr_id' => $request->procedure_ucr_id,
                        'description'=>$request->description,
                        
                    ]
                );
    
                $add = DB::table('PROCEDURE_UCR_LIST')
                ->insert([
                    'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    'procedure_code'   => $request->procedure_code,
                    'effective_date'   => $request->effective_date,
                    'termination_date' => $request->termination_date,
                    'unit_value'       => $request->unit_value,
                    'UCR_CURRENCY'     => $request->ucr_currency,
                ]);
                $add = DB::table('PROCEDURE_UCR_LIST')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                'procedure_ucr_id' => ['required', 'max:10'],
                


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }



                if($request->update_new == 0){
                    $checkGPI = DB::table('PROCEDURE_UCR_LIST')
                    ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                    ->where('PROCEDURE_CODE',$request->procedure_code)
                    ->where('EFFECTIVE_DATE',$request->effective_date)
                    ->first();

                    if($checkGPI){
                        $effectiveDate=$request->effective_date;
                        $terminationDate=$request->termination_date;
                        $overlapExists = DB::table('PROCEDURE_UCR_LIST')
                        ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                        ->where('PROCEDURE_CODE','!=',$request->procedure_code)
                        ->where('EFFECTIVE_DATE','!=',$request->effective_date)
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
                            return $this->respondWithToken($this->token(), 'For MAC , dates cannot overlap.', $validation, true, 200, 1);
                        }

                        $add_names = DB::table('procedure_ucr_names')
                        ->where('procedure_ucr_id',$request->procedure_ucr_id)
                        ->update(
                            [
                                'description'=>$request->description,
                                
                            ]
                        );
    
    
                        $update = DB::table('PROCEDURE_UCR_LIST' )
                        ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                        ->where('PROCEDURE_CODE',$request->procedure_code)
                        ->where('EFFECTIVE_DATE',$request->effective_date)   
                        ->update([
                            // 'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                            // 'procedure_code' => $request->procedure_code,
                            'effective_date' => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'unit_value' => $request->unit_value,
                            'UCR_CURRENCY' => $request->ucr_currency,
                        ]);
                        $update = DB::table('PROCEDURE_UCR_LIST')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                    }else{
                        return $this->respondWithToken($this->token(), [["Record Not found to update"]], '', 'false');
                    }

                }elseif($request->update_new == 1){
                    $checkGPI = DB::table('PROCEDURE_UCR_LIST')
                                    ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                                    ->where('PROCEDURE_CODE',$request->procedure_code)
                                    ->where('EFFECTIVE_DATE',$request->effective_date)
                                    ->get();
                    if(count($checkGPI) >= 1){
                        return $this->respondWithToken($this->token(), [["Record already exists"]], '', 'false');
                    }else{

                        $effectiveDate=$request->effective_date;
                        $terminationDate=$request->termination_date;
                        $overlapExists = DB::table('PROCEDURE_UCR_LIST')
                        ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
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
                            return $this->respondWithToken($this->token(), 'For MAC , dates cannot overlap.', $validation, true, 200, 1);
                        }

                        $update = DB::table('PROCEDURE_UCR_LIST')
                        ->insert([
                            'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                            'procedure_code'   => $request->procedure_code,
                            'effective_date'   => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'unit_value'       => $request->unit_value,
                            'UCR_CURRENCY'     => $request->ucr_currency,
                        ]);
                      
                       
                        $add_names = DB::table('procedure_ucr_names')
                        ->where('procedure_ucr_id',$request->procedure_ucr_id)
                        ->update(
                            [
                                'description'=>$request->description,
                                
                            ]
                        );
    
                        $update = DB::table('procedure_ucr_names')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }
                }
    
                // $procedure_ucr_names = DB::table('procedure_ucr_names')
                // ->where('procedure_ucr_id', $request->procedure_ucr_id )
                // ->first();
                    
    
                // $checkGPI = DB::table('PROCEDURE_UCR_LIST')
                //     ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                //     ->where('PROCEDURE_CODE',$request->procedure_code)
                //     ->get()
                //     ->count();

                //     // dd($checkGPI);


                // $effect_date_check = DB::table('PROCEDURE_UCR_LIST')
                // ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                // ->where('PROCEDURE_CODE',$request->procedure_code)
                // ->where('EFFECTIVE_DATE',$request->effective_date)
                // ->where('TERMINATION_DATE',$request->termination_date)

                //     ->get()
                //     ->count();
                //     // dd($effective_date);
                //     // if result >=1 then update NDC_EXCEPTION_LISTS table record
                //     //if result 0 then add NDC_EXCEPTION_LISTS record


                // if($effect_date_check == 1){

                //     $add_names = DB::table('procedure_ucr_names')
                //     ->where('procedure_ucr_id',$request->procedure_ucr_id)
                //     ->update(
                //         [
                //             'description'=>$request->description,
                            
                //         ]
                //     );


                //     $update = DB::table('PROCEDURE_UCR_LIST' )
                //     ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                //     ->where('PROCEDURE_CODE',$request->procedure_code)
                //     ->where('EFFECTIVE_DATE',$request->effective_date)    
     
                //     ->update([
                //         // 'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                //         // 'procedure_code' => $request->procedure_code,
                //         'effective_date' => $request->effective_date,
                //         'termination_date' => $request->termination_date,
                //         'unit_value' => $request->unit_value,
                //         'UCR_CURRENCY' => $request->ucr_currency,
                //     ]);
                //     $update = DB::table('PROCEDURE_UCR_LIST')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);

                   


                // }else if($checkGPI == 1)
                // {

                //     return $this->respondWithToken($this->token(), 'Record already  exists',$checkGPI);


                // }
                // else{
                //     if ($checkGPI <= "0") {
                //         $update = DB::table('PROCEDURE_UCR_LIST')
                //         ->insert([
                //             'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                //             'procedure_code'   => $request->procedure_code,
                //             'effective_date'   => $request->effective_date,
                //             'termination_date' => $request->termination_date,
                //             'unit_value'       => $request->unit_value,
                //             'UCR_CURRENCY'     => $request->ucr_currency,
                //         ]);
                      
                       
                //         $add_names = DB::table('procedure_ucr_names')
                //         ->where('procedure_ucr_id',$request->procedure_ucr_id)
                //         ->update(
                //             [
                //                 'description'=>$request->description,
                                
                //             ]
                //         );
    
                //     $update = DB::table('procedure_ucr_names')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
    
                //     } 

                // }
               
                

    
            
            }

           
        }
    }

    public function submitRva(Request $request)
    {
        $effective_date = date('Ymd', strtotime($request->effective_date));
        $termination_date = date('Ymd', strtotime($request->termination_date));

        $check_record = DB::table('RVA_LIST')->where('RVA_LIST_ID', $request->rva_list_id)->first();


        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'rva_list_id' => ['required', 'max:10', Rule::unique('RVA_NAMES')->where(function ($q) {
                    $q->whereNotNull('rva_list_id');
                })],
                "description" => ['max:36'],
                'effective_date'=>['required'],
                'termination_date'=>['required','after:effective_date'],

            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }


            // if ($check_record) {
            //     return $this->respondWithToken($this->token(), 'RVA List ID is Already Exists', $check_record, true, 200, 1);
            // }

            $effectiveDate=$request->effective_date;
            $terminationDate=$request->termination_date;
            $overlapExists = DB::table('RVA_LIST')
            ->where('RVA_LIST_ID', $request->rva_list_id)
            ->where(function ($query) use ($effectiveDate, $terminationDate) {
                $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                    ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                    ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                        $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                            ->where('TERMINATION_DATE', '>=', $terminationDate);
                    });
            }) ->exists();
            if ($overlapExists) {
                // return $this->respondWithToken($this->token(), 'For RVA , dates cannot overlap.', '', 'false', 200, 1);
                return $this->respondWithToken($this->token(), [["For RVA , dates cannot overlap."]], '', 'false');
            }


            $add = DB::table('RVA_LIST')
                ->insert([
                    'RVA_LIST_ID' => $request->rva_list_id,
                    'EFFECTIVE_DATE' => $effective_date,
                    'TERMINATION_DATE' => $termination_date,
                    'RVA_VALUE' => $request->rva_value,
                    'USER_ID' => $request->user_id,
                    'FORM_ID' => $request->form_id,
                ]);

            $add1 = DB::table('RVA_NAMES')
                ->insert([
                    'RVA_LIST_ID' => $request->rva_list_id,
                    'DESCRIPTION' => $request->description,
                    'USER_ID' => $request->user_id,
                    'FORM_ID' => $request->form_id,
                ]);

            // $add = DB::table('mac_table')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
            return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
        } else if ($request->add_new == 0) {
            // if (!$check_record) {
            //     return $this->respondWithToken($this->token(), 'Record Not Found', $check_record, false, 404, 0);
            // }
            
            $validator = Validator::make($request->all(), [
                'rva_list_id' => ['required', 'max:10'],
                "description" => ['max:36'],
                'effective_date'=>['required'],
                'termination_date'=>['required','after:effective_date'],

            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            if($request->update_new == 0){
                $checkGPI = DB::table('RVA_LIST')
                ->where('RVA_LIST_ID', $request->rva_list_id)
                // ->where('RVA_VALUE', $request->rva_value)
                ->where('EFFECTIVE_DATE',$effective_date)
                ->first();

                if( $checkGPI ){

                    // $effectiveDate=$request->effective_date;
                    // $terminationDate=$request->termination_date;
                    // $overlapExists = DB::table('RVA_LIST')
                    // ->where('RVA_LIST_ID', $request->rva_list_id)
                    // // ->where('RVA_VALUE', $request->rva_value)
                    // ->where('EFFECTIVE_DATE','!=',$request->effective_date)
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
                    //     return $this->respondWithToken($this->token(), [["For Same RVA Value, dates cannot overlap."]], '', 'false');
                    // }


                    $updatecode = DB::table('RVA_LIST')
                    ->where('RVA_LIST_ID', $request->rva_list_id)
                    // ->where('RVA_VALUE', $request->rva_value)
                    ->where('EFFECTIVE_DATE', $effective_date)
                    ->update(
                        [
                            // 'EFFECTIVE_DATE' => $effective_date,
                            'TERMINATION_DATE' => $termination_date,
                            'RVA_VALUE' => $request->rva_value,
                            'USER_ID' => $request->user_id,
                            'FORM_ID' => $request->form_id,
                        ]
                    );
                    $update1 = DB::table('RVA_NAMES')->where('RVA_LIST_ID', $request->rva_list_id)
                        ->update([
                            // 'RVA_LIST_ID' => strtoupper($request->rva_list_id),
                            'DESCRIPTION' => $request->description,
                            'USER_ID' => $request->user_id,
                            'FORM_ID' => $request->form_id,
                        ]);
        
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updatecode);
                }else{
                    return $this->respondWithToken($this->token(), [["Record Not found to update"]], '', 'false');
                }

            }elseif($request->update_new == 1){
                $checkGPI = DB::table('RVA_LIST')
                ->where('RVA_LIST_ID', $request->rva_list_id)
                // ->where('RVA_VALUE', $request->rva_value)
                ->where('EFFECTIVE_DATE',$effective_date)
                ->get();

                if(count($checkGPI) >= 1){
                    return $this->respondWithToken($this->token(), [["RVA Value already exists"]], '', 'false');
                }else{
                    // $effectiveDate=$request->effective_date;
                    // $terminationDate=$request->termination_date;
                    // $overlapExists = DB::table('RVA_LIST')
                    // ->where('RVA_LIST_ID', $request->rva_list_id)
                    // // ->where('RVA_VALUE', $request->rva_value)
                    // ->where(function ($query) use ($effectiveDate, $terminationDate) {
                    //     $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                    //         ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                    //         ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                    //             $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                    //                 ->where('TERMINATION_DATE', '>=', $terminationDate);
                    //         });
                    // }) ->exists();
                    // if ($overlapExists) {
                    //     return $this->respondWithToken($this->token(), [["For Same RVA Value , dates cannot overlap."]], '', 'false');
                    // }

                    $add = DB::table('RVA_LIST')
                    ->insert([
                        'RVA_LIST_ID' => $request->rva_list_id,
                        'EFFECTIVE_DATE' => $effective_date,
                        'TERMINATION_DATE' => $termination_date,
                        'RVA_VALUE' => $request->rva_value,
                        'USER_ID' => $request->user_id,
                        'FORM_ID' => $request->form_id,
                    ]);
                    $update1 = DB::table('RVA_NAMES')->where('RVA_LIST_ID', $request->rva_list_id)
                    ->update([
                        // 'RVA_LIST_ID' => strtoupper($request->rva_list_id),
                        'DESCRIPTION' => $request->description,
                        'USER_ID' => $request->user_id,
                        'FORM_ID' => $request->form_id,
                    ]);
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', '');

                }
            }
            
        }
    }
}