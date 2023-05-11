<?php

namespace App\Http\Controllers\validationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProviderController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $pharmacyExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                ->where('PHARMACY_LIST', 'like', '%' . $request->search . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . $request->search . '%')
                ->orderBy('PHARMACY_LIST', 'ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $pharmacyExceptionData);
        }
    }



    public function getProviderValidationList($pharmacy_list)
    {

        $pharmacyValidationData = DB::table('PHARMACY_VALIDATIONS')
            ->select('PHARMACY_TABLE.PHARMACY_NABP', 'PHARMACY_VALIDATIONS.PHARMACY_LIST', 'PHARMACY_VALIDATIONS.PHARMACY_STATUS', 'PHARMACY_EXCEPTIONS.EXCEPTION_NAME', 'PHARMACY_TABLE.PHARMACY_NAME')
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
            ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
            ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $pharmacy_list)->get();

        // ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $pharmacy_list)
        // ->get();



        return $this->respondWithToken($this->token(), '', $pharmacyValidationData);
    }


    public function getProviderDetails($pharmacy_list, $pharmacy_nabp, $status)
    {


        $data = DB::table('PHARMACY_VALIDATIONS')
            ->select('PHARMACY_VALIDATIONS.*', 'PHARMACY_TABLE.PHARMACY_NAME')
            ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
            ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $pharmacy_list)
            ->where('PHARMACY_VALIDATIONS.PHARMACY_NABP', $pharmacy_nabp)
            ->where('PHARMACY_VALIDATIONS.pharmacy_status', $status)
            ->first();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addProviderDatacopy(Request $request)
    {
        $getProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
            ->where('PHARMACY_LIST', $request->pharmacy_list)
            ->first();

        $getProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
            ->where('PHARMACY_LIST', $request->pharmacy_list)
            ->where('PHARMACY_NABP', $request->pharmacy_nabp)
            ->first();
        if ($request->has('new')) {
            $validator = Validator::make($request->all(), [
                "pharmacy_list" => [
                    'required',
                    'max:10', Rule::unique('PHARMACY_EXCEPTIONS')->where(function ($q) {
                        $q->whereNotNull('pharmacy_list');
                    })
                ],
                "exception_name" => ['max:35'],
                "pharmacy_nabp" => ['required'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if (!$getProviderExceptionData && !$getProviderValidationData) {
                    $addProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                        ->insert([
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'EXCEPTION_NAME' => $request->exception_name,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name
                        ]);

                    $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                        ->insert([
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'PHARMACY_NABP' => $request->pharmacy_nabp,
                            'PHARMACY_STATUS' => $request->pharmacy_status,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name
                        ]);

                    if ($addProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $addProviderExceptionData);
                    }
                } else {
                    if (!$getProviderValidationData) {
                        $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                            ->insert([
                                'PHARMACY_LIST' => $request->pharmacy_list,
                                'PHARMACY_NABP' => $request->pharmacy_nabp,
                                'PHARMACY_STATUS' => $request->pharmacy_status,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'USER_ID' => $request->user_name
                            ]);
                        if ($addProviderValidationData) {
                            return $this->respondWithToken($this->token(), 'Record Added Successfully', $addProviderValidationData);
                        }
                    } else {
                        return $this->respondWithToken($this->token(), 'This Pharmacy Validation ID Already Exists');
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                "pharmacy_list" => ['required', 'max:10'],
                "exception_name" => ['max:35'],
                "pharmacy_nabp" => ['required'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $updateProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                    ->where('PHARMACY_LIST', $request->pharmacy_list)
                    ->update([
                        'EXCEPTION_NAME' => $request->exception_name,
                        'DATE_TIME_MODIFIED' => date('d-M-y'),
                    ]);

                if (!$getProviderValidationData) {
                    $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                        ->insert([
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'PHARMACY_NABP' => $request->pharmacy_nabp,
                            'PHARMACY_STATUS' => $request->pharmacy_status,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name
                        ]);
                    if ($addProviderValidationData) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $addProviderValidationData);
                    }
                } else {
                    $updateProviderExceptionData = DB::table('PHARMACY_VALIDATIONS')
                        ->where('PHARMACY_LIST', $request->pharmacy_list)
                        ->where('PHARMACY_NABP', $request->pharmacy_nabp)
                        ->update([
                            'PHARMACY_STATUS' => $request->pharmacy_status,
                            'DATE_TIME_MODIFIED' => date('d-M-y')
                        ]);

                    if ($updateProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Record Update Successfully', $updateProviderExceptionData);
                    }
                }
            }
        }
    }

    public function addProviderData(Request $request)
    {
        $createddate = date('y-m-d');

        $validation = DB::table('PHARMACY_EXCEPTIONS')
            ->where('pharmacy_list', $request->pharmacy_list)
            ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'pharmacy_list' => [
                    'required',
                    'max:10', Rule::unique('PHARMACY_EXCEPTIONS')->where(function ($q) {
                        $q->whereNotNull('pharmacy_list');
                    })
                ],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'pharmacy_nabp' => ['required', 'max:10', Rule::unique('PHARMACY_VALIDATIONS')->where(function ($q) {
                //     $q->whereNotNull('pharmacy_nabp');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('NDC_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "exception_name" => ['required','max:36'],
                "pharmacy_nabp"=>['required','max:10'],
                "pharmacy_status"=>['max:10'],
                "DATE_TIME_CREATED"=>['max:10'],
                "DATE_TIME_MODIFIED"=>['max:10']
                

            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Procedure Exception Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('PHARMACY_EXCEPTIONS')->insert(
                    [
                        'PHARMACY_LIST' => $request->pharmacy_list,
                        'EXCEPTION_NAME' => $request->exception_name,

                    ]
                );

                $add = DB::table('PHARMACY_VALIDATIONS')
                    ->insert(
                        [
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'PHARMACY_NABP' => $request->pharmacy_nabp,
                            'PHARMACY_STATUS' => $request->pharmacy_status,
                            'DATE_TIME_CREATED' => $createddate,



                        ]
                    );


                $add = DB::table('PHARMACY_VALIDATIONS')->where('pharmacy_list', 'like', '%' . $request->pharmacy_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }



        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                // 'pharmacy_nabp' => ['required', 'max:10', Rule::unique('PHARMACY_VALIDATIONS')->where(function ($q) {
                //     $q->whereNotNull('pharmacy_nabp');
                // })],

                "EXCEPTION_NAME" => ['max:36'],
                "PHARMACY_NABP" => ['max:10'],
                "PHARMACY_STATUS" => ['max:10'],
                "DATE_TIME_CREATED" => ['max:10'],
                "DATE_TIME_MODIFIED" => ['max:10']

            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {



                $checknames = DB::table('PHARMACY_EXCEPTIONS')
                    ->where('pharmacy_list', $request->pharmacy_list)
                    ->get();


                $effective_date_check = DB::table('PHARMACY_VALIDATIONS')
                    ->where('pharmacy_list', $request->pharmacy_list)
                    ->where('pharmacy_nabp', $request->pharmacy_nabp)
                    ->where('pharmacy_status', $request->pharmacy_status)
                    ->get()

                    ->count();


                $insert_check = DB::table('PHARMACY_VALIDATIONS')
                    ->where('pharmacy_list', $request->pharmacy_list)
                    ->pluck('pharmacy_nabp')->toArray();


                // dd($effective_date_check);


                if (in_array($request->pharmacy_nabp, $insert_check)) {
                    $add_names = DB::table('PHARMACY_EXCEPTIONS')
                    ->where('pharmacy_list', $request->pharmacy_list)
                    ->update([
                        'exception_name' => $request->exception_name,
                    ]);



                $update = DB::table('PHARMACY_VALIDATIONS')
                    ->where('pharmacy_list', $request->pharmacy_list)
                    ->where('pharmacy_nabp', $request->pharmacy_nabp)
                    ->update(
                        [
                            'pharmacy_status' => $request->pharmacy_status,


                        ]
                    );
                $update = DB::table('PHARMACY_VALIDATIONS')->where('pharmacy_list', 'like', '%' . $request->pharmacy_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                } else {

                    $updated = DB::table('PHARMACY_VALIDATIONS')
                        ->insert(
                            [
                                'PHARMACY_LIST' => $request->pharmacy_list,
                                'PHARMACY_NABP' => $request->pharmacy_nabp,
                                'PHARMACY_STATUS' => $request->pharmacy_status,
                                'DATE_TIME_CREATED' => $createddate,

                            ]
                        );

                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $updated);
                }


            }






        }

    }


    public function searchDropDownProviderList($pharmacy_list = '')
    {
        $data = DB::table('PHARMACY_TABLE')
            ->where('PHARMACY_NABP', 'LIKE', '%' . $pharmacy_list . '%')
            ->orWhere('PHARMACY_NAME', 'LIKE', '%' . $pharmacy_list . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getProviderOptions(Request $request)
    {
        $provider_options = [
            ['provider_id' => '', 'provider_name' => 'Not Specified'],
            ['provider_id' => 'N', 'provider_name' => 'NONE (no provider check)'],
            ['provider_id' => 'F', 'provider_name' => 'Validate Provider Format'],
            ['provider_id' => 'M', 'provider_name' => 'Must Exist Within Provider Master'],
            ['provider_id' => 'P', 'provider_name' => 'Must Exist Nn Provider Network'],
            ['provider_id' => 'V', 'provider_name' => 'Validate Provider In/Out Of Network'],
        ];

        return $this->respondWithToken($this->token(), '', $provider_options);
    }
}