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
            ->where('DIAGNOSIS_ID', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('DESCRIPTION', 'like', '%' .$request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $benefitcodes);
    }

    public function add(Request $request)
    {


        $benefitcode = DB::table('DIAGNOSIS_CODES')->updateOrInsert(
            [
                'DIAGNOSIS_ID' => strtoupper($request->diagnosis_id),
                'DESCRIPTION' => $request->description,
            ],
            [
                
                'DATE_TIME_CREATED' => date('y-m-d'),
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'USER_ID_CREATED' => '',
                'FORM_ID' => '',
                'COMPLETE_CODE_IND' => ''
            ]
        );

        $code = DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))->where('DESCRIPTION' , strtoupper($request->description))->first();

        return  $this->respondWithToken($this->token(), 'Successfully added', $code);
    }

    public function delete(Request $request)
    {
        return  DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
