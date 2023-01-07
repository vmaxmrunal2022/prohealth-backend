<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientGroupController extends Controller
{


    
    public function add( Request $request ) {
        $createddate = date('y-m-d' );

        if ( $request->has( 'new' ) ) {

            $accum_benfit_stat_names = DB::table('CLIENT_GROUP')->insert(
                [   

                    'customer_id' =>$request->customer_id,
                    'client_id'=>$request->client_id,
                    'client_group_id'=>$request->client_group_id,
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
                    'user_id'=>$request->user_id,
                    'census_date'=>$request->census_date,
                    'group_name'=>$request->group_name,
                    'misc_data_1'=>$request->misc_data_1,
                    'misc_data_2'=>$request->misc_data_2,
                    'misc_data_3'=>$request->misc_data_3,
                    'prescriber_exceptions_flag'=>$request->prescriber_exceptions_flag,
                    'prescriber_exceptions_flag_2'=>$request->prescriber_exceptions_flag_2,
                    // 'marketing_rep_id'=>$request->marketing_rep_id,

                
                ]
            );

            $benefitcode = DB::table('CLIENT_GROUP' ) ->where('client_group_id', 'like', '%' . $request->client_group_id. '%')->first();


        } else {


            $accum_benfit_stat = DB::table('CLIENT_GROUP' )
            ->where( 'CLIENT_GROUP_ID', $request->client_group_id)
            ->update(
                [
                    'customer_id' =>$request->customer_id,
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
                    'user_id'=>$request->user_id,
                    'census_date'=>$request->census_date,
                    'group_name'=>$request->group_name,
                    'misc_data_1'=>$request->misc_data_1,
                    'misc_data_2'=>$request->misc_data_2,
                    'misc_data_3'=>$request->misc_data_3,
                    'prescriber_exceptions_flag'=>$request->prescriber_exceptions_flag,
                    'prescriber_exceptions_flag_2'=>$request->prescriber_exceptions_flag_2,
                    'marketing_rep_id'=>$request->marketing_rep_id,

                

                ]
            );


            $benefitcode = DB::table('CLIENT_GROUP' ) ->where('client_group_id', 'like', '%' . $request->client_group_id. '%')->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added', $benefitcode );
    }
    public function getClientGroup(Request $request)
    {
        $customerid = $request->customerid;
        $clientid = $request->clientid;
        $groupid = $request->groupid;

        $clientgroup = DB::table('client_group')
                        ->when($customerid, function($q) use ($customerid) {
                            $q->where('CUSTOMER_ID', $customerid);
                        })
                        ->when($clientid, function($q) use ($clientid) {
                            $q->where('CLIENT_ID', $clientid);
                        })
                        ->when($groupid, function($q) use ($groupid) {
                            $q->where('CLIENT_GROUP_ID', $groupid);
                        })
                        ->get();

        $this->respondWithToken($this->token() ?? '', '', $clientgroup);
    }

    
    public function GetOneClientGroup($clientgrpid)
    {
        $client = DB::table('CLIENT_GROUP')
            // ->select('CUSTOMER_ID', 'CUSTOMER_NAME')
            ->where('CLIENT_GROUP_ID', 'like', '%' . strtoupper($clientgrpid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $client);
    }

    public function searchClientgroup(Request $request)
    {

        $search = $request->search;
     
        $client = DB::table('CLIENT_GROUP')
            // ->join('customer', 'client.CUSTOMER_ID', '=', 'customer.CUSTOMER_ID')
            ->select('CLIENT_ID','GROUP_NAME', 'CUSTOMER_ID', 'CLIENT_GROUP_ID')
            ->where('CUSTOMER_ID', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('CLIENT_ID', 'like', '%' . strtoupper($request->search) . '%')  
            ->orWhere('CLIENT_GROUP_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GROUP_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $client);
    }
}
