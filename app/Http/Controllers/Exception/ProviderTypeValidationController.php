<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderTypeValidationController extends Controller
{

    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {



            $accum_benfit_stat_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')->insert(
                [
                    'prov_type_list_id' => strtoupper( $request->benefit_list_id ),
                    'description'=>$request->description
                    

                ]
            );


            $accum_benfit_stat = DB::table('PROVIDER_TYPE_VALIDATIONS' )->insert(
                [
                    'prov_type_list_id'=>$request->prov_type_list_id,
                    'provider_type'=>$request->provider_type,

                ]
            );

            $benefitcode = DB::table('PROVIDER_TYPE_VALIDATIONS')->where('prov_type_list_id', 'like', '%'.$request->prov_type_list_id .'%')->first();

        } else {


            $benefitcode = DB::table('PROVIDER_TYPE_VALIDATION_NAMES' )
            ->where('prov_type_list_id', $request->prov_type_list_id )


            ->update(
                [
                    'description'=>$request->description,

                ]
            );

            $accum_benfit_stat = DB::table('PROVIDER_TYPE_VALIDATIONS' )
            ->where('proc_code_list_id', $request->proc_code_list_id )
            ->where('provider_type',$request->provider_type)
            ->update(
                [
                    'provider_type'=>$request->provider_type,
                    'effective_date'=>$request->effective_date,
                  

                ]
            );

            $benefitcode = DB::table('PROVIDER_TYPE_VALIDATIONS')->where('proc_code_list_id', 'like', $request->proc_code_list_id )->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
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


