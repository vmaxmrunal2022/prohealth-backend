<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderDataController extends Controller
{
    use AuditTrait;


    public function add(Request $request)
    {

        // $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', $request->pharmacy_nabp)->first();

        // dd($benefitcode);



        // $createddate = date( 'y-m-d' );



        $createddate = DB::table('PHARMACY_TABLE')
            ->where('pharmacy_nabp', $request->pharmacy_nabp)
            ->update(
                [
                    'pharmacy_nabp' => $request->pharmacy_nabp,
                    'pharmacy_name' => $request->pharmacy_name,
                    // 'address_1'=>$request->address_1,
                    // 'provider_first_name'=>$request->provider_first_name,
                    // 'provider_last_name'=>$request->provider_last_name,
                    // 'pharmacy_class'=>$request->pharmacy_class,
                    // 'address_2'=>$request->address_2,

                    // 'city'=>$request->city,
                    // 'state'=>$request->state,
                    // 'zip_code'=>$request->zip_code,
                    // 'zip_plus_2'=>$request->zip_plus_2,
                    // 'phone'=>$request->phone, 
                    // 'fax'=>$request->fax,
                    // 'mailing_address_1'=>$request->mailing_address_1,
                    // 'mailing_address_2'=>$request->mailing_address_2,
                    // 'mailing_city'=>$request->mailing_city,
                    // 'mailing_state'=>$request->mailing_state,
                    // 'mailing_zip_code'=>$request->mailing_zip_code,
                    // 'mailing_zip_plus_2'=>$request->mailing_zip_plus_2,
                    // 'edi_address'=>$request->edi_address,
                    // 'pharmacy_class'=>$request->pharmacy_class,
                    // 'aba_rtn'=>$request->aba_rtn,
                    // 'store_number'=>$request->store_number,
                    // 'head_office_ind'=>$request->head_office_ind,
                    // 'pharmacy_chain'=>$request->pharmacy_chain,
                    // 'mail_order'=>$request->mail_order,
                    // 'region'=>$request->region,
                    // 'district'=>$request->district,
                    // 'market'=>$request->market,

                    // 'price_zone'=>$request->price_zone,
                    // 'scd_age_threshold'=>$request->scd_age_threshold,
                    // 'market'=>$request->market,
                    // 'market'=>$request->market,


                ]
            );

        // // dd($request->pharmacy_nabp);

        // $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', $request->pharmacy_nabp)->first();

        $benefitcode = DB::table('PHARMACY_TABLE')->where('pharmacy_nabp', 'like', '%' . $request->pharmacy_nabp . '%')->first();


        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }





    public function search(Request $request)
    {

        if($request->search == 'undefined'){
            $ndc = DB::table('PHARMACY_TABLE')
            ->get();
        }else{
            $ndc = DB::table('PHARMACY_TABLE')

            ->where('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PHARMACY_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();


        }
       
        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function networkDetails($ndcid)
    {

        $ndc =  DB::table('PHARMACY_TABLE')

            ->where('PHARMACY_NABP', 'like', '%' . strtoupper($ndcid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
