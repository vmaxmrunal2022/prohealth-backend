<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditTrailController extends Controller
{
    public function getTables(Request $request)
    {
        $sql = DB::table('all_tables')->select('table_name')->where('owner', 'PHIDBA')
            ->where(DB::raw('UPPER(table_name)'), 'like', '%' . strtoupper($request->search) . '%')->get();
        return $this->respondWithToken($this->token(), '', $sql);
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
        if ($request->to_date != null) {
            $toDate = str_replace('-', '', $request->to_Date);
        } else {
            $toDate = '';
        }
        if ($request->from_date != null) {
            $fromDate = str_replace('-', '', $request->from_date);
        } else {
            $fromDate = '';
        }

        // print($fromDate);
        // print($toDate);
        // $countries = DB::table('COUNTRY_STATES')->where(DB::raw('UPPER(DESCRIPTION)'), 'like','%'.strtoupper($c_id).'%')->get();
        $search = DB::table('FE_RECORD_LOG')
            ->where('table_name', $request->table_name['value'])
            ->where('user_id', $request->user_id['uvalue'])
            ->where('record_action', $request->record_action['rvalue'])
            // ->where('DATE_CREATED', '>=', $fromDate)
            // ->where('DATE_CREATED', '<=', $toDate)
            // ->Where(DB::raw('date("Y-m-d", DATE_CREATED)'), '>=', $request->from_date)
            // ->Where(DB::raw('date("Y-m-d", DATE_CREATED)'), '<=', $request->to_date)
            // ->whereBetween('DATE_CREATED',[$fromDate, $toDate])
            ->get();
        // print_r($search);

        return $this->respondWithToken($this->token(), '', $search);
    }
}
