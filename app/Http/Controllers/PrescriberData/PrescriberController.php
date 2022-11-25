<?php

namespace App\Http\Controllers\PrescriberData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PrescriberController extends Controller
{
    public function search(Request $request)

    {
        $ndc = DB::table('PHYSICIAN_TABLE')
                ->where('PHYSICIAN_ID', 'like', '%' .$request->search. '%')
                ->orWhere('PHYSICIAN_FIRST_NAME', 'like', '%' .$request->search. '%')
                ->orWhere('PHYSICIAN_LAST_NAME', 'like', '%' . $request->search. '%')

                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('PHYSICIAN_TABLE')
                ->where('PHYSICIAN_ID', 'like', '%' .$ndcid. '%')
                ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}



