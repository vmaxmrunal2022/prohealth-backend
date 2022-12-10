<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDefinationController extends Controller
{
    public function get(Request $request)
    {
        // $userDefination = DB::table('FE_USERS')
        //                   ->join('FE_USER_GROUPS', 'FE_USER_GROUPS.group_id', '=', 'FE_USERS.group_id')
        //                   ->join('customer', 'customer.user_id', '=', 'fe_users.user_id')
        //                   ->join('CLIENT_GROUP', 'CLIENT_GROUP.user_id', '=', 'fe_users.user_id')                          
        //                   ->where('FE_USERS.user_id', 'like', '%'. $request->search .'%')
        //                   ->orWhere('FE_USERS.USER_FIRST_NAME', 'like', '%'. $request->search .'%')
        //                   ->orWhere('FE_USERS.USER_LAST_NAME', 'like', '%'. $request->search .'%')
        //                   ->get();

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

    public function getGroupData(Request $request)
    {
        $groupData = DB::table('FE_USER_GROUPS')->get();

        return $this->respondWithToken($this->token(),'', $groupData);
    }

    public function getSecurityOptions(Request $request)
    {
        
    }

    public function validateGroup(Request $request)
    {
        $validate = DB::table('FE_USER_GROUPS')
                    ->where('group_id', 'like','%'.$request->search.'%')
                    ->get()
                    ->count();
        return $this->respondWithToken($this->token(),'',$validate);
    }

    public function submitFormData(Request $request)
    {
        if($request->has('new'))
        {
            $addUser = DB::table('FE_USERS') 
                       ->insert([
                        'user_id' => $request->user_id,
                        'application' => 'PBM',
                        'SQL_SERVER_USER_ID' => 'phi',
                        'SQL_SERVER_USER_PASSWORD' => 'comet',  
                        'user_password' => $request->user_password,
                        'user_first_name' => $request->user_first_name,
                        'user_last_name' => $request->user_last_name,
                        'group_id' => $request->group_id
                       ]);

            if($addUser)
            {
                return $this->respondWithToken($this->token(), 'Added Successfullt !!!', $addUser);
            }
        }else{
            $updateUser = DB::table('FE_USERS')
                          ->where('user_id', $request->user_id)
                          ->update([
                            'user_password' => $request->user_password,
                            'user_first_name' => $request->user_first_name,
                            'user_last_name' => $request->user_last_name,
                            'group_id' => $request->group_id
                          ]);

            if($updateUser)
            {
                return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateUser);
            }
        }
    }

    public function getCustomers(Request $request)
    {
        $customers = DB::table('customer')                     
                    //  ->join('client', 'customer.user_id', '=','client.user_id')
                    //  ->join('client_group', 'customer.user_id', '=','client_group.user_id')
                     ->where('customer_id', 'like', '%'.$request->search.'%')
                     ->get();

        return $this->respondWithToken($this->token(), '', $customers);
    }

    public function getCustomersList(Request $request)
    {
        $list  = DB::table('customer')
                 ->join('client', 'client.user_id', '=', 'customer.user_id')
                //  ->join('CLIENT_GROUP', 'customer.user_id', '=', 'CLIENT_GROUP.user_id')
                 ->where('customer.user_id', 'like', '%'. $request->search.'%')
                 ->get();

        return $this->respondWithToken($this->token(), '', $list);
    }
}
