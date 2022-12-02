<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RvaListController extends Controller
{
    public function get(Request $request)
    {
        $rvaNames = DB::table('rva_names')->get();

        return $this->respondWithToken($this->token(), '', $rvaNames);
    }

    public function getRvaList(Request $request)
    {
        $rvaLists = DB::table('rva_names')
                    ->join('rva_list', 'rva_names.rva_list_id', 'rva_list.rva_list_id')
                    ->where('rva_list.rva_list_id', $request->search)
                    ->get();

       return $this->respondWithToken($this->token(), '', $rvaLists);
    }
}
