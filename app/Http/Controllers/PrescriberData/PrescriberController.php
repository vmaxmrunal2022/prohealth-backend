<?php

namespace App\Http\Controllers\PrescriberData;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PrescriberController extends Controller
{

    use AuditTrait;
    public function getAll(Request $request)
    {
        $ndc = DB::table('PHYSICIAN_TABLE')->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function search(Request $request)
    {
        $ndc = DB::table('PHYSICIAN_TABLE')

            ->whereRaw('LOWER(PHYSICIAN_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orWhere('PHYSICIAN_FIRST_NAME', 'like', '%' . $request->search . '%')
            ->orWhere('PHYSICIAN_LAST_NAME', 'like', '%' . $request->search . '%')

            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
   

    public function getDetails($ndcid)
    {
        $ndc = DB::table('PHYSICIAN_TABLE')
            ->where('PHYSICIAN_ID', 'like', '%' . $ndcid . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function add(Request $request)
    {

       
        $validator = Validator::make($request->all(), [
            "physician_id" => ['required', 'max:10'],
            "phys_file_srce_id" => ['required','max:35'],
            "physician_first_name" => ['max:35'],
            "physician_last_name" => ['max:20'],
            "physician_specialty" => ['max:6'],
            "physician_title" => ['max:10'],
            "license_number" => ['max:12'],
            "physician_dea" => ['max:10'],
            "spin_number" => ['max:10'],
            "medical_group" => ['max:10'],
            "address_1" => ['max:50'],
            "city" => ['max:20'],
            "zip_code" => ['max:9'],
            "phone" => ['max:13'],
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        }


        $check = DB::table('PHYSICIAN_TABLE')
        ->where('physician_id',strtoupper($request->physician_id))
        ->first();

        if($request->new==1){

            if($check){

                return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $check);


            }
            else{


                $insert = DB::table('PHYSICIAN_TABLE')
                  ->insert([
                    'physician_id' => strtoupper($request->physician_id),
                    'physician_last_name' => $request->physician_last_name,
                    'phys_file_srce_id'=>$request->phys_file_srce_id,
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
              'phys_file_srce_id'=>$request->phys_file_srce_id,
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

    public function delete(Request $request)
    {
        if (isset($request->physician_id)) {

            $physician_id_delete =  DB::table('PHYSICIAN_TABLE')
                ->where('physician_id', $request->physician_id)
                ->delete();
            if ($physician_id_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found');
        }
    }

    
}
