<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BenifitController extends Controller
{
    public function get(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "search" => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $benefitcodes = DB::table('benefit_codes')
                ->where(DB::raw('UPPER(benefit_code)'), 'like', '%' .  strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(description)'), 'like', '%' .  strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $benefitcodes);
        }
    }

    public function get_all(Request $request)
    {
        
            $benefitcodes = DB::table('benefit_codes')->get();
            if($benefitcodes){

                return $this->respondWithToken($this->token(), 'Datafetched Successfully', $benefitcodes);

            }else{
                return $this->respondWithToken($this->token(), 'something went wrong', $benefitcodes);

            }
        
    }

    public function add(Request $request)
    {
        $response  = new Response();
        // $response = $response->setStatusCode(200);
        // return $response;
        $createddate = date('y-m-d');

        if ($request->new) {
            $validator = Validator::make($request->all(), [
                'benefit_code' => ['required', 'max:10', Rule::unique('benefit_codes')->where(function ($q) {
                    $q->whereNotNull('benefit_code');
                })],
                "description" => ['max:36']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $benefitcode = DB::table('benefit_codes')->insert(
                    [
                        'benefit_code' => strtoupper($request->benefit_code),
                        'description' => $request->description,
                        'DATE_TIME_CREATED' => $createddate,
                        'USER_ID' => '', // TODO add user id
                        'DATE_TIME_MODIFIED' => '',
                        'USER_ID_CREATED' => '',
                        'FORM_ID' => ''
                    ]
                );
                $benefitcode = DB::table('benefit_codes')->where('benefit_code', 'like', '%' . $request->benefit_code . '%')->first();
            }
            return $this->respondWithToken($this->token(), 'Record Added Successfully ', $benefitcode);

            // } else if ($request->has('new')) {
        } else {
            $validator = Validator::make($request->all(), [
                'benefit_code' => ['required', 'max:10'],
                "description" => ['max:36']
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $benefitcode = DB::table('benefit_codes')
                    ->where('benefit_code', $request->benefit_code)
                    ->update(
                        [
                            'benefit_code' => strtoupper($request->benefit_code),
                            'description' => $request->description,
                            'DATE_TIME_CREATED' => $createddate,
                            'USER_ID' => '', // TODO add user id
                            'DATE_TIME_MODIFIED' => '',
                            'USER_ID_CREATED' => '',
                            'FORM_ID' => ''
                        ]
                    );
            }
            $benefitcode = DB::table('benefit_codes')->where('benefit_code', 'like', $request->benefit_code)->first();
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
        }
    }



    public function delete(Request $request)
    {
        return  DB::table('benefit_codes')->where('benefit_code', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Record Deleted Successfully')
            : $this->respondWithToken($this->token(), 'Could find data');
    }

    public function checkBenifitCodeExist(Request $reqeust)
    {
        $isExist = DB::table('benefit_codes')
            ->where(DB::raw('UPPER(benefit_code)'), strtoupper($reqeust->search))
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $isExist);
    }
}
