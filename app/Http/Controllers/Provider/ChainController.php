<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ChainController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('PHARMACY_CHAIN')
                ->where('PHARMACY_CHAIN', 'like', '%' .strtoupper($request->search). '%')
                ->orWhere('PHARMACY_CHAIN_NAME', 'like', '%' .strtoupper($request->search). '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getList ($ndcid)
    {
        $ndc =DB::table('PHARMACY_CHAIN')
        ->Where('PHARMACY_CHAIN', 'like', '%' .$ndcid. '%')
        ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
