<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouseOfLossController extends Controller
{
    public function get(Request $request)
    {
        $procedurecodes = DB::table('CAUSE_OF_LOSS_CODES')
            ->where('CAUSE_OF_LOSS_CODE', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return  $this->respondWithToken($this->token(), '', $procedurecodes);
    }

    public function add(Request $request)
    {
        if ($request->has('new')) {
            $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')->insert(
                [
                    'CAUSE_OF_LOSS_CODE' => strtoupper($request->causeofloss_code),
                    'DESCRIPTION' => $request->causeofloss_description,
                    'DATE_TIME_CREATED' => date('y-m-d'),
                    'USER_ID_CREATED' => '',
                    'USER_ID' => '',
                    'DATE_TIME_MODIFIED' => '',
                    'FORM_ID' => '',
                    'COMPLETE_CODE_IND' => ''
                ]
            );

            $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')->where('CAUSE_OF_LOSS_CODE', 'like', strtoupper($request->causeofloss_code))->first();
        } else {
            $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')
                ->where('CAUSE_OF_LOSS_CODES', 'like', strtoupper($request->benefit_code))
                ->update(
                    [
                        'CAUSE_OF_LOSS_CODE' => strtoupper($request->causeofloss_code),
                        'DESCRIPTION' => $request->causeofloss_description,
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'FORM_ID' => '',
                        'COMPLETE_CODE_IND' => ''
                    ]
                );
        }

        $procedurecode = DB::table('CAUSE_OF_LOSS_CODES')->where('CAUSE_OF_LOSS_CODE', 'like', strtoupper($request->causeofloss_code))->first();


        return   $this->respondWithToken($this->token(), 'Successfully added', $procedurecode);
    }

    public function delete(Request $request)
    {
        return DB::table('CAUSE_OF_LOSS_CODES')->where('REASON_CODE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
