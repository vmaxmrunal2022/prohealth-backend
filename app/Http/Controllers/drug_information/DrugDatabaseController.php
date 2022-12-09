<?php

namespace App\Http\Controllers\drug_information;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugDatabaseController extends Controller
{
    public function get(Request $request)
    {
        $data = DB::table('DRUG_MASTER')
            ->where('NDC', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('LABEL_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GENERIC_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getDrugPrices(Request $request)
    {
        $drugPrices = DB::table('drug_price')
            ->where('ndc', 'like','%' . $request->search.'%')
            ->get();
        return $this->respondWithToken($this->token(), '', $drugPrices);
    }
}
