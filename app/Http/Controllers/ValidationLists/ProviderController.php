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
                ->where(DB::raw('UPPER(PHARMACY_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(EXCEPTION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orderBy('PHARMACY_LIST', 'ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $pharmacyExceptionData);
        }
    }



    public function getProviderValidationList($pharmacy_list)
    {

        $pharmacyValidationData = DB::table('PHARMACY_VALIDATIONS')
            // ->select('PHARMACY_TABLE.PHARMACY_NABP', 'PHARMACY_VALIDATIONS.PHARMACY_LIST', 'PHARMACY_VALIDATIONS.PHARMACY_STATUS', 'PHARMACY_NAME')
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
            ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
            ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $pharmacy_list)
            ->get();

        return $this->respondWithToken($this->token(), '', $pharmacyValidationData);
    }


    public function getProviderDetails($pharmacy_list, $pharmacy_nabp)
    {
        $data = DB::table('PHARMACY_VALIDATIONS as a')
            ->select('a.PHARMACY_LIST', 'a.PHARMACY_NABP', 'a.PHARMACY_STATUS', 'c.PHARMACY_NAME', 'b.EXCEPTION_NAME')
            ->join('PHARMACY_EXCEPTIONS as b', 'b.PHARMACY_LIST', '=', 'a.PHARMACY_LIST')
            ->join('PHARMACY_TABLE as c', 'c.PHARMACY_NABP', '=', 'a.PHARMACY_NABP')
            ->where('a.PHARMACY_LIST',  $pharmacy_list)
            ->where('a.PHARMACY_NABP',  $pharmacy_nabp)
            ->first();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addProviderData(Request $request)
    {
        $getProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
            ->where(DB::raw('UPPER(PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
            ->first();

        $getProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
            ->where('PHARMACY_LIST', $request->pharmacy_list)
            ->where('PHARMACY_NABP', $request->pharmacy_nabp)
            ->first();
        if ($request->has('new')) {
            $validator = Validator::make($request->all(), [
                "pharmacy_list" => ['required', 'max:10', Rule::unique('PHARMACY_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('pharmacy_list');
                })],
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
                        return $this->respondWithToken($this->token(), 'Added Successfully ...!!!', $addProviderExceptionData);
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
                            return $this->respondWithToken($this->token(), 'Added Successfully ...!!!', $addProviderValidationData);
                        }
                    } else {
                        return $this->respondWithToken($this->token(), 'This record is already exists ..!!!');
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
                        return $this->respondWithToken($this->token(), 'Added Successfully ...!!!', $addProviderValidationData);
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
                        return $this->respondWithToken($this->token(), 'Update Successfully.. !!!', $updateProviderExceptionData);
                    }
                }
            }
        }
    }


    public function searchDropDownProviderList($pharmacy_list = '')
    {
        $data = DB::table('PHARMACY_TABLE')
            ->where('PHARMACY_NABP', 'LIKE', '%' . strtoupper($pharmacy_list) . '%')
            ->orWhere('PHARMACY_NAME', 'LIKE', '%' . strtoupper($pharmacy_list) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }
}
