<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeUnit\FunctionUnit;

class ProcedureUcrList extends Controller
{
    public function get(Request $request)
    {
        $ucrName = DB::table('procedure_ucr_names')
            ->where('PROCEDURE_UCR_ID', 'like', '%' . $request->search. '%')
            ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ucrName);
    }

    public function getProcedureListData(Request $request)
    {
        $ucrList = DB::table('procedure_ucr_names')
            ->join('procedure_ucr_list', 'procedure_ucr_names.procedure_ucr_id', '=', 'procedure_ucr_list.procedure_ucr_id')
            ->where('procedure_ucr_list.procedure_ucr_id', $request->search)
            ->get();
        return $this->respondWithToken($this->token(), '', $ucrList);
    }

    public function submitProcedureListcopy(Request $request)
    {

        $validate = DB::table('procedure_ucr_names')->where('procedure_ucr_id', $request->procedure_ucr_id)->get();
        // return "hi" . $validate->count();

        if ($request->add_new == 1) {
            // echo $request->add_new;

            if ($validate->count() > 0) {
                return $this->respondWithToken($this->token(), 'Procedure UCR List ID Already Exists', $validate, true, 200, 1);
            }

            $add_procedure_names = DB::table('procedure_ucr_names')
                ->insert([
                    'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    'DESCRIPTION' => $request->description
                ]);

            $add_procedure_list = DB::table('PROCEDURE_UCR_LIST')
                ->insert([
                    'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    'procedure_code'   => $request->procedure_code,
                    'effective_date'   => $request->effective_date,
                    'termination_date' => $request->termination_date,
                    'unit_value'       => $request->unit_value,
                    'UCR_CURRENCY'     => $request->ucr_currency,
                ]);
            return $this->respondWithToken($this->token(), 'Record Added Successfully', $add_procedure_list);
        } else if ($request->add_new == 0) {
            if ($validate->count() < 1) {
                return $this->respondWithToken($this->token(), 'Record Not Found', $validate, false, 404, 0);
            }

            $update_procedure_names = DB::table('PROCEDURE_UCR_NAMES')
                ->where('procedure_ucr_id', $request->procedure_ucr_id)
                ->update([
                    // 'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    'DESCRIPTION' => $request->description
                ]);

            $update_procedure_list = DB::table('PROCEDURE_UCR_LIST')
                ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                ->where('procedure_code', $request->procedure_code)
                ->update([
                    // 'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    // 'procedure_code' => $request->procedure_code,
                    'effective_date' => $request->effective_date,
                    'termination_date' => $request->termination_date,
                    'unit_value' => $request->unit_value,
                    'UCR_CURRENCY' => $request->ucr_currency,
                ]);
            // dd($update_procedure_list);
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update_procedure_list);
        }
    }


    public function submitProcedureList(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('procedure_ucr_names')
        ->where('PROCEDURE_UCR_ID',$request->procedure_ucr_id)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'procedure_ucr_id' => ['required', 'max:10', Rule::unique('procedure_ucr_names')->where(function ($q) {
                    $q->whereNotNull('procedure_ucr_id');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('procedure_ucr_names')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "description" => ['max:36'],
              



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'NDC Exception Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('procedure_ucr_names')->insert(
                    [
                        'procedure_ucr_id' => $request->procedure_ucr_id,
                        'description'=>$request->description,
                        
                    ]
                );
    
                $add = DB::table('PROCEDURE_UCR_LIST')
                ->insert([
                    'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    'procedure_code'   => $request->procedure_code,
                    'effective_date'   => $request->effective_date,
                    'termination_date' => $request->termination_date,
                    'unit_value'       => $request->unit_value,
                    'UCR_CURRENCY'     => $request->ucr_currency,
                ]);
                $add = DB::table('PROCEDURE_UCR_LIST')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                'procedure_ucr_id' => ['required', 'max:10'],
                


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }
    
                $procedure_ucr_names = DB::table('procedure_ucr_names')
                ->where('procedure_ucr_id', $request->procedure_ucr_id )
                ->first();
                    
    
                $checkGPI = DB::table('PROCEDURE_UCR_LIST')
                    ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                    ->where('PROCEDURE_CODE',$request->procedure_code)
                    ->get()
                    ->count();

                    // dd($checkGPI);


                $effect_date_check = DB::table('PROCEDURE_UCR_LIST')
                ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                ->where('PROCEDURE_CODE',$request->procedure_code)
                ->where('EFFECTIVE_DATE',$request->effective_date)
                ->where('TERMINATION_DATE',$request->termination_date)

                    ->get()
                    ->count();
                    // dd($effective_date);
                // if result >=1 then update NDC_EXCEPTION_LISTS table record
                //if result 0 then add NDC_EXCEPTION_LISTS record


                if($effect_date_check == 1){

                    $add_names = DB::table('procedure_ucr_names')
                    ->where('procedure_ucr_id',$request->procedure_ucr_id)
                    ->update(
                        [
                            'description'=>$request->description,
                            
                        ]
                    );


                    $update = DB::table('PROCEDURE_UCR_LIST' )
                    ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                    ->where('PROCEDURE_CODE',$request->procedure_code)
                    ->where('EFFECTIVE_DATE',$request->effective_date)    
     
                    ->update([
                        // 'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                        // 'procedure_code' => $request->procedure_code,
                        'effective_date' => $request->effective_date,
                        'termination_date' => $request->termination_date,
                        'unit_value' => $request->unit_value,
                        'UCR_CURRENCY' => $request->ucr_currency,
                    ]);
                        $update = DB::table('PROCEDURE_UCR_LIST')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);

                   


                }else if($checkGPI == 1)
                {

                    return $this->respondWithToken($this->token(), 'Record already  exists',$checkGPI);


                }
                else{
                    if ($checkGPI <= "0") {
                        $update = DB::table('PROCEDURE_UCR_LIST')
                        ->insert([
                            'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                            'procedure_code'   => $request->procedure_code,
                            'effective_date'   => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'unit_value'       => $request->unit_value,
                            'UCR_CURRENCY'     => $request->ucr_currency,
                        ]);
                      
                       
                    $add_names = DB::table('procedure_ucr_names')
                    ->where('procedure_ucr_id',$request->procedure_ucr_id)
                    ->update(
                        [
                            'description'=>$request->description,
                            
                        ]
                    );
    
                    $update = DB::table('procedure_ucr_names')->where('procedure_ucr_id', 'like', '%' . $request->procedure_ucr_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
    
                    } 

                }
               
                

    
            
            }

           
        }
    }

    public function getProcedureCode(Request $request)
    {
        $procedure_codes = DB::table('procedure_codes')
            ->get();
        return $this->respondWithToken($this->token(), '', $procedure_codes);
    }
}
