<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderTypeValidationController extends Controller
{

    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        
        $recordcheck = DB::table('PROVIDER_TYPE_VALIDATIONS')
        ->where('prov_type_list_id', strtoupper($request->prov_type_list_id))
        ->first();
        

        if ( $request->has( 'new' ) ) {

           
            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Provider Type  ID Already Exists', $recordcheck);

            }

            else{

                $accum_benfit_stat_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')->insert(
                    [
                        'prov_type_list_id' => strtoupper( $request->prov_type_list_id ),
                        'description'=>$request->description,
                        'DATE_TIME_CREATED'=>$createddate,
                        
    
                    ]
                );
    
    
                $add = DB::table('PROVIDER_TYPE_VALIDATIONS')->insert(
                    [
                        'PROC_CODE_LIST_ID'=>$request->procedure_code_list_id,
                        'PROV_TYPE_LIST_ID'=>$request->prov_type_list_id,
                        'PROVIDER_TYPE'=>$request->provider_type,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>$createddate,
    
                    ]
                );


                return $this->respondWithToken( $this->token(), 'Record Added Successfully',$add);


            }

        } else {


            $benefitcode = DB::table('PROVIDER_TYPE_VALIDATION_NAMES' )
            ->where('PROV_TYPE_LIST_ID', $request->prov_type_list_id )


            ->update(
                [
                    'description'=>$request->description,

                ]
            );

            $update = DB::table('PROVIDER_TYPE_VALIDATIONS' )
            ->where('proc_code_list_id', $request->proc_code_list_id )
            ->where('provider_type',$request->provider_type)
            ->update(
                [
                        'PROC_CODE_LIST_ID'=>$request->procedure_code_list_id,
                        'PROVIDER_TYPE'=>$request->provider_type,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>$createddate,
                  

                ]
            );

            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

        }


    }
    

    public function getAllNames(Request $request){

        $data = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
        ->where('PROV_TYPE_PROC_ASSOC_ID','LIKE','%'.strtoupper($request->search).'%')
        ->get();

        return $this->respondWithToken( $this->token(), 'data fetched  successfully',$data);


    }
  
    public function get(Request $request)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')

                            // ->where('effective_date', 'like', '%'.$request->search.'%')
                            ->Where('PROV_TYPE_LIST_ID', 'like', '%'.strtoupper($request->search).'%')
                            ->orWhere('DESCRIPTION', 'like', '%'.strtoupper($request->search).'%')
                            ->get();
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }

    public function getList($ncdid)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS')

        ->Where('PROV_TYPE_LIST_ID', 'like', '%'.strtoupper($ncdid).'%')
        ->get();
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
       
    }




    public function getNDCItemDetails($ndcid,$ndcid2)
    {
        $ndc = DB::table('PROVIDER_TYPE_VALIDATIONS')
        ->join('PROVIDER_TYPE_VALIDATION_NAMES', 'PROVIDER_TYPE_VALIDATION_NAMES.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
        // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
        ->where('proc_code_list_id',$ndcid)
        ->where('provider_type',$ndcid2)


        // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}


