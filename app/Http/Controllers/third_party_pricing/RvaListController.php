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
        $validate = DB::table('rva_names')->where('RVA_LIST_ID', $request->rva_list_id)->get()->count();
// dd($validate);
        if($request->add_new)
        {
            // dd($request->all());
            if($validate <= "0")
            {
                $add_rva_names = DB::table('rva_names')
                ->insert([
                    'rva_list_id' => $request->rva_list_id,
                    'description' => $request->description
                ]);

                if($request->effective_date)
                {
                    $add_rva_list = DB::table('rva_list')
                    ->insert([
                        'rva_list_id' => $request->rva_list_id,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'rva_value' => $request->rva_value,
                    ]);
                }                
                return $this->respondWithToken($this->token(), 'Added Successfully!', $add_rva_names);
           }else{
            return $this->respondWithToken($this->token(),'Something went wrong!', $validate);
           }
         }else{            
                // dd("update");
                $update_rva_name = DB::table('rva_names')
                ->where('rva_list_id', $request->rva_list_id)
                ->update([
                    'description' => $request->description
                ]);

                $update_rva_list = DB::table('rva_list')
                ->where('rva_list_id', $request->rva_list_id)
                ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                ->update([
                    'termination_date' => date('Ymd', strtotime($request->termination_date)),
                    'rva_value' => $request->rva_value
                ]);

                $checkRvaExist = DB::table('rva_list')
                ->where('rva_list_id', $request->rva_list_id)
                ->where('EFFECTIVE_DATE', date('Ymd', strtotime($request->effective_date)))
                ->get()
                ->count();

                if($checkRvaExist <= "0")
                {
                    $add_rva_list = DB::table('rva_list')
                    ->insert([
                        'rva_list_id' => $request->rva_list_id,
                        'effective_date' => date('Ymd', strtotime($request->effective_date)),
                        'termination_date' => date('Ymd', strtotime($request->termination_date)),
                        'rva_value' => $request->rva_value,
                    ]);
                }


                
                return $this->respondWithToken($this->token(), 'Updated Successfully!', $update_rva_name);
            }
    }
}
