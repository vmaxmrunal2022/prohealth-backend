<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RvaListController extends Controller
{
    public function get(Request $request)
    {
        // return "hi";
        $rvaNames = DB::table('rva_names')
            // ->when($request->search, function ($query) use ($request) {
            //     return $query->where('rva_list_id', 'like', "%$request->search%");
            // })
            ->where(DB::raw('lower(rva_list_id)'), 'like', '%' . strtolower($request->search) . '%')
            // ->where('rva_list_id', 'like', "%$request->search%")
            ->get();

        return $this->respondWithToken($this->token(), '', $rvaNames);
    }

    public function RvaListDropdown(Request $request)
    {
        // return "hi";
        $rvaNames = DB::table('rva_names')
           
            ->get();

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


         if ($request->add_new) {


            if($check_record){

                return $this->respondWithToken($this->token(), 'Record Alredy Exists', $check_record);
    
            }

            else{

            }


            $add = DB::table('RVA_LIST')
                ->insert([
                    'RVA_LIST_ID' =>strtoupper($request->rva_list_id),
                    'EFFECTIVE_DATE' => $effective_date,
                    'TERMINATION_DATE' => $termination_date,
                    'RVA_VALUE' => $request->rva_value,
                    'USER_ID' => $request->user_id,
                    'FORM_ID' => $request->form_id,
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

            // $add = DB::table('mac_table')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
            return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

         }


        else{

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

            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updatecode);


        }



           

       
    }
}
