<?php

namespace App\Http\Controllers\ThirdPartyPricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RVAListController extends Controller
{
    public function Search(Request $request)
    {
        $ndc = DB::table('RVA_NAMES')
        ->join('RVA_LIST', 'RVA_NAMES.RVA_LIST_ID', '=', 'RVA_LIST.RVA_LIST_ID')
                ->where('RVA_LIST.RVA_LIST_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('RVA_NAMES.RVA_LIST_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    } 

}
