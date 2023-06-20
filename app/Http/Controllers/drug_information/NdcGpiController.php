<?php

namespace App\Http\Controllers\drug_information;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class NdcGpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if($request->ndc){

            $data = DB::table('DRUG_MASTER')
            ->where('NDC',$request->ndc)
            ->get();

        }

        if($request->gpi){

            $data = DB::table('DRUG_MASTER')
           ->Where('GENERIC_PRODUCT_ID',$request->gpi)
            ->get();

        }

        return $this->respondWithToken($this->token(), '', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDetails($ndcid)
    {

        $ndc =DB::table('DRUG_MASTER')
                ->where('NDC', $ndcid)
                ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function GpiDropDown(Request $request){
        $data = DB::table('DRUG_MASTER')
        ->select('NDC','GENERIC_PRODUCT_ID','LABEL_NAME')
        ->get();
        return $this->respondWithToken($this->token(),'',$data);
    }
    
    
  
}
