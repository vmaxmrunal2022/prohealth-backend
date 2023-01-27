<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderTypeController extends Controller
{
    public function get(Request $request)
    {
        $procedurecodes = DB::table('PROVIDER_TYPES')
            ->where('PROVIDER_TYPE', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $procedurecodes);
    }


    public function IdSearch(Request $request)
    {
        $priceShedule = DB::table('RX_NETWORKS')
            ->where('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $priceShedule);
    }

    public function add(Request $request)
    {

        // $procedurecode = DB::table('PROVIDER_TYPES')->updateOrInsert(
        //     [
        //         'PROVIDER_TYPE' => strtoupper($request->provider_type),
        //     ],
        //     [
        //         'PROVIDER_TYPE' => strtoupper($request->provider_type),
        //         'DESCRIPTION' => strtoupper($request->description),
        //         'DATE_TIME_CREATED' => date('y-m-d'),
        //         'USER_ID_CREATED' => '',
        //         'USER_ID' => '',
        //         'DATE_TIME_MODIFIED' => '',
        //         'FORM_ID' => '',
        //         // 'COMPLETE_CODE_IND' => ''
        //     ]
        // );
        if ($request->new) {
            $procedurecode = DB::table('PROVIDER_TYPES')->insert(
                [
                    'PROVIDER_TYPE' => strtoupper($request->provider_type),
                    'DESCRIPTION' => strtoupper($request->description),
                    'DATE_TIME_CREATED' => date('y-m-d'),
                    'USER_ID_CREATED' => '',
                    'USER_ID' => '',
                    'DATE_TIME_MODIFIED' => '',
                    'FORM_ID' => '',
                    // 'COMPLETE_CODE_IND' => ''
                ]
            );
            return  $this->respondWithToken($this->token(), 'Added successfully!', $procedurecode);
        } else {
            $procedurecode = DB::table('PROVIDER_TYPES')
                ->where(DB::raw('UPPER(PROVIDER_TYPE)'), strtoupper($request->provider_type))
                ->update(
                    [
                        // 'PROVIDER_TYPE' => strtoupper($request->provider_type),
                        'DESCRIPTION' => strtoupper($request->description),
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'FORM_ID' => '',
                        // 'COMPLETE_CODE_IND' => ''
                    ]
                );
            return  $this->respondWithToken($this->token(), 'Updated successfully!', $procedurecode);
        }

        // $procedurecode = DB::table('PROVIDER_TYPES')->where('PROVIDER_TYPE', $request->provider_type)->first();
        // 49cd477a9edf20221018112956 mobi

    }


    public function delete(Request $request)
    {
        return DB::table('PROVIDER_TYPES')->where('PROVIDER_TYPE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }

    public function checkProviderTypeExist(Request $request)
    {
        $check = DB::table('PROVIDER_TYPES')
            ->where(DB::raw('UPPER(PROVIDER_TYPE)'), strtoupper($request->search))
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $check);
    }
}
