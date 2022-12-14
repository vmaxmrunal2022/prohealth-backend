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
            ->where('FE_USERS.user_id', 'like', '%' . $request->search . '%')
            ->orWhere('FE_USERS.USER_FIRST_NAME', 'like', '%' . $request->search . '%')
            ->orWhere('FE_USERS.USER_LAST_NAME', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $userDefination);
    }

    public function getGroupData(Request $request)
    {
        $groupData = DB::table('FE_USER_GROUPS')->get();

        return $this->respondWithToken($this->token(), '', $groupData);
    }

    public function getSecurityOptions(Request $request)
    {
    }

    public function validateGroup(Request $request)
    {
        $validate = DB::table('FE_USER_GROUPS')
            ->where('group_id', 'like', '%' . $request->search . '%')
            ->get()
            ->count();
        return $this->respondWithToken($this->token(), '', $validate);
    }

    public function submitFormData(Request $request)
    {
        if ($request->has('new')) {
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

    public function getCustomers(Request $request)
    {
        if ($request->user_id == 'undefined') {
            $customers = DB::table('customer')
                ->select('customer_id as value', 'customer_name as label')
                ->where('customer_id', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('customer_name', 'like', '%' . strtoupper($request->search) . '%')
                ->get();
        } else {
            $customers = DB::table('customer')
                ->select('customer_id as value', 'customer_name as label')
                ->where('user_id', 'like', '%' . $request->user_id . '%')
                ->where('customer_id', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('customer_name', 'like', '%' . strtoupper($request->search) . '%')
                ->get();
        }


        $array = $customers->map(function ($obj) {
            return (array) $obj;
        })->toArray();

        return $this->respondWithToken($this->token(), '', $array);
    }

    public function getCustomersList(Request $request)
    {
        $list  = DB::table('customer')
            ->join('client', 'client.user_id', '=', 'customer.user_id')
            //  ->join('CLIENT_GROUP', 'customer.user_id', '=', 'CLIENT_GROUP.user_id')
            ->where('customer.user_id', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $list);
    }

    public function getClients(Request $request)
    {
        if ($request->user_id == 'undefined') {
            $clients = DB::table('client')
                ->select('client_id as value', 'client_name as label')
                ->where('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('CLIENT_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();
        } else {
            $clients = DB::table('client')
                ->select('client_id as value', 'client_name as label')
                ->where('user_id', 'like', '%' . $request->user_id . '%')
                ->where('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('CLIENT_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();
        }

        return $this->respondWithToken($this->token(), '', $clients);
    }

    public function getClientGroups(Request $request)
    {
        if ($request->user_id == 'undefined') {
            $client_groups = DB::table('CLIENT_GROUP')
                ->select('CLIENT_GROUP_ID as value', 'GROUP_NAME as label')
                // ->where('CLIENT_GROUP_ID', 'like', '%' . strtoupper($request->search) . '%')
                // ->orWhere('CLIENT_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();
        } else {
            $client_groups = DB::table('CLIENT_GROUP')
                ->select('CLIENT_GROUP_ID as value', 'GROUP_NAME as label')
                // ->where('user_id', 'like', '%' . $request->user_id . '%')
                // ->where('CLIENT_GROUP_ID', 'like', '%' . strtoupper($request->search) . '%')
                // ->orWhere('GROUP_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();
        }


        return $this->respondWithToken($this->token(), '', $client_groups);
    }
}
