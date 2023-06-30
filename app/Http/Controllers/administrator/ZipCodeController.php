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
        if ($request->has('new')) {
            $addUser = DB::table('ZIP_CODES')
                ->insert([
                    'ZIP_CODE' => $request->zip_code,
                    'CITY' => $request->city,
                    'STATE' => $request->state_code['value'],
                    'COUNTY' => $request->county,
                    'COUNTRY_CODE' => $request->country_code['value'],
                    'USER_ID' => $request->user_name
                ]);

            if ($addUser) {
                return $this->respondWithToken($this->token(), 'Added Successfully !!!', $addUser);
            }
        } else {
            $updateUser = DB::table('ZIP_CODES')
                ->where('ZIP_CODE', $request->zip_code)
                ->update([
                    'CITY' => $request->city,
                    'STATE' => $request->state_code['value'],
                    'COUNTY' => $request->county,
                    'COUNTRY_CODE' => $request->country_code['value'],
                    'USER_ID' => $request->user_name
                ]);

            if ($updateUser) {
                return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateUser);
            }
        }
    }
}
