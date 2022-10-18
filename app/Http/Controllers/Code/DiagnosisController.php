<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosisController extends Controller
{
    public function get(Request $request)
    {
        
        $benefitcodes = DB::table('DIAGNOSIS_CODES')
                            ->where('DIAGNOSIS_ID', 'like', '%'.$request->code.'%')
                            ->orWhere('DESCRIPTION', 'like', '%'.$request->description.'%')
                            ->get();
                            
        return $this->respondWithToken($this->token(), '', $benefitcodes);
    }

    public function add(Request $request)
    {
       
        $benefitcode = DB::table('DIAGNOSIS_CODES')->insert(
            [
                'DIAGNOSIS_ID' => $request->diagnosis_code,
                'DESCRIPTION' => $request->diagnosis_description,
                'DATE_TIME_CREATED' => date('y-m-d'),
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'USER_ID_CREATED' => '',
                'FORM_ID' => '',
                'COMPLETE_CODE_IND' => ''
            ]
        );

        return  $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }

    public function delete(Request $request)
    {
        return  DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', $request->id)->delete() 
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
