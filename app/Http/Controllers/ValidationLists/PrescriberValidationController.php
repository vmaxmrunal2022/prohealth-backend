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

<<<<<<< HEAD
    public function prescriberValidationList()
    {
        $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS')
            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST')
            ->get();
        if ($physician_validation_list) {
            return $this->respondWithToken($this->token(), '', $physician_validation_list);
        } else {
            return $this->respondWithToken($this->token(), 'Data Not Found', [], false);
        }
    }

=======
>>>>>>> a9c9f87266fc8b49e1ab6bb8498c7a11b2a69760
    public function getProviderValidationList($physician_list)
    {
        $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS as a')
            // ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME','a.EXCEPTION_NAME')
            // ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME','a.EXCEPTION_NAME')
            ->join('PHYSICIAN_TABLE as b ', 'b.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
            ->where('a.PHYSICIAN_LIST', 'like', '%' . $physician_list . '%')
            ->get();

<<<<<<< HEAD
        return $this->respondWithToken(
            $this->token(),
            '',
            $physician_validation_list
        );
        $physician_validation_list = DB::table('PHYSICIAN_TABLE')
            // ->select('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME')
            ->join('PHYSICIAN_VALIDATIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST')
            ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $physician_list)
            ->get();
=======
       
>>>>>>> d598b3a8b36f783c7698d872ad45f84ca0592df4

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

    public function addPrescriberData(Request $request)
    {
        $getProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->first();

        $getProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->where('PHYSICIAN_ID', $request->physician_id)
            ->first();

<<<<<<< HEAD
        $recordcheck = DB::table('PHYSICIAN_EXCEPTIONS')
            ->where('PHYSICIAN_LIST', strtoupper($request->physician_list))
=======
        $recordcheck = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('physician_id', $request->physician_id)
>>>>>>> d598b3a8b36f783c7698d872ad45f84ca0592df4
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

<<<<<<< HEAD
            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'Prescriber List Id  Already Existed', $recordcheck, false);
            }



            // $validator = Validator::make($request->all(), [
            //     "physician_list" => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
            //         $q->whereNotNull('physician_list');
            //     })],
            //     "exception_name" => ['max:35'],
            //     "physician_id" => ['required'],
            //     "physician_status" => ['max:1'],
            // ]);

            // if ($validator->fails()) {
            //     return $this->respondWithToken($this->token(), $validator->errors()->first(),[],false);
            // } else {


            else {

                if (!$getProviderExceptionData && !$getProviderValidationData) {

                    $addProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->insert([
                            'PHYSICIAN_LIST' => strtoupper($request->physician_list),
                            'EXCEPTION_NAME' => $request->exception_name,
                            'USER_ID' => $request->user_name,
                            'DATE_TIME_CREATED' => date('d-M-y')
                        ]);

<<<<<<< HEAD
=======
                }
               
            }
        // } else {
        //     $validator = Validator::make($request->all(), [
        //         "physician_list" => ['required', 'max:10'],
        //         "physician_list" => ['required', 'max:10'],
        //         "exception_name" => ['max:35'],
        //         "physician_id" => ['required'],
        //         "physician_status" => ['max:1'],
        //     ]);
        //     if ($validator->fails()) {
        //         return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        //      }
            //   else {
            //     $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
        // } else {
            // $validator = Validator::make($request->all(), [
            //     "physician_list" => ['required', 'max:10'],
            //     "exception_name" => ['max:35'],
            //     "physician_id" => ['required'],
            //     "physician_status" => ['max:1'],
            // ]);
            // if ($validator->fails()) {
            //     return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            // } else {


                else{


                    $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where('PHYSICIAN_LIST', $request->physician_list)
                    ->update([
                        'EXCEPTION_NAME' => $request->exception_name,
                        'DATE_TIME_MODIFIED' => date('d-M-y'),
                    ]);

                if ($updateProviderExceptionData) {
>>>>>>> a9c9f87266fc8b49e1ab6bb8498c7a11b2a69760
                    $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                        ->insert([
                            'PHYSICIAN_LIST' => strtoupper($request->physician_list),
                            'PHYSICIAN_ID' => $request->physician_id,
                            'PHYSICIAN_STATUS' => $request->physician_status,
                            'USER_ID' => $request->user_name,
                            'DATE_TIME_CREATED' => date('d-M-y')
                        ]);
=======
                    if ($addProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $addProviderExceptionData);
                    }

>>>>>>> d598b3a8b36f783c7698d872ad45f84ca0592df4

                    if ($addProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully ...!!!', $addProviderExceptionData);
                    }
                }
<<<<<<< HEAD
            }
        }
        // } else {
        // $validator = Validator::make($request->all(), [
        //     "physician_list" => ['required', 'max:10'],
        //     "exception_name" => ['max:35'],
        //     "physician_id" => ['required'],
        //     "physician_status" => ['max:1'],
        // ]);
        // if ($validator->fails()) {
        //     return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        // } else {


        else {


            $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('PHYSICIAN_LIST', $request->physician_list)
                ->update([
                    'EXCEPTION_NAME' => $request->exception_name,
                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                ]);

            if ($updateProviderExceptionData) {
                $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where('PHYSICIAN_LIST', $request->physician_list)

                    ->update([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'PHYSICIAN_ID' => $request->physician_id,
                        'PHYSICIAN_STATUS' => $request->physician_status,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'USER_ID' => $request->user_name
                    ]);
                if ($addProviderValidationData) {
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully ...!!!', $addProviderValidationData);
                }
            }
        }

        // }
        // }
=======
>>>>>>> d598b3a8b36f783c7698d872ad45f84ca0592df4
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





    public function searchDropDownPrescriberList()
    {
        $data = DB::table('PHYSICIAN_TABLE')
            ->orWhere('PHYSICIAN_LAST_NAME', 'LIKE', '%' . strtoupper('campB') . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }
}