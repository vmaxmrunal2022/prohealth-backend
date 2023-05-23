<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class MajorMedicalController extends Controller
{



    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "customer_id" => ['required'],
            "client_id" => ['required'],
            "client_group_id" => ['required'],
            'mm_claim_max' => ['max:10'],
            'mm_life_maximum' => ['max:10'],
        ]);

  
        $record_check=DB::table('MM_LIFE_MAX')
        ->where('customer_id', $request->customer_id)
        ->where('client_id', $request->client_id)
        ->where('client_group_id', $request->client_group_id)
        ->where('effective_date',$request->effective_date)
        ->where('termination_date',$request->termination_date)
        ->first();

       
        
            if ($request->new == 1) {


                if ($record_check) {
                    return $this->respondWithToken($this->token(), 'Record Already Exists', "false");
                }else{

                    $insert = DB::table('MM_LIFE_MAX')->insert(
                        [
                            'customer_id' => $request->customer_id,
                            'client_id' => $request->client_id,
                            'client_group_id' => $request->client_group_id,
                            'mm_life_maximum' => $request->mm_life_maximum,
                            'grouping_type' => $request->grouping_type,
                            'mm_claim_max' => $request->mm_claim_max,
                            'effective_date' => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'mm_claim_max_group_type' => strtoupper($request->mm_claim_max_group_type),

                        ]
                    );
                    // $benefitcode = DB::table('TEMP_MM_LIFE_MAX')->where('customer_id',$request->customer_id)->first();    
                    return $this->respondWithToken($this->token(), 'Record added Successfully', $insert);

                }
                    
            } else {

                $update = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', strtoupper($request->customer_id))
                    ->where('client_id', strtoupper($request->client_id))
                    ->where('client_group_id', strtoupper($request->client_group_id))
                    ->update(
                        [
                            'mm_life_maximum' => $request->mm_life_maximum,
                            'grouping_type' => $request->grouping_type,
                            'mm_claim_max' => $request->mm_claim_max,
                            'effective_date' => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'mm_claim_max_group_type' => strtoupper($request->mm_claim_max_group_type),
                        ]
                    );
                $benefitcode = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', strtoupper($request->customer_id))
                    ->where('client_id', strtoupper($request->client_id))
                    ->where('client_group_id', strtoupper($request->client_group_id))
                    ->first();

                // $benefitcode = DB::table('MM_LIFE_MAX')->where('mm_life_maximum', 'like', '%'.$request->mm_life_maximum .'%')->first();
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
            }

        }
            // dd($request->all());

            
        
    

    public function delete(Request $request)
    {
        if (isset($request->customer_id) && isset($request->client_id) && isset($request->client_group_id)) {
            $delete_mm_life_max =  DB::table('MM_LIFE_MAX')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->delete();
            if ($delete_mm_life_max) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found');
        }
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
        $ndc = DB::table('MM_LIFE_MAX')
            ->select('MM_LIFE_MAX.CUSTOMER_ID', 'MM_LIFE_MAX.CLIENT_ID', 'MM_LIFE_MAX.CLIENT_GROUP_ID', 'MM_LIFE_MAX.EFFECTIVE_DATE', 'MM_LIFE_MAX.TERMINATION_DATE', 'MM_LIFE_MAX.MM_LIFE_MAXIMUM', 'MM_LIFE_MAX.MM_CLAIM_MAX', 'MM_LIFE_MAX.GROUPING_TYPE', 'MM_LIFE_MAX.MM_CLAIM_MAX_GROUP_TYPE', 'MM_LIFE_MAX.MM_CLAIM_MAX_PERIOD', 'CUSTOMER.CUSTOMER_NAME', 'CLIENT.CLIENT_NAME', 'CLIENT_GROUP.GROUP_NAME')
            ->join('CUSTOMER', 'CUSTOMER.CUSTOMER_ID', '=', 'MM_LIFE_MAX.CUSTOMER_ID')
            ->join('CLIENT', 'CLIENT.CLIENT_ID', '=', 'MM_LIFE_MAX.CLIENT_ID')
            ->join('CLIENT_GROUP', 'CLIENT_GROUP.CLIENT_GROUP_ID', '=', 'MM_LIFE_MAX.CLIENT_GROUP_ID')
            ->where('MM_LIFE_MAX.client_group_id', $ndcid)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
