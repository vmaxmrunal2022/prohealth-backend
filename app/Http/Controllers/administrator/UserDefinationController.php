<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDefinationController extends Controller
{
    public function get(Request $request)
    {
        $userDefination = DB::table('FE_USERS')
                          ->join('FE_USER_GROUPS', 'FE_USER_GROUPS.group_id', '=', 'FE_USERS.group_id')
                          ->join('customer', 'customer.user_id', '=', 'fe_users.user_id')
                          ->join('CLIENT_GROUP', 'CLIENT_GROUP.user_id', '=', 'fe_users.user_id')                          
                          ->where('FE_USERS.user_id', 'like', '%'. $request->search .'%')
                          ->orWhere('FE_USERS.USER_FIRST_NAME', 'like', '%'. $request->search .'%')
                          ->orWhere('FE_USERS.USER_LAST_NAME', 'like', '%'. $request->search .'%')
                          ->get();

        return $this->respondWithToken($this->token(), '', $userDefination);
    }
}
