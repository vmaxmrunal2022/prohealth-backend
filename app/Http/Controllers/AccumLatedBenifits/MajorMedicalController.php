<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class MajorMedicalController extends Controller
{
    


    public function search(Request $request)

    {
                $ndc = DB::table('CUSTOMER')
                ->where('CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('CUSTOMER_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getClient($ndcid)
    {
        $ndc =DB::table('CLIENT')
                ->where('CUSTOMER_ID', 'like', '%' .$ndcid. '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function getClientGroup($ndcid)
    {

        $ndc =DB::table('CLIENT_GROUP')
                ->where('CLIENT_ID', 'like', '%' .$ndcid. '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    



}
