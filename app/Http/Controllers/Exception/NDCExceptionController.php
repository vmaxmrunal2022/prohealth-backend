<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NDCExceptionController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
                ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('NDC_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('NDC_EXCEPTION_LISTS')
                    ->select('NDC_EXCEPTION_LISTS.*', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST as exception_list', 'NDC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
                    ->where('NDC_EXCEPTION_LISTS.NDC', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
