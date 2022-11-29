<?php

namespace App\Http\Controllers\membership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class MemberController extends Controller
{
    public function get(Request $request)
    {
        // $memberData = DB::table('member')
        //               ->where('CUSTOMER_ID', 'like', '%'. strtoupper($request->search) .'%')
                    //   ->orWhere('CLIENT_ID', 'like', '%'. strtoupper($request->search) .'%')
                    //   ->orWhere('MEMBER_LAST_NAME', 'like', '%'. strtoupper($request->search) .'%')
                    //   ->orWhere('MEMBER_FIRST_NAME', 'like', '%'. strtoupper($request->search) .'%')
                    //   ->orWhere('DATE_OF_BIRTH', 'like', '%'. strtoupper($request->search) .'%')
        //               ->get();

        $memberData = FacadesDB::table('MEMBER')
                      ->where('CUSTOMER_ID', 'like', '%'.strtoupper($request->search).'%')  
                      ->get();

        return $this->respondWithToken($this->token(), '', json_encode($memberData));
    }
}
