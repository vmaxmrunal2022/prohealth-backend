<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use App\Models\administrator\UserDefinition;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule as ValidationRule;
use Symfony\Component\Console\Input\Input;
use Throwable;
use Auth;
use Illuminate\Contracts\Session\Session;

class UserDefinationController extends Controller
{

    public function addUserDefinition(Request $request)
    {

        $getusersData = DB::table('FE_USERS')
            ->where('user_id', $request->user_id)
            ->first();

        if ($request->has('new')) {

            if ($getusersData) {

                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getusersData);
            } else {


                $addData = DB::table('FE_USERS')
                    ->insert([
                        'user_id' => $request->user_id,
                        'user_password' => $request->user_password,
                        'user_first_name' => $request->user_first_name,
                        'user_last_name' => $request->user_last_name,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'group_id' => $request->group_id,
                        'application' => $request->application,
                        'user_password' => $request->user_password,
                        'sql_server_user_id' => $request->sql_server_user_id,
                        'sql_server_user_password' => $request->sql_server_user_password,
                        'privs' => $request->privs,

                    ]);

                $addData2 = DB::table('FE_USERS_PASSWORD_HISTORY')

                    ->insert([
                        'USER_ID' => $request->user_id,
                        'APPLICATION' => $request->application,

                    ]);

                if ($addData) {
                    return $this->respondWithToken($this->token(), 'Added Successfully!!!', $addData);
                }
            }
        } else { {
                $updateUser = DB::table('FE_USERS')
                    ->where('user_id', $request->user_id)
                    ->update([
                        'user_password' => $request->user_password,
                        'user_first_name' => $request->user_first_name,
                        'user_last_name' => $request->user_last_name,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'group_id' => $request->group_id,
                        'application' => $request->application,
                        'user_password' => $request->user_password,
                        'sql_server_user_id' => $request->sql_server_user_id,
                        'sql_server_user_password' => $request->sql_server_user_password,
                        'privs' => $request->privs,
                    ]);

                $updateUser = DB::table('FE_USERS_PASSWORD_HISTORY')
                    ->where('user_id', $request->user_id)
                    ->update([
                        'APPLICATION' => $request->application,
                        'USER_PASSWORD' => $request->user_password,
                        'ENCRYPTION_TYPE' => $request->encryption_type,
                    ]);




                if ($updateUser) {
                    return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateUser);
                }
            }
        }
    }

    public function get(Request $request)
    {
        $userDefination = DB::table('FE_USERS')
            // ->join( 'FE_USER_GROUPS', 'FE_USER_GROUPS.group_id', '=', 'FE_USERS.group_id' )
            // ->join( 'customer', 'customer.user_id', '=', 'fe_users.user_id' )
            // ->join( 'CLIENT_GROUP', 'CLIENT_GROUP.user_id', '=', 'fe_users.user_id' )
            ->where(DB::raw('UPPER(FE_USERS.user_id)'), 'like', '%' . strtoupper($request->search . '%'))
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
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', Rule::unique('fe_users')->where(function ($q) {
                $q->whereNotNull('user_id');
            })],
            'user_password' => ['required'],
        ]);
        $prefix = '$2y$';
        $cost = '10';
        $salt = '$thisisahardcodedsalt$';
        $blowfishPrefix = $prefix . $cost . $salt;
        $password = $request->user_password;
        $hash = crypt($password, $blowfishPrefix);
        $hashToThirdParty = substr($hash, -32);
        $hashFromThirdParty = $hashToThirdParty;

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        } else  if ($request->has('new')) {
            //$addUser = DB::table('FE_USERS')->insert([
            $addUser = UserDefinition::insert([
                'user_id' => $request->user_id,
                'application' => 'PBM',
                'SQL_SERVER_USER_ID' => 'phi',
                'SQL_SERVER_USER_PASSWORD' => 'comet',
                //'user_password' => $request->user_password,
                'user_password' => $hashFromThirdParty,
                'user_first_name' => $request->user_first_name,
                'user_last_name' => $request->user_last_name,
                'group_id' => $request->group_id,
                'user_id_created' => $request->session()->get('user')
            ]);

            //TODO
            // 1.Table name
            // 2. record action
            // 3. record snapshot log
            $addUserLog = DB::table('FE_RECORD_LOG')->insert([
                'user_id' => $request->user_id,
                'application' => 'PBM',
                'date_created' => date('Ymd'),
                'time_created' => date('HiA'),
                // 'SQL_SERVER_USER_ID' => 'phi',
                // 'SQL_SERVER_USER_PASSWORD' => 'comet',
                // 'user_password' => $request->user_password,
                // 'user_first_name' => $request->user_first_name,
                // 'user_last_name' => $request->user_last_name,
                // 'group_id' => $request->group_id,
            ]);

            if ($addUser) {
                return $this->respondWithToken($this->token(), 'Added Successfully !!!', $addUser);
            }
        } else {
            $updateUser = DB::table('FE_USERS')
                ->where('user_id', $request->user_id)
                ->update([
                    'user_password' => $request->user_password,
                    'user_first_name' => $request->user_first_name,
                    'user_last_name' => $request->user_last_name,
                    'group_id' => $request->group_id,
                    'user_id_created' => $request->session()->get('user')
                ]);
            //TODO
            // 1.Table name
            // 2. record action
            // 3. record snapshot log
            $updateUserLog = DB::table('FE_RECORD_LOG')->insert([
                'user_id' => $request->user_id,
                'application' => 'PBM',
                'date_created' => date('Ymd'),
                'time_created' => date('HiA'),
                // 'SQL_SERVER_USER_ID' => 'phi',
                // 'SQL_SERVER_USER_PASSWORD' => 'comet',
                // 'user_password' => $request->user_password,
                // 'user_first_name' => $request->user_first_name,
                // 'user_last_name' => $request->user_last_name,
                // 'group_id' => $request->group_id
            ]);

            if ($updateUser) {
                return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateUser);
            }
        }

        // $prefix = '$2y$';
        // $cost = '10';
        // $salt = '$thisisahardcodedsalt$';
        // $blowfishPrefix = $prefix . $cost . $salt;
        // $password = $request->user_password;
        // $hash = crypt($password, $blowfishPrefix);
        // $hashToThirdParty = substr($hash, -32);
        // $hashFromThirdParty = $hashToThirdParty;

        // if ($request->has('new')) {
        //     //$addUser = DB::table('FE_USERS')->insert([
        //     $addUser = UserDefinition::insert([
        //         'user_id' => $request->user_id,
        //         'application' => 'PBM',
        //         'SQL_SERVER_USER_ID' => 'phi',
        //         'SQL_SERVER_USER_PASSWORD' => 'comet',
        //         //'user_password' => $request->user_password,
        //         'user_password' => $hashFromThirdParty,
        //         'user_first_name' => $request->user_first_name,
        //         'user_last_name' => $request->user_last_name,
        //         'group_id' => $request->group_id
        //     ]);

        //     //TODO
        //     // 1.Table name
        //     // 2. record action
        //     // 3. record snapshot log
        //     $addUserLog = DB::table('FE_RECORD_LOG')->insert([
        //         'user_id' => $request->user_id,
        //         'application' => 'PBM',
        //         'date_created' => date('Ymd'),
        //         'time_created' => date('HiA'),
        //         // 'SQL_SERVER_USER_ID' => 'phi',
        //         // 'SQL_SERVER_USER_PASSWORD' => 'comet',
        //         // 'user_password' => $request->user_password,
        //         // 'user_first_name' => $request->user_first_name,
        //         // 'user_last_name' => $request->user_last_name,
        //         // 'group_id' => $request->group_id
        //     ]);

        //     if ($addUser) {
        //         return $this->respondWithToken($this->token(), 'Added Successfully !!!', $addUser);
        //     }
        // } else {
        //     $updateUser = DB::table('FE_USERS')
        //         ->where('user_id', $request->user_id)
        //         ->update([
        //             'user_password' => $request->user_password,
        //             'user_first_name' => $request->user_first_name,
        //             'user_last_name' => $request->user_last_name,
        //             'group_id' => $request->group_id
        //         ]);
        //     //TODO
        //     // 1.Table name
        //     // 2. record action
        //     // 3. record snapshot log
        //     $updateUserLog = DB::table('FE_RECORD_LOG')->insert([
        //         'user_id' => $request->user_id,
        //         'application' => 'PBM',
        //         'date_created' => date('Ymd'),
        //         'time_created' => date('HiA'),
        //         // 'SQL_SERVER_USER_ID' => 'phi',
        //         // 'SQL_SERVER_USER_PASSWORD' => 'comet',
        //         // 'user_password' => $request->user_password,
        //         // 'user_first_name' => $request->user_first_name,
        //         // 'user_last_name' => $request->user_last_name,
        //         // 'group_id' => $request->group_id
        //     ]);

        //     if ($updateUser) {
        //         return $this->respondWithToken($this->token(), 'Updated Successfully !!!', $updateUser);
        //     }
        // }
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

        $array = $customers->map(
            function ($obj) {
                return (array) $obj;
            }
        )->toArray();

        return $this->respondWithToken($this->token(), '', $array);
    }

    public function getCustomersList(Request $request)
    {
        $list  = DB::table('customer')
            ->join('client', 'client.user_id', '=', 'customer.user_id')
            ->join('CLIENT_GROUP', 'customer.user_id', '=', 'CLIENT_GROUP.user_id')
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
        // print_r( Auth::id() );
        // exit();
        if ($request->user_id == 'undefined') {
            $client_groups = DB::table('CLIENT_GROUP')
                ->select('CLIENT_GROUP_ID as value', 'GROUP_NAME as label')
                // ->where( 'CLIENT_GROUP_ID', 'like', '%' . strtoupper( $request->search ) . '%' )
                // ->orWhere( 'CLIENT_NAME', 'like', '%' . strtoupper( $request->search ) . '%' )
                ->get();
        } else {
            $client_groups = DB::table('CLIENT_GROUP')
                ->select('CLIENT_GROUP_ID as value', 'GROUP_NAME as label')
                // ->where( 'user_id', 'like', '%' . $request->user_id . '%' )
                // ->where( 'CLIENT_GROUP_ID', 'like', '%' . strtoupper( $request->search ) . '%' )
                // ->orWhere( 'GROUP_NAME', 'like', '%' . strtoupper( $request->search ) . '%' )
                ->get();
        }

        return $this->respondWithToken($this->token(), '', $client_groups);
    }

    public function submitGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => ['required', Rule::unique('FE_USER_GROUPS')->where(function ($q) {
                $q->whereNotNull('group_id');
            })],
            'customer_id' => ['required'],
            'status' => ['required'],
            'exclude_flag' => ['required']
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        } else if ($request->add_new_group) {
            $validate = DB::table('FE_USER_GROUPS')
                ->where(DB::raw('UPPER(GROUP_ID)'), strtoupper($request->group_id))
                ->get()
                ->count();
            if ($validate <= "0") {
                $add_fe_group = DB::table('FE_USER_GROUPS')
                    ->insert([
                        'group_id' => $request->group_id,
                        'group_name' => $request->group_name,
                        'user_profile' => $request->user_profile
                    ]);

                return $this->respondWithToken($this->token(), 'Added Successfully!', $add_fe_group);
            } else {
                return $this->respondWithToken($this->token(), 'Something went wrong!');
            }
        } else {
            $update_fe_group = DB::table('FE_USER_GROUPS')
                ->where(DB::raw('UPPER(group_id)'), strtoupper($request->group_id))
                ->update([
                    'group_name' => $request->group_name,
                    'user_profile' => $request->user_profile
                ]);
            return $this->respondWithToken($this->token(), 'Updated Successfully!', $update_fe_group);
        }
    }

    public function getGroupIds(Request $request)
    {
        $group_ids = DB::table('fe_user_groups')
            ->get();
        return $this->respondWithToken($this->token(), '', $group_ids);
    }
}
