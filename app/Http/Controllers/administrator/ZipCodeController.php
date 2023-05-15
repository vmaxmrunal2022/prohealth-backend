<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ZipCodeController extends Controller
{
    public function search(Request $request)
    {
        $zip_code_list = DB::table('ZIP_CODES')
            ->where('ZIP_CODE', 'like', '%' . $request->search . '%')
            ->orWhere(DB::raw('UPPER(CITY)'), 'like', '%' . strtoupper($request->search) . '%')
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
        // dd($request->all());
        $record = DB::table('ZIP_CODES')
        ->where('ZIP_CODE', $request->zip_code)
        ->first();


        if ($request->has('new')) {

            $validator = Validator::make($request->all(), [
                'zip_code' => ['required','max:9'],
                'city' => ['max:18'],
                'state' => ['max:2'],
                'user_id' => ['max:10'],
                'form_id' => ['max:10'],
                'county' => ['max:20'],
                'country_code' => ['max:4'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            if($record){

                return $this->respondWithToken($this->token(), 'ZipCode Already Exists', $record);


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

            $validator = Validator::make($request->all(), [
                'zip_code' => ['required','max:9'],
                'city' => ['max:18'],
                'state' => ['max:2'],
                'user_id' => ['max:10'],
                'form_id' => ['max:10'],
                'county' => ['max:20'],
                'country_code' => ['max:4'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }
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
