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
        $requesta = Crypt::decryptString('U2FsdGVkX18vhdbpIbCdrKAwHeUevqKu2higsiJ2izHjGg0v8trOx4ywt/QDxN5RQUVfqbM9L0Q/zWjauQhk1perZPqf+uoerdm3/nF2iuPIJoKeyhs1gtTYzif676APDVuaD9flH8X4hFaLVGL7oOe83a+2GN9mnTjYnm/Rupsfvtjv1bzZbauOvan5uGe7TrdzCS8j5+rNUZc1cGCxFture3NCEjxPAi8+f2r0bnMXhr0PcDRx81ig1caS2NoiqgIaby98WA6R4+Fncbpqjt2m8D+IZ9m9nzsOuam9eO6ojVPzUIZmBSEI36ONZCFK5MehA9Qq320JpMjGfxPF7c0B0cds9uoC2uytKMmUr6pLo8pVjPM09ZbCyX4Bmwk0ztpeAOMn8B6CJTyO1wWxMxNhW6CLzf8aelp6s1K+VHKHUoVjlulgA9apcV++7J/S');
        print_r($requesta); exit;
        if ($request->has('new')) {
            $addUser = DB::table('ZIP_CODES')
                ->insert([
                    'ZIP_CODE' => $request->zip_code,
                    'CITY' => $request->city,
                    'STATE' => $request->state_code,
                    'COUNTY' => $request->county,
                    'COUNTRY_CODE' => $request->country_code['value'],
                    'USER_ID' => $request->user_name
                ]);

            if ($addUser) {
                return $this->respondWithToken($this->token(), 'Added Successfullt !!!', $addUser);
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
