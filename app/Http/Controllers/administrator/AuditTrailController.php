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
            'search' => ['required']
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        } else {
            $sql = DB::table('all_tables')->select('table_name')->where('owner', 'PHIDBA')
                ->where(DB::raw('UPPER(table_name)'), 'like', '%' . strtoupper($request->search) . '%')->get();
            return $this->respondWithToken($this->token(), '', $sql);
        }
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
