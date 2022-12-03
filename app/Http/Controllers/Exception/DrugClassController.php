<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugClassController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('TC_EXCEPTIONS')
                ->select('THER_CLASS_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('THER_CLASS_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getTCList($ndcid)
    {
        $ndclist = DB::table('TC_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('THER_CLASS_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getTCItemDetails($ndcid)
    {
        $ndc = DB::table('TC_EXCEPTION_LISTS')
                    ->select('TC_EXCEPTION_LISTS.*', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST as exception_list', 'TC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->join('TC_EXCEPTIONS', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST', '=', 'TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')
                    ->where('TC_EXCEPTION_LISTS.therapy_class', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
