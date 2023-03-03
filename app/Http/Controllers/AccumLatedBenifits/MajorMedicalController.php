<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class MajorMedicalController extends Controller
{



    public function add( Request $request ) {


        $recordcheck=DB::table('MM_LIFE_MAX')
        ->where('customer_id', strtoupper($request->customer_id))
        ->where('client_id',strtoupper($request->client_id))
        ->where('client_group_id',strtoupper($request->client_group_id))
        ->first();


        // dd($request->all());

        if ( $request->new == 1 ) {

            if($recordcheck){

                return $this->respondWithToken($this->token(), 'Record already exists in the system..!!!', $recordcheck,false);

            }

            else{

                $insert = DB::table('MM_LIFE_MAX')->insert(
                    [
                        'customer_id'=>$request->customer_id,
                        'client_id'=>$request->client_id,
                        'client_group_id'=>$request->client_group_id,
                        'mm_life_maximum' => $request->mm_life_maximum,
                        'grouping_type'=>$request->grouping_type,
                        'mm_claim_max'=>$request->mm_claim_max,
                        'effective_date'=>$request->effective_date,
                        'termination_date'=>$request->termination_date,
                        'mm_claim_max_group_type'=>strtoupper($request->mm_claim_max_group_type),
                      
                       
                    ]
                );
    
                // $benefitcode = DB::table('TEMP_MM_LIFE_MAX')->where('customer_id',$request->customer_id)->first();
    
            return $this->respondWithToken( $this->token(), 'Record added Successfully',$insert);

            }
    
           
        }else{

           

            $update = DB::table('MM_LIFE_MAX')
            ->where('customer_id', strtoupper($request->customer_id ))
            ->where('client_id',strtoupper($request->client_id))
            ->where('client_group_id',strtoupper($request->client_group_id))
            ->update(
                [
                    
                    'mm_life_maximum' => $request->mm_life_maximum,
                    'grouping_type'=>$request->grouping_type,
                    'mm_claim_max'=>$request->mm_claim_max,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,
                    'mm_claim_max_group_type'=>strtoupper($request->mm_claim_max_group_type),

                   
                ]
            );


            $benefitcode=DB::table('MM_LIFE_MAX')
            ->where('customer_id', strtoupper($request->customer_id))
            ->where('client_id',strtoupper($request->client_id))
            ->where('client_group_id',strtoupper($request->client_group_id))
            ->first();
        
        // $benefitcode = DB::table('MM_LIFE_MAX')->where('mm_life_maximum', 'like', '%'.$request->mm_life_maximum .'%')->first();
        return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$recordcheck);


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



    public function getDetails($ndcid)
    {

        $ndc =DB::table('MM_LIFE_MAX')
        ->select('MM_LIFE_MAX.CUSTOMER_ID','MM_LIFE_MAX.CLIENT_ID','MM_LIFE_MAX.CLIENT_GROUP_ID','MM_LIFE_MAX.EFFECTIVE_DATE','MM_LIFE_MAX.TERMINATION_DATE','MM_LIFE_MAX.MM_LIFE_MAXIMUM','MM_LIFE_MAX.MM_CLAIM_MAX','MM_LIFE_MAX.GROUPING_TYPE','MM_LIFE_MAX.MM_CLAIM_MAX_GROUP_TYPE','MM_LIFE_MAX.MM_CLAIM_MAX_PERIOD','CUSTOMER.CUSTOMER_NAME','CLIENT.CLIENT_NAME','CLIENT_GROUP.GROUP_NAME')
        ->join('CUSTOMER', 'CUSTOMER.CUSTOMER_ID', '=', 'MM_LIFE_MAX.CUSTOMER_ID')
        ->join('CLIENT', 'CLIENT.CLIENT_ID', '=', 'MM_LIFE_MAX.CLIENT_ID')
        ->join('CLIENT_GROUP', 'CLIENT_GROUP.CLIENT_GROUP_ID', '=', 'MM_LIFE_MAX.CLIENT_GROUP_ID')
        ->where('MM_LIFE_MAX.client_group_id',$ndcid)
         ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }




    


    



}
