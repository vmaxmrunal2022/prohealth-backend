<?php

namespace App\Http\Controllers\drug_information;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NdcGpiController extends Controller
{
    use AuditTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->ndc) {

            $data = DB::table('DRUG_MASTER')
                ->where('NDC', $request->ndc)
                ->get();
        }

        if ($request->gpi) {

            $data = DB::table('DRUG_MASTER')
                ->Where('GENERIC_PRODUCT_ID', $request->gpi)
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

        $ndc = DB::table('DRUG_MASTER')
            ->where('NDC', $ndcid)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function GpiDropDown(Request $request)
    {
        $data = DB::table('DRUG_MASTER')
            ->select('NDC', 'GENERIC_PRODUCT_ID', 'LABEL_NAME')
            ->whereRaw('LOWER(NDC) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orWhereRaw('LOWER(GENERIC_PRODUCT_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])

            ->paginate(100);
        return $this->respondWithToken($this->token(), '', $data);
    }
}
