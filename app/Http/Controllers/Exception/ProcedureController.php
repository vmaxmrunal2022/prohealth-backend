<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcedureController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('PROCEDURE_EXCEPTION_NAMES')
                ->select('PROCEDURE_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('PROCEDURE_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getPCList($ndcid)
    {
        $ndclist = DB::table('PROCEDURE_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('PROCEDURE_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getPCItemDetails($ndcid)
    {
        $ndc = DB::table('PROCEDURE_EXCEPTION_LISTS')
                    ->select('PROCEDURE_EXCEPTION_LISTS.*', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST as exception_list', 'PROCEDURE_EXCEPTION_NAMES.EXCEPTION_NAME as exception_name')
                    ->join('PROCEDURE_EXCEPTION_NAMES', 'PROCEDURE_EXCEPTION_NAMES.PROCEDURE_EXCEPTION_LIST', '=', 'PROCEDURE_EXCEPTION_LISTS.PROCEDURE_EXCEPTION_LIST')
                    ->where('PROCEDURE_EXCEPTION_LISTS.PROC_CODE_LIST_ID', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
