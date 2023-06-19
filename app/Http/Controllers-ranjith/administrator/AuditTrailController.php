<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PDO;

class AuditTrailController extends Controller
{
    public function getTables(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'search' => ['required']
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        } else {
            $sql = DB::table('all_tables')->select('table_name')->where('owner', 'PHIDBA')
                ->where(DB::raw('UPPER(table_name)'), 'like', '%' . strtoupper($request->search) . '%')->get();
            return $this->respondWithToken($this->token(), '', $sql);
        }
    }

    public function getUserAllRecord(Request $request)
    {
        /***************************** */
        $user_record = DB::table('FE_RECORD_LOG')
            ->where('user_id', $request->user_id)
            ->where('table_name', $request->table_name)
            ->orderBy('date_created', 'desc')
            ->where('date_created', date('Ymd', strtotime($request->date_created)))
            ->get();

        $results = DB::table('PHIDBA.FE_RECORD_LOG')
            ->whereRaw("get_record_snapshot_from_fe_record_log(rowid) like '%" . substr($request->record_snapshot, 0, 30) . "%'")
            ->where('table_name', $request->table_name)
            ->orderBy(
                'DATE_CREATED',
                'DESC'
            )
            ->orderBy('TIME_CREATED', 'DESC')
            ->take(2000)
            ->get();

        $table_name = $request->table_name;
        $customer_id = isset(json_decode($request->record_snapshot)->customer_id) ? json_decode($request->record_snapshot)->customer_id : null;
        $client_id = isset(json_decode($request->record_snapshot)->client_id)  ? json_decode($request->record_snapshot)->client_id : null;
        $client_group_id = isset(json_decode($request->record_snapshot)->client_group_id)  ? json_decode($request->record_snapshot)->client_group_id : null;
        $record = DB::table($request->table_name)
            ->when($customer_id, function ($query) use ($customer_id) {
                return $query->where('customer_id', 'like', '%' . $customer_id . '%');
            })
            ->when($client_id, function ($query) use ($client_id) {
                return $query->where('client_id', 'like', '%' . $client_id . '%');
            })
            ->when($client_group_id, function ($query) use ($client_group_id) {
                return $query->where('client_group_id', 'like', '%' . $client_group_id . '%');
            })
            ->get();

        $old_column_arr = [];
        $old_value_arr = [];
        foreach (count($results) > 1 ? json_decode($results[1]->record_snapshot) :
            json_decode($results[0]->record_snapshot)  as $key => $val) {
            $arr2 = $key;
            $value = $val;
            array_push($old_column_arr, $arr2);
            // array_push($old_value_arr, $value);
        }
        $old_value_arr = count($results) > 1 ? json_decode($results[1]->record_snapshot) : null;

        $new_column_arr = [];
        $new_value_arr = [];
        foreach (json_decode($results[0]->record_snapshot) as $key => $val) {
            $arr2 = $key;
            $value = $val;
            array_push($new_column_arr, $arr2);
            array_push($new_value_arr, $value);
        }


        /***************************** */


        $get_column = DB::table($request->table_name)->get();
        $column_arr = [];
        foreach ($get_column[0] as $key => $val) {
            $arr2 = $key;
            array_push($column_arr, $arr2);
        }

        $current_record = [];
        foreach ($record[0] as $key => $val) {
            $ar = $val;
            array_push($current_record, $ar);
        }

        // $data = ['user_record' => $user_record[0], 'record_snapshot' => $current_record, 'old_record' => $old_value_arr, 'columns' => $old_column_arr];
        $data = ['user_record' => $user_record[0], 'record_snapshot' => $record[0], 'old_record' => $old_value_arr, 'columns' => $old_column_arr];
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function check_query(Request $request)
    {
        // return $request->all();
        $results = DB::table('FE_RECORD_LOG')
            ->select('*')
            ->orderBy('DATE_CREATED', 'desc')
            ->orderBy('TIME_CREATED', 'desc')
            ->where('table_name', '=', $request->table_name)
            ->take(2)
            ->get();

        return $results;
    }

    public function getUserAllRecord_mrunal(Request $request)
    {
        // return ($request->all());

        // $sql = 'select *from vu_FE_RECORD_LOG where record_snapshot like "%' . $request->record_snapshot . '"% ';
        // $query = DB::select($sql);
        // dd($query);
        $results = DB::table('FE_RECORD_LOG')
            ->select('*')
            ->orderBy('DATE_CREATED', 'desc')
            ->orderBy('TIME_CREATED', 'desc')
            ->where(
                'table_name',
                '=',
                $request->table_name
            )
            ->take(2)
            ->get();

        return $results;

        //working code DONT TOUCH HERE
        $get_column = DB::table($request->table_name)->get();
        $column_arr = [];
        foreach ($get_column[0] as $key => $val) {
            $arr2 = $key;
            array_push($column_arr, $arr2);
        }
        // dd($column_arr);

        $record = DB::table('FE_RECORD_LOG')
            ->where('user_id', $request->user_id)
            ->where('table_name', $request->table_name)
            ->orderBy('date_created', 'desc')
            ->where('date_created', $request->date_created)
            ->get();
        //$record[0] => latest
        //$record[1] => old


        $old_record = DB::connection('oracle')
            ->table('vu_FE_RECORD_LOG')
            //->where('record_snapshot', 'like', '%' . $request->record_snapshot . '%')
            //->where(DB::raw('(record_snapshot)'), 'like', '%' . $request->record_snapshot . '%')
            // ->where('name', 'John')
            // ->orWhere('name', 'Jane')
            ->where('user_id', $request->user_id)
            ->where('table_name', $request->table_name)
            ->orderBy('date_created', 'desc')
            ->orderBy('time_created', 'desc')
            ->where('table_name', $request->table_name)

            ->take(2)
            ->get();

        // dd($old_record);
        // print_r($old_record);
        // echo count($old_record);
        if (!empty(count($old_record))) {
            $count = count($old_record);
            if ($count > 1) {
                // echo "in if";
                // $new_snapshot = explode('|', $record[0]->record_snapshot);
                $new_snapshot = explode('|', $old_record[0]->record_snapshot);
                $old_snapshot = explode('|', $old_record[1]->record_snapshot);
                // $old_snapshot = [];
            } else {
                // echo "in else";
                //$new_snapshot = explode('|', $record[0]->record_snapshot);
                $new_snapshot = explode('|', $old_record[0]->record_snapshot);
                $old_snapshot = explode('|', $old_record[0]->record_snapshot);
            }
        } else {
            $new_snapshot = [];
            $old_snapshot = [];
        }



        //$new_snapshot = explode('|', $record[0]->record_snapshot);

        // $old_snapshot = explode('|', $record[1]->record_snapshot);
        // $new_snapshot = explode('|', $old_record[0]->record_snapshot);


        // $old_snapshot = explode('|', $old_record[1]->record_snapshot);



        // dd(similar_text($record[1]->record_snapshot, $record[0]->record_snapshot, $percent));
        //$data = ['user_record' => $user_record, 'record_snapshot' => $record_snapshot, 'old_record' => $old_snap, 'columns' => $col_array];
        $data = ['user_record' => $record[0], 'record_snapshot' => $new_snapshot, 'old_record' => $old_snapshot, 'columns' => $column_arr];
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getUserIds(Request $request)
    {
        $sql = DB::table('fe_users')->select('user_id')
            ->where(DB::raw('UPPER(user_id)'), 'like', '%' . strtoupper($request->search) . '%')->get();
        return $this->respondWithToken($this->token(), '', $sql);
    }

    public function getRecordAction(Request $request)
    {
        $value = ['IN', 'UP', 'DE'];

        $label = ['Insert', 'Update', 'Delete'];

        $data = [$value, $label];


        return $this->respondWithToken($this->token(), '', $data);
    }

    public function searchUserLog(Request $request)
    {
        // print_r($request->to_date);
        if ($request->from_date != null) {
            $fromDate = str_replace('-', '', $request->from_date);
        } else {
            $fromDate = null;
        }

        // if ($request->to_date) {
        //     $toDate = str_replace('-', '', $request->to_date);
        //     print_r($toDate, "toDate");
        // } else {
        //     $toDate = null;
        //     // $toDate = str_replace('-', '', $request->to_date);
        // }
        $toDate = str_replace('-', '', $request->to_date);

        if ($request->user_id != null) {
            $user_id = $request->user_id['uvalue'];
        } else {
            $user_id = null;
        }

        if ($request->record_action != null) {
            $record_action = $request->record_action['rvalue'];
        } else {
            $record_action = null;
        }
        // print_r($toDate);
        // print_r($request->to_date, "todate");

        $search = DB::table('FE_RECORD_LOG')
            ->where('table_name', $request->table_name['value'])
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('user_id', 'like', '%' . $user_id . '%');
            })

            ->when($record_action, function ($query) use ($record_action) {
                return $query->where('record_action', 'like', '%' . $record_action . '%');
            })

            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->where('date_created', '>=', $fromDate);
            })

            ->when($toDate, function ($query) use ($toDate) {
                return $query->where('date_created', '<=', $toDate);
            })

            ->orderBy('date_created', 'desc')
            ->get();

        return $this->respondWithToken($this->token(), '', $search);
    }

    public function getOldUserLog(Request $request)
    {
        $old_user_log_data = DB::table('FE_RECORD_LOG')
            ->where('user_id', $request->user_id)
            ->where('record_action', $request->record_action)
            ->where('table_name', $request->table_name)
            ->get();
        return $this->respondWithToken($this->token(), '', $old_user_log_data);
    }
}