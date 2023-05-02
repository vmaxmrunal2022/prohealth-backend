<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PrescriberValidationController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $physicianExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('PHYSICIAN_LIST', 'like', '%' . $request->search . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . $request->search . '%')
                ->orderBy('PHYSICIAN_LIST', 'ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $physicianExceptionData);
        }
    }

    public function getProviderValidationList($physician_list)
    {
        $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS as a')
            // ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME','a.EXCEPTION_NAME')
            // ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME','a.EXCEPTION_NAME')
            ->join('PHYSICIAN_TABLE as b ', 'b.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
            ->where('a.PHYSICIAN_LIST', 'like', '%' . $physician_list . '%')
            ->get();

       

        return $this->respondWithToken($this->token(), '', $physician_validation_list[0]);
    }


    public function getProviderDetails($physicain_list, $physicain_id)
    {
        $data = DB::table('PHYSICIAN_VALIDATIONS as a')
            ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.EXCEPTION_NAME', 'c.PHYSICIAN_LAST_NAME', 'c.PHYSICIAN_FIRST_NAME')
            ->join('PHYSICIAN_EXCEPTIONS as b', 'b.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
            ->join('PHYSICIAN_TABLE as c', 'c.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
            ->where('a.PHYSICIAN_LIST', $physicain_list)
            ->where('a.PHYSICIAN_ID', $physicain_id)
            ->first();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addPrescriberDatacopy(Request $request)
    {
        $getProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->first();

        $getProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->where('PHYSICIAN_ID', $request->physician_id)
            ->first();

        $recordcheck = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('physician_id', $request->physician_id)
            ->first();




        if ($request->has('new')) {

            if ($getProviderExceptionData && $getProviderExceptionData ) {
                return $this->respondWithToken($this->token(), 'Prescriber List Id  Already Existed', $recordcheck, false);

            } else {


                


                if (!$getProviderExceptionData && !$getProviderValidationData) {

                    $addProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->insert([
                            'PHYSICIAN_LIST' => $request->physician_list,
                            'EXCEPTION_NAME' => $request->exception_name,
                            'USER_ID' => $request->user_name,
                            'DATE_TIME_CREATED' => date('d-M-y')
                        ]);

                    $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                        ->insert([
                            'PHYSICIAN_LIST' => $request->physician_list,
                            'PHYSICIAN_ID' => $request->physician_id,
                            'PHYSICIAN_STATUS' => $request->physician_status,
                            'USER_ID' => $request->user_name,
                            'DATE_TIME_CREATED' => date('d-M-y')
                        ]);

                    if ($addProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $addProviderExceptionData);
                    }


                }
            }



        } else if($request->updateForm == 'update') {


            $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('PHYSICIAN_LIST', $request->physician_list)
                ->update([
                    'EXCEPTION_NAME' => $request->exception_name,
                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                ]);

            if ($updateProviderExceptionData) {
                $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where('PHYSICIAN_ID', $request->physician_id)
                    ->where('PHYSICIAN_LIST',$request->physician_list)


                    ->update([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        // 'PHYSICIAN_ID' => $request->physician_id,
                        'PHYSICIAN_STATUS' => $request->physician_status,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'USER_ID' => $request->user_name
                    ]);
                if ($addProviderValidationData) {
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $addProviderValidationData);
                }
            }
        }
    }

    public function addPrescriberData(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('PHYSICIAN_EXCEPTIONS')
        ->where('physician_list',$request->physician_list)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'physician_list' => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('physician_list');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "EXCEPTION_NAME" => ['max:36'],
                "PHARMACY_NABP"=>['max:10'],
                "PHARMACY_STATUS"=>['max:10'],
                "DATE_TIME_CREATED"=>['max:10'],
                "DATE_TIME_MODIFIED"=>['max:10']
                
            

            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'NDC Exception Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('PHYSICIAN_EXCEPTIONS')->insert(
                    [
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'EXCEPTION_NAME'=>$request->exception_name,
                        
                    ]
                );
    
                $add = DB::table('PHYSICIAN_VALIDATIONS')
                    ->insert([
    
                        
                            'PHYSICIAN_LIST' =>$request->physician_list,
                            'PHYSICIAN_ID'=>$request->physician_id,
                            'PHYSICIAN_STATUS'=>$request->physician_status,
                            'DATE_TIME_CREATED'=>$createddate,
                            
                        
                        
                    ]);
    
                $add = DB::table('PHYSICIAN_VALIDATIONS')->where('physician_list', 'like', '%' . $request->physician_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

               


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }
    
                $update_names = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('physician_list', $request->physician_list )
                ->first();
                    
    
                $checkexists = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where('physician_list', $request->physician_list)
                    ->where('physician_id',$request->physician_id)
                    ->get();
                    // dd($checkGPI);
                // if result >=1 then update NDC_EXCEPTION_LISTS table record
                //if result 0 then add NDC_EXCEPTION_LISTS record

    
              

            if (count($checkexists) >= 1) {
                $update_names = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('physician_list', $request->physician_list )
                ->update([

                    'exception_name'=>$request->exception_name,

                ]);
                return $this->respondWithToken($this->token(), 'Record Already Existed!', $validation, false, 404, 0);
            } else {

                $checkexists = DB::table('PHYSICIAN_VALIDATIONS')
                    ->insert(
                        [
                            'physician_list' => $request->physician_list,
                            'physician_id' => $request->physician_id,
                            'PHYSICIAN_STATUS' => $request->physician_status,
                            'DATE_TIME_CREATED' => $createddate,

                        ]
                    );


                $add = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where('physician_list', $request->physician_list)
                    ->where('physician_id', $request->physician_id)

                    ->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }
    
               

            }

           
        }
    }





    public function searchDropDownPrescriberList()
    {
        $data = DB::table('PHYSICIAN_TABLE')
            ->orWhere('PHYSICIAN_LAST_NAME', 'LIKE', '%' . strtoupper('campB') . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }
}