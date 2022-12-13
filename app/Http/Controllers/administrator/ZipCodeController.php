<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ZipCodeController extends Controller
{
    public function search(Request $request){
        // dd($request->search);
        $zip_code_list = DB::table('ZIP_CODES')
        ->where('ZIP_CODE','like', '%' .$request->search. '%')
        ->orWhere('CITY','like', '%' .$request->search. '%')
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
}
