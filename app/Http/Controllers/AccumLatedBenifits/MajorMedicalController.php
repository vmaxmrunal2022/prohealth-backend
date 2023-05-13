<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MajorMedicalController extends Controller
{



    public function add(Request $request)
    {


        if ($request->has('new')) {



            $insert = DB::table('MM_LIFE_MAX')->insert(
                [
                    'customer_id' => $request->customer_id,
                    'client_id' => $request->client_id,
                    'client_group_id' => $request->client_group_id,
                    'mm_life_maximum' => $request->mm_life_maximum,
                    'grouping_type' => $request->grouping_type,
                    'mm_claim_max' => $request->mm_claim_max,
                    'effective_date' => '19950701',
                    'termination_date' => '19950701'



                ]
            );

            // $benefitcode = DB::table('TEMP_MM_LIFE_MAX')->where('customer_id',$request->customer_id)->first();
            $benefitcode = DB::table('MM_LIFE_MAX')->where('mm_life_maximum', 'like', '%' . $request->mm_life_maximum . '%')->first();
        } else {



            $update = DB::table('MM_LIFE_MAX')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->update(
                    [
                        'mm_life_maximum' => $request->mm_life_maximum,
                        'grouping_type' => $request->grouping_type,
                        'mm_claim_max' => $request->mm_claim_max,

                    ]
                );



            $benefitcode = DB::table('MM_LIFE_MAX')->where('mm_life_maximum', 'like', '%' . $request->mm_life_maximum . '%')->first();
        }






        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }



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
        $ndc = DB::table('CLIENT')
            ->where('CUSTOMER_ID', 'like', '%' . $ndcid . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getClientGroup($ndcid)
    {
        $ndc = DB::table('CLIENT_GROUP')
            ->where(DB::raw('UPPER(CLIENT_GROUP_ID)'), 'like', '%' . strtoupper($ndcid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getDetails($ndcid)
    {

        $ndc = DB::table('MM_LIFE_MAX')
            ->where('client_group_id', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
