<?php

namespace App\Http\Controllers\PrescriberData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriberController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('PHYSICIAN_TABLE')
                ->where('PHYSICIAN_ID', 'like', '%' .$request->search. '%')
                ->orWhere('PHYSICIAN_FIRST_NAME', 'like', '%' .$request->search. '%')
                ->orWhere('PHYSICIAN_LAST_NAME', 'like', '%' . $request->search. '%')

                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($ndcid)
    {
        $ndc = DB::table('PHYSICIAN_TABLE')
                ->where('PHYSICIAN_ID', 'like', '%' .$ndcid. '%')
                ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function add(Request $request)
    {

        $check = DB::table('PHYSICIAN_TABLE')
        ->where('physician_id',strtoupper($request->physician_id))
        ->first();

        if($request->new){

            if($check){

                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $check);


            }
            else{


                $insert = DB::table('PHYSICIAN_TABLE')
                  ->insert([
                    'physician_id' => $request->physician_id,
                    'physician_last_name' => $request->physician_last_name,
                    'physician_first_name' => $request->physician_first_name,
                    'address_1' => $request->address_1,
                    'city' => $request->city,
                    'country' => $request->country,
                    'license_number' => $request->license_number,
                    'medical_group' => $request->medical_group,
                    'phone' => $request->phone,
                    'physician_dea' => $request->physician_dea,
                    'physician_specialty' => $request->physician_specialty,
                    'physician_title' => $request->physician_title,
                    'spin_number' => $request->spin_number,
                    'state' => $request->state,
                    'user_id' => $request->user_id,
                    'zip_code' => $request->zip_code,
                  ]);


                  if($insert)
                  {
                      $getUpdated = DB::table('PHYSICIAN_TABLE')->where('physician_id', $request->physician_id)->first();
                      return $this->respondWithToken($this->token(), 'Record Added Successfully', $getUpdated);
                  }

            }

            

        }
        
       

        else{

            $update = DB::table('PHYSICIAN_TABLE')
            ->where('PHYSICIAN_ID', $request->physician_id)
            ->update([
              'physician_id' => $request->physician_id,
              'physician_last_name' => $request->physician_last_name,
              'physician_first_name' => $request->physician_first_name,
              'address_1' => $request->address_1,
              'city' => $request->city,
              'country' => $request->country,
              'license_number' => $request->license_number,
              'medical_group' => $request->medical_group,
              'phone' => $request->phone,
              'physician_dea' => $request->physician_dea,
              'physician_specialty' => $request->physician_specialty,
              'physician_title' => $request->physician_title,
              'spin_number' => $request->spin_number,
              'state' => $request->state,
              'user_id' => $request->user_id,
              'zip_code' => $request->zip_code,
            ]);

            if($update)
            {
                $getUpdated = DB::table('PHYSICIAN_TABLE')->where('physician_id', $request->physician_id)->first();
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $getUpdated);
            }
            
        }

       
    }
}



