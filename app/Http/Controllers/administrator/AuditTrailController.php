<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

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

    public function getUserAllRecord_old(Request $request)
    {
        // dd($request->table_name);
        $user_record = DB::table('FE_RECORD_LOG')
            ->where('user_id', $request->user_id)
            ->where('record_action', $request->record_action)
            ->where('time_created', $request->time_created)
            ->where('date_created', $request->date_created)
            ->where('table_name', $request->table_name)
            ->first();

        // $old = DB::table('FE_RECORD_LOG')
        //     ->where('user_id', $request->user_id)
        //     ->where('record_action', $request->record_action)
        //     ->where('time_created', '<', $user_record->time_created)
        //     ->where('date_created', $request->date_created)
        //     ->where('table_name', $request->table_name)
        //     ->first();

        $old = DB::table('FE_RECORD_LOG')
            ->where('user_id', $request->user_id)
            // ->where('record_action', $request->record_action)
            // ->where('time_created', '<', $user_record->time_created)
            // ->where('date_created', $request->date_created)
            ->orderBy('date_created', 'desc')
            ->where('table_name', $request->table_name)
            ->limit(2)
            ->get();

        $columns = DB::table('all_tab_cols')->select('column_name')->where('owner', 'PHIDBA')
            ->where('table_name', $request->table_name)->get();

        // $columns = DB::table('all_tab_cols')
        //     // ->select('column_name')
        //     // ->where('table_name', $request->table_name)
        //     //     ->where('owner', 'PHIDBA')
        //     // ->where('table_name', 'CUSTOMER')
        //     ->get();


        // dd($columns);
        $col_array = [];
        foreach ($columns as  $col) {
            $arr = $col->column_name;
            // $arr = $key;
            array_push($col_array, $arr);
        }

        // dd($col_array);



        $record_snapshot = explode('|', $user_record->record_snapshot);

        $old_snap = explode('|', $old[1]->record_snapshot);
        // dd($old_snap);
        $data = ['user_record' => $user_record, 'record_snapshot' => $record_snapshot, 'old_record' => $old_snap, 'columns' => $col_array];
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getUserAllRecord(Request $request)
    {
        // dd($request->date_created);
        $date_for = strtoupper(date("d-M-y H:i:s", strtotime($request->date_created)));
        // dd($date_for);
        // 20230407
        $check_date = DB::table($request->table_name)
            ->select('date_time_modified')
            // ->where(DB::raw('UPPER(date_time_modified)'), strtoupper(date("d-M-y H:i:s", strtotime($request->date_created))))
            // ->where('date_time_modified', '2023-04-07 00:00:00')
            ->where('date_time_modified', '2023-04-07 00:00:00')
            ->get();

        // dd($check_date);

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
            ->get();
        //$record[0] => latest
        //$record[1] => old


        $new_snapshot = explode('|', $record[0]->record_snapshot);

        $old_snapshot = explode('|', $record[1]->record_snapshot);



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
