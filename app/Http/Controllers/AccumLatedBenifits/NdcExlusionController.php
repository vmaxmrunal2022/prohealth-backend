<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NdcExlusionController extends Controller
{


    public function add(Request $request)
    {


        if ($request->has('new')) {


            $accum_benfit_stat = DB::table('NDC_EXCLUSION_LISTS')->insert(
                [

                    'ndc' => $request->ndc_exclusion_list,
                    'ndc_exclusion_list' => $request->ndc_exclusion_list,




                ]
            );

            $insert = DB::table('NDC_EXCLUSIONS')->insert(
                [

                    'ndc_exclusion_list' => $request->ndc_exclusion_list,
                    'exclusion_name' => $request->exclusion_name,




                ]
            );

            $benefitcode = DB::table('NDC_EXCLUSION_LISTS')->where('ndc_exclusion_list', 'like', '%' . $request->ndc_exclusion_list . '%')->first();
        } else {

            $createddate = DB::table('NDC_EXCLUSION_LISTS')
                ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                ->update(
                    [
                        'ndc' => $request->ndc,

                    ]
                );


            $update = DB::table('NDC_EXCLUSIONS')
                ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                ->update(
                    [
                        'exclusion_name' => $request->exclusion_name,

                    ]
                );



            $benefitcode = DB::table('NDC_EXCLUSION_LISTS')->where('ndc_exclusion_list', 'like', '%' . $request->ndc_exclusion_list . '%')->first();
        }






        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }




    public function search(Request $request)
    {
        $ndc = DB::table('NDC_EXCLUSION_LISTS')
            ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
            ->whereRaw('LOWER(NDC_EXCLUSIONS.NDC_EXCLUSION_LIST) LIKE ?', ['%' . $request->search . '%'])
            // ->orWhere('NDC_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' .$request->search. '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($ndcid)
    {
        $ndc = DB::table('NDC_EXCLUSION_LISTS')
            ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
            ->where('NDC_EXCLUSION_LISTS.NDC', 'like', '%' . $ndcid . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('NDC_EXCLUSION_LISTS')
            ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
            ->where('NDC_EXCLUSION_LISTS.NDC', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
