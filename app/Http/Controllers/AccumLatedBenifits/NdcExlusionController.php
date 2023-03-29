<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class NdcExlusionController extends Controller
{


    public function add( Request $request ) {

        $createddate = date('y-m-d' );



        $recordcheck = DB::table('NDC_EXCLUSION_LISTS')
        ->where('ndc_exclusion_list', strtoupper($request->ndc_exclusion_list))
        ->first();


        if ( $request->has( 'new' ) ) {

            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Ndc Exclusion List Id already exists in the system..!!!', $recordcheck);

            }

            else{

                $accum_benfit_stat = DB::table('NDC_EXCLUSION_LISTS' )->insert(
                    [
                        
                        'ndc' => $request->ndc,
                        'ndc_exclusion_list'=>$request->ndc_exclusion_list,
                        'DATE_TIME_CREATED'=>$createddate,
    

    
                    ]
                );
    
                $insert = DB::table('NDC_EXCLUSIONS' )->insert(
                    [
                        
                        'ndc_exclusion_list'=>$request->ndc_exclusion_list,
                        'exclusion_name'=>$request->exclusion_name,
                        'DATE_TIME_CREATED'=>$createddate,

    
                    ]
                );

                    if ($insert) {
                    return $this->respondWithToken($this->token(), 'Recored Added Successfully', $insert);
                }
    
    

            }


          


        }else{

            $createddate = DB::table('NDC_EXCLUSION_LISTS')
            ->where('ndc_exclusion_list', $request->ndc_exclusion_list )
            ->update(
                [
                    'ndc' => $request->ndc,
                   
                ]
            );


            $update = DB::table('NDC_EXCLUSIONS')
            ->where('ndc_exclusion_list', $request->ndc_exclusion_list )
            ->update(
                [
                    'exclusion_name' => $request->exclusion_name,
                   
                ]
            );

        
            if ($update) {
                            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                        }
        }

       


    }

    public function AllNdcGpisExcusions(Request $request){

        $data = DB::table('NDC_EXCLUSION_LISTS')
        ->get();

        if($data){
            return $this->respondWithToken($this->token(), 'Data fetched succefully', $data);

        }
        else{
            return $this->respondWithToken($this->token(), 'Something went wrong', $data);

        }


    }




    public function search(Request $request)

    {
        $ndc =DB::table('NDC_EXCLUSION_LISTS')
        ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
                ->where('NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', 'like', '%' .$request->search. '%')
                ->orWhere('NDC_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' .$request->search. '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($ndcid)
    {
        $ndc =DB::table('NDC_EXCLUSION_LISTS')
        ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
                ->where('NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', $ndcid)
                ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('NDC_EXCLUSION_LISTS')
        ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
        ->join('DRUG_MASTER', 'NDC_EXCLUSION_LISTS.NDC', '=', 'DRUG_MASTER.NDC')
        ->where('NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST',$ndcid)
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
