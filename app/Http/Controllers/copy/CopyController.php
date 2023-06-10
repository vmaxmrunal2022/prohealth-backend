<?php

namespace App\Http\Controllers\copy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopyController extends Controller
{
    public function viewCopy(Request $request)
    {
        return "hello controller";
    }

    public function getUniqueId(Request $request)
    {
        $table_name = $request[0];
        $uniqueColumns = DB::table('all_ind_columns')
            ->select('column_name')
            ->whereIn('index_name', function ($query) use ($table_name) {
                $query->select('index_name')
                    ->from('all_indexes')
                    ->where('table_name', $table_name)
                    ->where('uniqueness', 'UNIQUE');
            })
            ->where('table_name', $table_name)
            ->orderBy('column_position', 'desc')
            // ->latest()
            ->first();

        // $selectedColumns = [];
        // $selectedColumns[] = DB::raw($column->column_name . ' AS `' . $column->column_name . '`');

        $selectedColumns = DB::raw($uniqueColumns->column_name . ' AS ' . 'source_id' . '');



        $table_data = DB::table($table_name)
            ->select($selectedColumns)
            ->get();
        return $this->respondWithToken($this->token(), '', $table_data);
    }

    public function submitCopy(Request $request)
    {
        $uniqueColumns = DB::table('all_ind_columns')
            ->select('column_name')
            ->whereIn('index_name', function ($query) use ($request) {
                $query->select('index_name')
                    ->from('all_indexes')
                    ->where('table_name', $request->table_name)
                    ->where('uniqueness', 'UNIQUE');
            })
            ->where('table_name', $request->table_name)
            ->orderBy('column_position', 'desc')
            // ->latest()
            ->first();

        $ifExists = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->destination_id)
            ->get()
            ->count();
        // return $ifExists;
        if (strtoupper($request->source_id) == strtoupper($request->destination_id) || $ifExists >= 1) {
            return $this->respondWithToken($this->token(), 'Record Already Exists', '', false);
        }



        $get_source_record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->source_id)
            ->first();

        //chatgpt  code
        // return $uniqueColumns->column_name;
        $sourceCustomer = $request->source_id;
        $destinationCustomer = $request->destination_id;

        $record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $sourceCustomer)
            ->first();

        $newRecord = (array) $record;
        $newRecord[$uniqueColumns->column_name] = $destinationCustomer;

        $excludedColumns = [$uniqueColumns->column_name]; // Add any other duplicate column names here

        $columns = array_diff(array_keys($newRecord), [strtolower($excludedColumns[0])]);

        // return array_intersect_key($newRecord, $columnsToCopy);
        $copy_source_to_dest = DB::table($request->table_name)
            ->insert(array_intersect_key($newRecord, array_flip($columns)));
            
        $get_dest_record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->destination_id)
            ->first();

        return $this->respondWithToken($this->token(), 'Record Cloned Successfully', $get_dest_record);
    }
}
