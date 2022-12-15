<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ZipCodeController extends Controller
{
    public function search(Request $request){
        // dd($request->search);
        $zip_code_list = DB::table('ZIP_CODES')
        ->where('ZIP_CODE','like', '%' .$request->search. '%')
        ->orWhere(DB::raw('UPPER(CITY)'),'like', '%' .strtoupper($request->search). '%')
        ->get();
        return $this->respondWithToken($this->token(), '', $zip_code_list);

    }

    public function getZipCodeList($zip_code){
        // dd($zip_code);
        $zip_code_list = DB::table('ZIP_CODES')
        ->where('ZIP_CODE','=', $zip_code)
        ->first();
        return $this->respondWithToken($this->token(), '', $zip_code_list);

    }

    public function submitFormData(Request $request){
        if ($request->has('new')) {
            $addUser = DB::table('ZIP_CODES')
                ->insert([
                    'user_id' => $request->user_id,
                    'ZIP_CODE' => $request->zip_code,
                    'CITY' => $request->city,
                    'STATE' => $request->state_code,
                    'COUNTY' => $request->county,
                    'COUNTRY_CODE' => $request->country_code,
                    'USER_ID' => Auth::user(),
                ]);

            if ($addUser) {
                return $this->respondWithToken($this->token(), 'Added Successfullt !!!', $addUser);
            }
        } else {
            $updateUser = DB::table('FE_USERS')
                ->where('user_id', $request->user_id)
                ->update([
                    'user_password' => $request->user_password,
                    'user_first_name' => $request->user_first_name,
                    'user_last_name' => $request->user_last_name,
                    'group_id' => $request->group_id
                ]);

            if ($updateUser) {
                return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateUser);
            }
        }
    }
}
