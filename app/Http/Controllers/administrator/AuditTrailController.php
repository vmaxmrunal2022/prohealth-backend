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
        // print_r($request->all());

        if ($request->from_date != null) {
            $fromDate = str_replace('-', '', $request->from_date);
        } else {
            $fromDate = '';
        }
        if ($request->to_date != null) {
            $toDate = str_replace('-', '', $request->to_Date);
        } else {
            $toDate = '';
        }
        // print($fromDate);
        // print((date('Y-m-d', $fromDate)));
        $search = DB::table('FE_RECORD_LOG')
            ->where('table_name', $request->table_name['value'])
            ->Where('user_id', $request->user_id['uvalue'])
            ->Where('record_action', $request->record_action['rvalue'])
            //   ->Where('DATE_CREATED', '>=', $fromDate)
            //   ->Where('DATE_CREATED', '<=' ,$toDate)
            // ->Where(date('Y-m-d','DATE_CREATED'), '>=', $fromDate)
            // ->Where(date('Y-m-d','DATE_CREATED'), '<=', $toDate)
            // ->whereBetween('DATE_CREATED',[$fromDate, $toDate])
            ->get();
        //   print_r($search);

        return $this->respondWithToken($this->token(), '', $search);
    }
}
