<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class ReasonCodeExceptionController extends Controller
{



    // public function addcopy(Request $request)
    // {



    //     $createddate = date('y-m-d');
    //     $effective_date = date('Ymd', strtotime($request->effective_date));
    //     $terminate_date = date('Ymd', strtotime($request->termination_date));


    //     $check = DB::table('REASON_CODE_LISTS')
    //         ->where('REASON_CODE_LIST_ID', strtoupper($request->reason_code_list_id))
    //         ->first();


    //     if ($request->has('new')) {

    //         if ($check) {
    //             return $this->respondWithToken($this->token(), 'Reason Code List Id Already Exists', $check);

    //         } else {

    //             $accum_benfit_stat = DB::table('REASON_CODE_LISTS')
    //             ->insert(
    //                 [
    //                     'reason_code_list_id' => strtoupper($request->reason_code_list_id),
    //                     'reject_code' => $request->reject_code,
    //                     'reason_code' => $request->reason_code,
    //                     'effective_date' => $effective_date,
    //                     'termination_date' => $terminate_date,
    //                     'date_time_created' => $createddate,
    //                     'date_time_modified' => $createddate,
    //                 ]
    //             );


    //             $accum_benfit_stat = DB::table('REASON_CODE_LIST_NAMES')->insert(
    //                 [

    //                     'reason_code_list_id' => strtoupper($request->reason_code_list_id),
    //                     'reason_code_name' => $request->reason_code_name,
    //                     'date_time_created' => $createddate,


    //                 ]
    //             );

    //             $benefitcode = DB::table('REASON_CODE_LISTS')->where('reason_code_list_id', 'like', $request->reason_code_list_id)->first();

    //             return $this->respondWithToken($this->token(), 'Record Added Successfully', $benefitcode);

    //         }





    //     } else {





    //         $createddate = DB::table('REASON_CODE_LISTS')
    //             ->where('reason_code_list_id', $request->reason_code_list_id)
    //             ->update(
    //                 [
    //                     'reject_code' => $request->reject_code,
    //                     'reason_code' => $request->reason_code,
    //                     'effective_date' => $effective_date,
    //                     'termination_date' => $terminate_date,
    //                     'date_time_created' => $createddate,
    //                     'date_time_modified' => $createddate,

    //                 ]
    //             );


    //         $reason_code_names = DB::table('REASON_CODE_LIST_NAMES')
    //             ->where('reason_code_list_id', $request->reason_code_list_id)
    //             ->update(
    //                 [
    //                     'reason_code_name' => $request->reason_code_name,

    //                 ]
    //             );

    //         $benefitcode = DB::table('REASON_CODE_LIST_NAMES')->where('reason_code_name', 'like', '%' . $request->reason_code_name . '%')->first();

    //     }


    //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
    // }

    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $validation = DB::table('REASON_CODE_LIST_NAMES')
            ->where('reason_code_list_id', $request->reason_code_list_id)
            ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'reason_code_list_id' => [
                    'required',
                    'max:10', Rule::unique('REASON_CODE_LISTS')->where(function ($q) {
                        $q->whereNotNull('reason_code_list_id');
                    })
                ],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => [
                //     'required',
                //     'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //         $q->whereNotNull('effective_date');
                //     })
                // ],

                // 'ndc_exception_list' => [
                //     'required',
                //     'max:10', Rule::unique('REASON_CODE_LIST_NAMES')->where(function ($q) {
                //         $q->whereNotNull('ndc_exception_list');
                //     })
                // ],
                // "reason_code_list_id" => ['required','max:36'],
                "reason_code_name"=>['required','max:36'],
                "reject_code"=>['required','max:11'],
                'reason_code'=>['required','max:10'],
                'effective_date'=>['required','max:10'],
                'termination_date'=>['required','max:10','after:effective_date'],
            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Reason Code  Exception Already Exists', $validation, true, 200, 1);
                }

                $effectiveDate=$request->effective_date;
                $terminationDate=$request->termination_date;
                $overlapExists = DB::table('REASON_CODE_LISTS')
                ->where('REASON_CODE_LIST_ID', $request->prov_type_proc_assoc_id)
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
                    return $this->respondWithToken($this->token(), 'For same Reason Code  dates range cannot overlap.', $validation, true, 200, 1);
                }




                $add_names = DB::table('REASON_CODE_LIST_NAMES')->insert(
                    [
                        'reason_code_list_id' => $request->reason_code_list_id,
                        'reason_code_name' => $request->reason_code_name,

                    ]
                );

                $add = DB::table('REASON_CODE_LISTS')
                ->insert(
                    [
                        'reason_code_list_id' => $request->reason_code_list_id,
                        'reject_code' => $request->reject_code,
                        'reason_code' => $request->reason_code,
                        'effective_date' => $request->effective_date,
                        'termination_date' => $request->termination_date,
                        'date_time_created' => $createddate,
                        'date_time_modified' => $createddate,
                    ]
                );
                    
                $add = DB::table('REASON_CODE_LISTS')->where('reason_code_list_id', 'like', '%' . $request->reason_code_list_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }



        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                "reason_code_list_id" => ['required','max:36'],
                "reason_code_name"=>['required','max:36'],
                "reject_code"=>['required'],
                'reason_code'=>['required'],
                'effective_date'=>['required','max:10'],
                'termination_date'=>['required','max:10','after:effective_date'],
            ],[
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }

                // $effectiveDate=$request->effective_date;
                // $terminationDate=$request->termination_date;
                // $overlapExists = DB::table('REASON_CODE_LISTS')
                // ->where('REASON_CODE_LIST_ID', $request->prov_type_proc_assoc_id)
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
                //     return $this->respondWithToken($this->token(), 'For same Reason Code  dates range cannot overlap.', $validation, true, 200, 1);
                // }


                if($request->update_new == 0){

                    $add_names = DB::table('REASON_CODE_LIST_NAMES')
                        ->where('reason_code_list_id', $request->reason_code_list_id)
                        ->update(
                            [
                                'reason_code_name' => $request->reason_code_name,

                            ]
                        );

                    $update = DB::table('REASON_CODE_LISTS')
                    ->where('REASON_CODE_LIST_ID', $request->reason_code_list_id)
                    ->where('REJECT_CODE', $request->reject_code)
                    ->where('REASON_CODE', $request->reason_code)
                    ->where('EFFECTIVE_DATE',$request->effective_date)
                    ->get();

                    // return $request->all();
                        // ->update(
                        //     [
                        //         'TERMINATION_DATE'=>$request->termination_date,

                        //     ]
                        // );
                    $update = DB::table('REASON_CODE_LISTS')->where('reason_code_list_id', 'like', '%' . $request->reason_code_list_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);

                }elseif($request->update_new == 1){
                    $checkGPI = DB::table('REASON_CODE_LISTS')
                    ->where('REASON_CODE_LIST_ID', $request->reason_code_list_id)
                    ->where('REJECT_CODE', $request->reject_code)
                    ->where('REASON_CODE', $request->reason_code)
                    ->where('EFFECTIVE_DATE',$request->effective_date)
                   
                    ->get();
// return $checkGPI;
                    if(count($checkGPI) >= 1){
                        return $this->respondWithToken($this->token(), [["Reason Code List ID already exists"]], '', 'false');
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


                // $update_names = DB::table('REASON_CODE_LIST_NAMES')
                //     ->where('reason_code_list_id', $request->reason_code_list_id)
                //     ->first();


                // $checkGPI = DB::table('REASON_CODE_LISTS')
                //     ->where('REASON_CODE_LIST_ID', $request->reason_code_list_id)
                //     ->where('REJECT_CODE', $request->reject_code)
                //     ->where('reason_code', $request->reason_code)
                //     ->get()
                //     ->count();
                // // dd($checkGPI);
                // // if result >=1 then update NDC_EXCEPTION_LISTS table record
                // //if result 0 then add NDC_EXCEPTION_LISTS record


                // if ($checkGPI <= "0") {
                //     $update = DB::table('REASON_CODE_LISTS')
                //     ->insert(
                //         [
                //             'reason_code_list_id' => $request->reason_code_list_id,
                //             'reject_code' => $request->reject_code,
                //             'reason_code' => $request->reason_code,
                //             'effective_date' => $request->effective_date,
                //             'termination_date' => $request->termination_date,
                //             'date_time_created' => $createddate,
                //             'date_time_modified' => $createddate,
                //         ]
                //     );
                       
                //     $update = DB::table('REASON_CODE_LISTS')->where('reason_code_list_id', 'like', '%' . $request->reason_code_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                // } else {


                //     $add_names = DB::table('REASON_CODE_LIST_NAMES')
                //         ->where('reason_code_list_id', $request->reason_code_list_id)
                //         ->update(
                //             [
                //                 'reason_code_name' => $request->reason_code_name,

                //             ]
                //         );

                //     $update = DB::table('REASON_CODE_LISTS')
                //     ->where('REASON_CODE_LIST_ID', $request->reason_code_list_id)
                //     ->where('REJECT_CODE', $request->reject_code)
                //     ->where('reason_code', $request->reason_code)
                //         ->update(
                //             [
                //                 'TERMINATION_DATE'=>$request->termination_date,

                //             ]
                //         );
                //     $update = DB::table('REASON_CODE_LISTS')->where('reason_code_list_id', 'like', '%' . $request->reason_code_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                // }



            }


        }
    }



    public function search(Request $request)
    {
        $ndc = DB::table('REASON_CODE_LISTS')
            ->join('REASON_CODE_LIST_NAMES', 'REASON_CODE_LISTS.REASON_CODE_LIST_ID', '=', 'REASON_CODE_LIST_NAMES.REASON_CODE_LIST_ID')
            ->where('REASON_CODE_LIST_NAMES.REASON_CODE_LIST_ID', 'like', '%' .$request->search. '%')
            ->orWhere('REASON_CODE_LISTS.REASON_CODE_LIST_ID', 'like', '%' . $request->search. '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCItemDetails($list_id,$reject_code,$reason_code,$efff)
    {
        $ndc = DB::table('REASON_CODE_LISTS')
            // ->select('NDC_EXCEPTION_LISTS.*', 'REASON_CODE_LIST_NAMES.NDC_EXCEPTION_LIST as exception_list', 'REASON_CODE_LIST_NAMES.EXCEPTION_NAME as exception_name')
            ->join('REASON_CODE_LIST_NAMES', 'REASON_CODE_LISTS.REASON_CODE_LIST_ID', '=', 'REASON_CODE_LIST_NAMES.REASON_CODE_LIST_ID')
            ->where('REASON_CODE_LISTS.REASON_CODE_LIST_ID', $list_id)
            ->where('REASON_CODE_LISTS.REJECT_CODE', $reject_code)
            ->where('REASON_CODE_LISTS.REASON_CODE', $reason_code)
            ->where('REASON_CODE_LISTS.EFFECTIVE_DATE', $efff)

            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function getList($id){

        $ndc = DB::table('REASON_CODE_LISTS')
        // ->select('NDC_EXCEPTION_LISTS.*', 'REASON_CODE_LIST_NAMES.NDC_EXCEPTION_LIST as exception_list', 'REASON_CODE_LIST_NAMES.EXCEPTION_NAME as exception_name')
            ->where('REASON_CODE_LIST_ID',$id)->first();

            return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function delete(Request $request)
    {
        if (isset($request->reason_code_list_id) && ($request->reason_code)) {
            $all_exceptions_lists =  DB::table('REASON_CODE_LISTS')
                ->where('REASON_CODE_LIST_ID', $request->reason_code_list_id)
                ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->reason_code_list_id)) {

            $exception_delete =  DB::table('REASON_CODE_LIST_NAMES')
                ->where('REASON_CODE_LIST_ID', $request->reason_code_list_id)
                ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}
