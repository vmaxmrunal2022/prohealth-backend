<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProviderTypeValidationController extends Controller
{

    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );
        if ( $request->has( 'new' ) ) {

        
        $recordcheck = DB::table('PROVIDER_TYPE_VALIDATIONS')
        ->where('prov_type_list_id', strtoupper($request->prov_type_list_id))
        ->first();
        

        $createddate = date('y-m-d');

           
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
                        'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
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
                            'description' => $request->description,

                        ]
                    );

            $update = DB::table('PROVIDER_TYPE_VALIDATIONS' )
            ->where('proc_code_list_id', $request->proc_code_list_id )
            ->where('provider_type',$request->provider_type)
            ->update(
                [
                        'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                        'PROVIDER_TYPE'=>$request->provider_type,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                        'DATE_TIME_CREATED'=>$createddate,
                  

                        ]
                    );

            return $this->respondWithToken( $this->token(), 'Record Updated Successfully',$update);

        }


}


    public function getAllNames(Request $request)
    {

        $data = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
            ->where('PROV_TYPE_PROC_ASSOC_ID', 'LIKE', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), 'data fetched  successfully', $data);
    }

    public function get(Request $request)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
            // $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS')
            // ->where('effective_date', 'like', '%'.$request->search.'%')
            ->Where(DB::raw('UPPER(prov_type_list_id)'), 'like', '%' . strtoupper($request->search) . '%')
            // ->orWhere(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        // dd($request->all());
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }

    public function getList($ncdid)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS')
        ->join('PROVIDER_TYPE_VALIDATION_NAMES', 'PROVIDER_TYPE_VALIDATION_NAMES.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
        ->Where('PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID',$ncdid)
        ->get();
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }




    public function getNDCItemDetails($ndcid, $ndcid2)
    {
        $ndc = DB::table('PROVIDER_TYPE_VALIDATIONS')
        ->join('PROVIDER_TYPE_VALIDATION_NAMES as valdation_names', 'valdation_names.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
        ->join('PROC_CODE_LIST_NAMES as list_names','list_names.PROC_CODE_LIST_ID','=','PROVIDER_TYPE_VALIDATIONS.PROC_CODE_LIST_ID')
        ->join('PROVIDER_TYPES as types','types.PROVIDER_TYPE','=','PROVIDER_TYPE_VALIDATIONS.PROVIDER_TYPE')
        ->select('PROVIDER_TYPE_VALIDATIONS.PROC_CODE_LIST_ID',
        'PROVIDER_TYPE_VALIDATIONS.prov_type_list_id',
        'PROVIDER_TYPE_VALIDATIONS.provider_type',
        'PROVIDER_TYPE_VALIDATIONS.date_time_created',
        'PROVIDER_TYPE_VALIDATIONS.date_time_modified',
        'PROVIDER_TYPE_VALIDATIONS.effective_date',
        'PROVIDER_TYPE_VALIDATIONS.form_id',
        'PROVIDER_TYPE_VALIDATIONS.termination_date',
        'PROVIDER_TYPE_VALIDATIONS.user_id',
        'PROVIDER_TYPE_VALIDATIONS.user_id_created',
        'valdation_names.DESCRIPTION as description',
        'list_names.DESCRIPTION as Procedure_code_description',
        'types.DESCRIPTION as provider_type_description')
        ->where('PROVIDER_TYPE_VALIDATIONS.proc_code_list_id',$ndcid)
        ->where('PROVIDER_TYPE_VALIDATIONS.provider_type',$ndcid2)


            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
