<?php

namespace App\Http\Controllers\Speciality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SpecialityController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $data = DB::table('SPECIALTY_EXCEPTIONS')
                // ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
                ->select('SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', 'SPECIALTY_EXCEPTIONS.EXCEPTION_NAME')
                ->where(DB::raw('UPPER(SPECIALTY_EXCEPTIONS.SPECIALTY_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
                ->get();
            return $this->respondWithToken($this->token(), '', $data);
        }
    }
    public function getAll(Request $request)
    {
       
        $data = DB::table('SPECIALTY_EXCEPTIONS')
            // ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
            ->select('SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', 'SPECIALTY_EXCEPTIONS.EXCEPTION_NAME')
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
        
    }

    public function specialityValidationList(){
        $ndclist = DB::table('SPECIALTY_VALIDATIONS')
        ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getSpecialityList($specialty_id)
    {
        $ndclist = DB::table('SPECIALTY_VALIDATIONS')
        ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
            ->where('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', '=', $specialty_id)
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getSpecialityDetails($specialty_id, $specialty_list)
    {
        $ndc = DB::table('SPECIALTY_VALIDATIONS')
            ->join('SPECIALTY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS.SPECIALTY_LIST', '=', 'SPECIALTY_VALIDATIONS.SPECIALTY_LIST')
            ->where('SPECIALTY_VALIDATIONS.SPECIALTY_ID', '=', $specialty_id)
            ->where('SPECIALTY_VALIDATIONS.SPECIALTY_LIST', '=', $specialty_list)
            ->first();
        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function addSpeciality(Request $request)
    {
        if ($request->has('new')) {
            $validator = Validator::make($request->all(), [
                "specialty_list" => ['required', 'max:10', Rule::unique('SPECIALTY_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('specialty_list');
                })],
                "exception_name" => ['max:35'],
                "specialty_status" => ['max:1']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $getSpecialtyExceptionData = DB::table('SPECIALTY_EXCEPTIONS')
                    ->where(DB::raw('UPPER(SPECIALTY_LIST)'), '=', strtoupper($request->specialty_list))
                    ->first();

                $getSpecialtyValidationData = DB::table('SPECIALTY_VALIDATIONS')
                    ->where(DB::raw('UPPER(SPECIALTY_LIST)'), '=', strtoupper($request->specialty_list))
                    ->where(DB::raw('UPPER(SPECIALTY_ID)'), '=', strtoupper($request->specialty_id))
                    ->first();


                if (!$getSpecialtyExceptionData && !$getSpecialtyValidationData) {
                    $addData = DB::table('SPECIALTY_EXCEPTIONS')
                        ->insert([
                            'SPECIALTY_LIST' => $request->specialty_list,
                            'EXCEPTION_NAME' => $request->exception_name,
                            'USER_ID' => $request->user_name,
                        ]);

                    if ($addData) {
                        $data = DB::table('SPECIALTY_VALIDATIONS')
                            ->insert([
                                'SPECIALTY_LIST' => $request->specialty_list,
                                'SPECIALTY_ID' => $request->specialty_id,
                                'SPECIALTY_STATUS' => $request->specialty_status,
                                'USER_ID' => $request->user_name,
                            ]);

                        return $this->respondWithToken($this->token(), 'Added Successfully..!!!', $addData);
                    }
                } else {
                    if (!$getSpecialtyValidationData) {
                        $data = DB::table('SPECIALTY_VALIDATIONS')
                            ->insert([
                                'SPECIALTY_LIST' => $request->specialty_list,
                                'SPECIALTY_ID' => $request->specialty_id,
                                'SPECIALTY_STATUS' => $request->specialty_status,
                                'USER_ID' => $request->user_name,
                            ]);
                        return $this->respondWithToken($this->token(), 'Added Successfully..!!!', $data);
                    } else {
                        return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getSpecialtyValidationData);
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                "specialty_list" => ['required', 'max:10'],
                "exception_name" => ['max:35'],
                "specialty_status" => ['max:1']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $updateData = DB::table('SPECIALTY_EXCEPTIONS')
                    ->where('SPECIALTY_LIST', $request->specialty_list)
                    ->update([
                        'EXCEPTION_NAME' => $request->exception_name
                    ]);
                if ($updateData) {
                    $data = DB::table('SPECIALTY_VALIDATIONS')
                        ->where('SPECIALTY_LIST', $request->specialty_list)
                        ->where('SPECIALTY_ID', $request->specialty_id)
                        ->update([
                            'SPECIALTY_STATUS' => $request->specialty_status
                        ]);
                    return $this->respondWithToken($this->token(), 'Updated Successfully..!!!', $updateData);
                }
            }
        }
    }
}
