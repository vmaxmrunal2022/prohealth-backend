<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class ChainController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('PHARMACY_CHAIN')
                ->where('PHARMACY_CHAIN', 'like', '%' .strtoupper($request->search). '%')
                ->orWhere('PHARMACY_CHAIN_NAME', 'like', '%' .strtoupper($request->search). '%')
                ->get();

         return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getList ($ndcid)
    {
        $ndc =DB::table('PHARMACY_CHAIN')
        ->Where('PHARMACY_CHAIN',$ndcid)
        ->first();
        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function dropdowns(){

        $ndc =DB::table('PHARMACY_CHAIN')
        ->select('PHARMACY_CHAIN','PHARMACY_CHAIN_NAME')
        ->get();
        return $this->respondWithToken($this->token(), '', $ndc);

    }
    public function dropdownsNew(){

        $ndc =DB::table('PHARMACY_CHAIN')
        ->select('PHARMACY_CHAIN','PHARMACY_CHAIN_NAME')
        ->paginate(100);
        return $this->respondWithToken($this->token(), '', $ndc);

    }
    

    public function add(Request $request)
    {
        if ($request->new==1) {


            $recordcheck = DB::table('PHARMACY_CHAIN')
                ->where('PHARMACY_CHAIN', strtoupper($request->pharmacy_chain))
            
                ->first();

                if($recordcheck){

                    return $this->respondWithToken($this->token(), 'Pharmacy Chain Id Already Exists', $recordcheck);


                }

                else{

                    $procedurecode = DB::table('PHARMACY_CHAIN')->insert(
                        [
                            'PHARMACY_CHAIN' => strtoupper($request->pharmacy_chain),
                            'PHARMACY_CHAIN_NAME' => strtoupper($request->pharmacy_chain_name),
                            'MAILING_NAME'=>$request->mailing_name,
                            'MAILING_ADDRESS_1'=>$request->mailing_address_1,
                            'MAILING_ADDRESS_2'=>$request->mailing_address_2,
                            'MAILING_CITY'=>$request->mailing_city,
                            'MAILING_STATE'=>$request->mailing_state,
                            'MAILING_ZIP_CODE'=>$request->mailing_zip_code,
                            'MAILING_COUNTRY_CODE'=>$request->mailing_country_code,
                            'BILLING_NAME'=>$request->billing_name,
                            'BILLING_ADDRESS_1'=>$request->billing_address_1,
                            'BILLING_ADDRESS_2'=>$request->billing_address_2,
                            'BILLING_CITY'=>$request->billing_city,
                            'BILLING_STATE'=>$request->billing_state,
                            'BILLING_ZIP_CODE'=>$request->billing_zip_code,
                            'BILLING_COUNTRY_CODE'=>$request->billing_country_code,
                            'CONTACT'=>$request->contact,
                            'PHONE'=>$request->phone,
                            'FAX'=>$request->fax,
                            'EMAIL'=>$request->email,
                            'NOTES'=>$request->notes,
                            'TAX_ID'=>$request->tax_id,
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID_CREATED' => '',
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'FORM_ID' => '',
                            // 'COMPLETE_CODE_IND' => ''
                        ]
                    );
                 return  $this->respondWithToken($this->token(), 'Record Added Successfully', $procedurecode);
                

                }
           
                
        } else {
           

                $procedurecode = DB::table('PHARMACY_CHAIN')
                    ->where(DB::raw('UPPER(PHARMACY_CHAIN)'), strtoupper($request->pharmacy_chain))
                    ->update(
                        [
                        'PHARMACY_CHAIN_NAME' => strtoupper($request->pharmacy_chain_name),
                        'MAILING_NAME'=>$request->mailing_name,
                        'MAILING_ADDRESS_1'=>$request->mailing_address_1,
                        'MAILING_ADDRESS_2'=>$request->mailing_address_2,
                        'MAILING_CITY'=>$request->mailing_city,
                        'MAILING_STATE'=>$request->mailing_state,
                        'MAILING_ZIP_CODE'=>$request->mailing_zip_code,
                        'MAILING_COUNTRY_CODE'=>$request->mailing_country_code,
                        'BILLING_NAME'=>$request->billing_name,
                        'BILLING_ADDRESS_1'=>$request->billing_address_1,
                        'BILLING_ADDRESS_2'=>$request->billing_address_2,
                        'BILLING_CITY'=>$request->billing_city,
                        'BILLING_STATE'=>$request->billing_state,
                        'BILLING_ZIP_CODE'=>$request->billing_zip_code,
                        'BILLING_COUNTRY_CODE'=>$request->billing_country_code,
                        'CONTACT'=>$request->contact,
                        'PHONE'=>$request->phone,
                        'FAX'=>$request->fax,
                        'EMAIL'=>$request->email,
                        'NOTES'=>$request->notes,
                        'TAX_ID'=>$request->tax_id,
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'FORM_ID' => '',
                        ]
                    );

                    
                return  $this->respondWithToken($this->token(), 'Record Updated Successfully', $procedurecode);
            
        }

        

    }

    public function chainDelete(Request $request){
        if(isset($request->pharmacy_chain)) {
            $chainDelete = DB::table('PHARMACY_CHAIN')
                              ->where(DB::raw('UPPER(PHARMACY_CHAIN)'), strtoupper($request->pharmacy_chain))->delete() ;

            if ($chainDelete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}
