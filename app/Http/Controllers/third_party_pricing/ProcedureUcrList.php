<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeUnit\FunctionUnit;

class ProcedureUcrList extends Controller
{
    public function get(Request $request)
    {
        $ucrName = DB::table('procedure_ucr_names')
                   ->where('PROCEDURE_UCR_ID', 'like', '%'.strtoupper($request->search).'%')
                   ->orWhere('DESCRIPTION', 'like', '%'.strtoupper($request->search).'%')
                   ->get();

        return $this->respondWithToken($this->token(), '', $ucrName);
    }

    public function getProcedureListData(Request $request)
    {
        $ucrList = DB::table('procedure_ucr_names')
                   ->join('procedure_ucr_list', 'procedure_ucr_names.procedure_ucr_id', '=', 'procedure_ucr_list.procedure_ucr_id')
                   ->where('procedure_ucr_list.procedure_ucr_id', $request->search)
                   ->get();
        return $this->respondWithToken($this->token(), '', $ucrList);
    }
}
