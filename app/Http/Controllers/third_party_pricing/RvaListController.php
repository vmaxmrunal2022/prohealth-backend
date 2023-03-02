<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RvaListController extends Controller
{
    public function get(Request $request)
    {
        $rvaNames = DB::table('rva_names')->get();

        return $this->respondWithToken($this->token(), '', $rvaNames);
    }

    public function getRvaList(Request $request)
    {
        $rvaLists = DB::table('rva_names')
                    ->join('rva_list', 'rva_names.rva_list_id', 'rva_list.rva_list_id')
                    ->where('rva_list.rva_list_id', $request->search)
                    ->get();

       return $this->respondWithToken($this->token(), '', $rvaLists);
    }


    public function submitRva(Request $request)
    {
        $effective_date = date('Ymd', strtotime($request->effective_date));
        $termination_date = date('Ymd', strtotime($request->termination_date));

        $check_record= DB::table('RVA_LIST')->where('RVA_LIST_ID',$request->rva_list_id)->first();
        if($check_record){
            $updatecode = DB::table( 'RVA_LIST' )
            ->where( 'RVA_LIST_ID', $request->rva_list_id )
            ->update(
                [
                    'EFFECTIVE_DATE' => $effective_date,
                    'TERMINATION_DATE' => $termination_date,
                    'RVA_VALUE' => $request->rva_value,
                    'USER_ID' => $request->user_id,
                    'FORM_ID' => $request->form_id,
                ]
            );
            $update1 = DB::table('RVA_NAMES')->where('RVA_LIST_ID',$request->rva_list_id)
                    ->update([
                        'RVA_LIST_ID' =>strtoupper($request->rva_list_id),
                        'DESCRIPTION' => $request->description,
                        'USER_ID' => $request->user_id,
                        'FORM_ID' => $request->form_id,
            ]);

           return $this->respondWithToken($this->token(), 'updated Successfully!', $updatecode, $update1);
        }else{
            if ($request->add_new) {
                $add = DB::table('RVA_LIST')
                    ->insert([
                        'RVA_LIST_ID' =>strtoupper($request->rva_list_id),
                        'EFFECTIVE_DATE' => $effective_date,
                        'TERMINATION_DATE' => $termination_date,
                        'RVA_VALUE' => $request->rva_value,
                        'USER_ID' => $request->user_id,
                        'FORM_ID' => $request->form_id,
                    ]);
    
                $add1 = DB::table('RVA_NAMES')
                    ->insert([
                        'RVA_LIST_ID' => strtoupper($request->rva_list_id),
                        'DESCRIPTION' => $request->description,
                        'USER_ID' => $request->user_id,
                        'FORM_ID' => $request->form_id,
                    ]);
    
                // $add = DB::table('mac_table')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Added Successfully!', $add, $add1);

        }
        }
    }


    
}
