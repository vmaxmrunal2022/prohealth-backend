<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{



    public function add( Request $request ) {
        $createddate = date('y-m-d' );

        if ( $request->has( 'new' ) ) {

            $accum_benfit_stat_names = DB::table('CLIENT')->insert(
                [   

                'client_name' =>$request->client_name,
                'customer_id'=>$request->customer_id,
                'client_id'=>$request->client_id,
                'address_1'=>$request->address_1,
                'address_2'=>$request->address_2,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip_code'=>$request->zip_code,
                'zip_plus_2'=>$request->zip_plus_2,
                'phone'=>$request->phone,
                'fax'=>$request->fax,
                'contact'=>$request->contact,
                'effective_date'=>$request->effective_date,
                'termination_date'=>$request->termination_date,
                'date_time_created'=>'19-FEB-20',
                'user_id'=>'1',
                'date_time_modified'=>'19-FEB-20',
                'form_id'=>'1',
                // 'auto_term_level'=>$request->auto_term_level,
                // 'census_date'=>$request->census_date,
                ]
            );

            $benefitcode = DB::table('CLIENT' ) ->where('client_id', 'like', '%' . $request->client_id. '%')->first();


        } else {


            $accum_benfit_stat = DB::table('CLIENT' )
            ->where( 'CLIENT_ID', $request->client_id)
            ->update(
                [
                    'client_name' =>$request->client_name,
                    'address_1'=>$request->address_1,
                    'address_2'=>$request->address_2,
                    'city'=>$request->city,
                    'state'=>$request->state,
                    'zip_code'=>$request->zip_code,
                    'zip_plus_2'=>$request->zip_plus_2,
                    'phone'=>$request->phone,
                    'fax'=>$request->fax,
                    'contact'=>$request->contact,
                    'effective_date'=>$request->effective_date,
                    'termination_date'=>$request->termination_date,
                    'user_id'=>$request->user_id,
                    'auto_term_level'=>$request->auto_term_level,
                    'census_date'=>$request->census_date,
                

                ]
            );


            $benefitcode = DB::table('CLIENT' ) ->where('client_id', 'like', '%' . $request->client_id. '%')->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added', $benefitcode );
    }
     

    public function getClient(Request $request)
    {
        // $customerid = $request->customerid;
        // $customername = $request->customername;
        // $clientid = $request->clientid;
        // $clientname = $request->clientname;

        $search = $request->search;


        $clients = DB::table('client')
                        ->when($search, function($q) use ($search) {
                            $q->where('CUSTOMER_ID', $search);
                        })
                        ->when($search, function($q) use ($search) {
                            $q->where('CLIENT_ID', $search);
                        })
                        ->when($search, function($q) use ($search) {
                            $q->where('CLIENT_NAME', $search);
                        })
                        ->get();

        $this->respondWithToken($this->token() ?? '', '', $clients);
        
    }

    public function GetOneClient($clientid)
    {
        $client = DB::table('client')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where('CLIENT_ID', 'like', '%' . strtoupper($clientid) . '%')
            ->orWhere('CLIENT_NAME', 'like', '%' . strtoupper($clientid) . '%')
            ->orWhere('CUSTOMER_ID', 'like', '%' . strtoupper($clientid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $client);
    }

    public function searchClient(Request $request)
    {

        $search = $request->search;
     
        $client = DB::table('client')
            ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
            ->select('CLIENT_ID','CLIENT_NAME', 'customer.CUSTOMER_NAME as customername', 'client.CUSTOMER_ID as customerid','client.EFFECTIVE_DATE as clienteffectivedate','client.TERMINATION_DATE as clientterminationdate')
            ->where('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('CLIENT_NAME', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('customer.CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('customer.CUSTOMER_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $client);
    }
}
