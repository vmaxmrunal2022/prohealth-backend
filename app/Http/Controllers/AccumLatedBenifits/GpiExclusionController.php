<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
 
class GpiExclusionController extends Controller
{



    public function add( Request $request ) {


        $recordcheck = DB::table('GPI_EXCLUSION_LISTS')
        ->where('generic_product_id', strtoupper($request->generic_product_id))
        ->first();


        if ( $request->has( 'new' ) ) {

            if($recordcheck){
                return $this->respondWithToken($this->token(), 'GPI Exclusion List ID already exists in the system..!!!', $recordcheck,208);

            }

            else{

                $accum_benfit_stat = DB::table('GPI_EXCLUSION_LISTS' )->insert(
                    [
                        
                        'generic_product_id' => $request->generic_product_id,
                        'gpi_exclusion_list'=>$request->gpi_exclusion_list,
    
                
                    ]
                );
    
                $insert = DB::table('GPI_EXCLUSIONS' )->insert(
                    [
                        
                        'gpi_exclusion_list'=>$request->gpi_exclusion_list,
                        'exclusion_name'=>$request->exclusion_name,

    
                    ]
                );

            }

            if ($insert) {
                return $this->respondWithToken($this->token(), 'Recored Added Successfully', $insert);
            }

        

        }else{

            $createddate = DB::table('GPI_EXCLUSION_LISTS')
            ->where('gpi_exclusion_list', $request->gpi_exclusion_list )
            ->update(
                [
                    'generic_product_id' => $request->generic_product_id,
                   
                ]
            );


            $update = DB::table('GPI_EXCLUSIONS')
            ->where('gpi_exclusion_list', $request->gpi_exclusion_list )
            ->update(
                [
                    'exclusion_name' => $request->exclusion_name,
                   
                ]
            );

    

        }

        if ($update) {
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
        }

    

    }




    public function search(Request $request)

    {
        $ndc =DB::table('GPI_EXCLUSION_LISTS')
        ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
                ->where('GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', 'like', '%' .$request->search. '%')
                ->orWhere('GPI_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' .$request->search. '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($ndcid)
    {
        $ndc =DB::table('GPI_EXCLUSION_LISTS')
        ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
                ->where('GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', 'like', '%' .$ndcid. '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
        ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
        ->join('DRUG_MASTER', 'GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', '=', 'DRUG_MASTER.GENERIC_PRODUCT_ID')
        ->where('GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', 'like', '%' .$ndcid. '%')
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
    
}
 