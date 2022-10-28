<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BenifitController extends Controller
{
    public function get(Request $request)
    {
        // dd($request->all());
        $benefitcodes = DB::table('benefit_codes')
)            ->where('benefit_code', 'like', '%' .  strtoupper($request->search) . '%'))
            ->Where('description', 'like', '%' .  strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $benefitcodes);
    }

    public function add(Request $request)
    {
        
        $createddate = date('y-m-d');
        $benefitcode = DB::table('benefit_codes')->insert(
            [
                'benefit_code' => $request->benefit_code,
                'description' => $request->benefit_description,
                'DATE_TIME_CREATED' => $createddate,
                'USER_ID' => '', // TODO add user id
                'DATE_TIME_MODIFIED' => '',
                'USER_ID_CREATED' => '',
                'FORM_ID' => ''
            ]
        );

        return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
    }

    public function delete(Request $request)
    {
        return  DB::table('benefit_codes')->where('benefit_code', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
