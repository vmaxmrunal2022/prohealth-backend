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
                ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
                ->where(DB::raw('UPPER(EXCEPTION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orderBy('PHYSICIAN_LIST', 'ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $physicianExceptionData);
        }
    }



    public function getProviderValidationList($physician_list)
    {
        $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS as a')
            // ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME','a.EXCEPTION_NAME')
            ->join('PHYSICIAN_TABLE as b ', 'b.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
            ->join('PHYSICIAN_EXCEPTIONS','PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST','=','a.PHYSICIAN_LIST')
            ->where('a.PHYSICIAN_LIST', 'like', '%' . $physician_list . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $physician_validation_list);
    }


    public function getProviderDetails($physicain_list, $physicain_id)
    {
        $data = DB::table('PHYSICIAN_VALIDATIONS as a')
            ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.EXCEPTION_NAME', 'c.PHYSICIAN_LAST_NAME', 'c.PHYSICIAN_FIRST_NAME')
            ->join('PHYSICIAN_EXCEPTIONS as b', 'b.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
            ->join('PHYSICIAN_TABLE as c', 'c.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
            ->where('a.PHYSICIAN_LIST',  $physicain_list)
            ->where('a.PHYSICIAN_ID',  $physicain_id)
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
        if ($request->has('new')) {
            $validator = Validator::make($request->all(), [
                "physician_list" => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('physician_list');
                })],
                "exception_name" => ['max:35'],
                "physician_id" => ['required'],
                "physician_status" => ['max:1'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
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
                        return $this->respondWithToken($this->token(), 'Added Successfully ...!!!', $addProviderExceptionData);
                    }
                } else {
                    if (!$getProviderValidationData) {
                        $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                            ->insert([
                                'PHYSICIAN_LIST' => $request->physician_list,
                                'PHYSICIAN_ID' => $request->physician_id['value'],
                                'PHYSICIAN_STATUS' => $request->physician_status,
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
                "physician_list" => ['required', 'make:10'],
                "exception_name" => ['max:35'],
                "physician_id" => ['required'],
                "physician_status" => ['max:1'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where('PHYSICIAN_LIST', $request->physician_list)
                    ->update([
                        'EXCEPTION_NAME' => $request->exception_name,
                        'DATE_TIME_MODIFIED' => date('d-M-y'),
                    ]);

                if (!$getProviderValidationData) {
                    $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                        ->insert([
                            'PHYSICIAN_LIST' => $request->physician_list,
                            'PHYSICIAN_ID' => $request->physician_id,
                            'PHYSICIAN_STATUS' => $request->physician_status,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name
                        ]);
                    if ($addProviderValidationData) {
                        return $this->respondWithToken($this->token(), 'Added Successfully ...!!!', $addProviderValidationData);
                    }
                } else {
                    $updateProviderExceptionData = DB::table('PHYSICIAN_VALIDATIONS')
                        ->where('PHYSICIAN_LIST', $request->physician_list)
                        ->where('PHYSICIAN_ID', $request->physician_id)
                        ->update([
                            'PHYSICIAN_STATUS' => $request->physician_status,
                            'DATE_TIME_MODIFIED' => date('d-M-y'),
                        ]);

                    if ($updateProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Update Successfully.. !!!', $updateProviderExceptionData);
                    }
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
