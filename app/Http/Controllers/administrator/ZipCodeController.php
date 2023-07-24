<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ZipCodeController extends Controller
{

    use AuditTrait;
    public function search(Request $request)
    {
        $zip_code_list = DB::table('ZIP_CODES')
            // ->where('ZIP_CODE', 'like', '%' . $request->search . '%')
            ->whereRaw('LOWER(ZIP_CODE) LIKE ?', ['%' . strtolower($request->search) . '%'])

            // ->orWhere(DB::raw('UPPER(CITY)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $zip_code_list);
    }

    public function getZipCodeList($zip_code)
    {
        // dd($zip_code);
        $zip_code_list = DB::table('ZIP_CODES')
            ->where('ZIP_CODE', '=', $zip_code)
            ->first();
        return $this->respondWithToken($this->token(), '', $zip_code_list);
    }


    public function submitFormData(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'zip_code' => ['required', 'string', 'max:10'],

        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {


            // dd($request->all());
            if ($request->has('new')) {

                $recordcheck = DB::table('ZIP_CODES')
                ->where('zip_code', $request->zip_code)
                ->first();

                if ($recordcheck) {
                        return $this->respondWithToken($this->token(), 'ZipCode Already Exists', $recordcheck, false);
                    }

                    else{

                        $addUser = DB::table('ZIP_CODES')
                        ->insert([
                            'ZIP_CODE' => $request->zip_code,
                            'CITY' => $request->city,
                            'STATE' => $request->state,
                            'COUNTY' => $request->county,
                            'COUNTRY_CODE' => $request->country_code,
                            'USER_ID' => $request->user_name
                        ]);
    
                    if ($addUser) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $addUser);
                    }

                    }

               
            } else {
                $updateUser = DB::table('ZIP_CODES')
                    ->where('ZIP_CODE', $request->zip_code)
                    ->update([
                        'CITY' => $request->city,
                        'STATE' => $request->state,
                        'COUNTY' => $request->county,
                        'COUNTRY_CODE' => $request->country_code,
                        'USER_ID' => $request->user_name
                    ]);

                if ($updateUser) {
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateUser);
                }
            }
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->zip_code)) {

            $zip_code_delete = DB::table('ZIP_CODES')
                ->where('ZIP_CODE', $request->zip_code)
                ->delete();

            if ($zip_code_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found!');
            }
        }
    }
}
