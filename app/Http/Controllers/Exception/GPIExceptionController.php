<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GPIExceptionController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('GPI_EXCEPTIONS')
                ->select('GPI_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('GPI_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('GPI_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('GPI_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('GPI_EXCEPTION_LISTS')
                    ->select('GPI_EXCEPTION_LISTS.*', 'GPI_EXCEPTIONS.GPI_EXCEPTION_LIST as exception_list', 'GPI_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->join('GPI_EXCEPTIONS', 'GPI_EXCEPTIONS.GPI_EXCEPTION_LIST', '=', 'GPI_EXCEPTION_LISTS.GPI_EXCEPTION_LIST')
                    ->where('GPI_EXCEPTION_LISTS.generic_product_id', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
