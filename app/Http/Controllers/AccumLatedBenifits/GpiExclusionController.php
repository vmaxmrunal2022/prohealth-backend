<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpiExclusionController extends Controller
{
    public function GPIS(Request $request)
    {
        $gpis =  DB::table('GPI_EXCEPTION_LISTS')->get();
        return $this->respondWithToken($this->token(), 'data fetched successfully ', $gpis);
    }


    public function add(Request $request)
    {


        if ($request->has('new')) {


            $accum_benfit_stat = DB::table('GPI_EXCLUSION_LISTS')->insert(
                [

                    'generic_product_id' => $request->generic_product_id,
                    'gpi_exclusion_list' => $request->gpi_exclusion_list,




                ]
            );

            $insert = DB::table('GPI_EXCLUSIONS')->insert(
                [

                    'gpi_exclusion_list' => $request->gpi_exclusion_list,
                    'exclusion_name' => $request->exclusion_name,




                ]
            );

            $benefitcode = DB::table('GPI_EXCLUSION_LISTS')->where('gpi_exclusion_list', 'like', '%' . $request->gpi_exclusion_list . '%')->first();
        } else {

            $createddate = DB::table('GPI_EXCLUSION_LISTS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->update(
                    [
                        'generic_product_id' => $request->generic_product_id,

                    ]
                );


            $update = DB::table('GPI_EXCLUSIONS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->update(
                    [
                        'exclusion_name' => $request->exclusion_name,

                    ]
                );



            $benefitcode = DB::table('GPI_EXCLUSION_LISTS')->where('gpi_exclusion_list', 'like', '%' . $request->gpi_exclusion_list . '%')->first();
        }






        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }





    public function search(Request $request)

    {
        // $ndc = DB::table('GPI_EXCLUSION_LISTS')
        //     ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
        //     ->whereRaw('LOWER(GPI_EXCLUSIONS.GPI_EXCLUSION_LIST) LIKE ?', ['%' . strtolower($request->search) . '%'])
        //     ->orWhere('GPI_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' . $request->search . '%')
        //     ->get();
        $ndc = DB::table('GPI_EXCLUSIONS')
            ->whereRaw('LOWER(GPI_EXCLUSION_LIST) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($ndcid)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
            ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->where('GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', 'like', '%' . $ndcid . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
            ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->where('GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
