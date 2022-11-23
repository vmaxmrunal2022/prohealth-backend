<?php

namespace App\Http\Controllers\exception_list;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrcedureCodeListController extends Controller
{
    public function get(Request $request)
    {
        // $providerCodeList = DB::table('PROC_CODE_LISTS')
        //                     ->join('PROC_CODE_LIST_NAMES','PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID','=','PROC_CODE_LISTS.PROC_CODE_LIST_ID')
        //                     ->where('PROC_CODE_LISTS.PROC_CODE_LIST_ID','like','%'.strtoupper($request->search).'%')
        //                     ->orWhere('PROC_CODE_LISTS.PROCEDURE_CODE','like','%'.strtoupper($request->search).'%')
        //                     ->get();
        $providerCodeList = DB::table('PROC_CODE_LIST_NAMES')
                            ->where('PROC_CODE_LIST_ID','like','%'.strtoupper($request->search).'%')
                            ->orWhere('DESCRIPTION','like','%'.strtoupper($request->search).'%')
                            ->get();
        return $this->respondWithToken($this->token(), '', $providerCodeList);
    }

    public function getProcCodeList(Request $request)
    {
        $providerCodeList = DB::table('PROC_CODE_LISTS')
                            ->join('PROC_CODE_LIST_NAMES','PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID','=','PROC_CODE_LISTS.PROC_CODE_LIST_ID')
                            ->where('PROC_CODE_LISTS.PROC_CODE_LIST_ID','like','%'.strtoupper($request->search).'%')
                                ->get();
        return $this->respondWithToken($this->token(), '', $providerCodeList);
    }
}
