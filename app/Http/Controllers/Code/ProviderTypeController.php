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
                                ->where('PROVIDER_TYPE', 'like', '%'.$request->code.'%')
                                ->orWhere('DESCRIPTION', 'like', '%'.$request->description.'%')
                                ->get();

        return $this->respondWithToken($this->token(), '', $procedurecodes);

    }

    public function add(Request $request)
    {
       
        $procedurecode = DB::table('PROVIDER_TYPES')->insert(
            [
                'PROVIDER_TYPE' => $request->provider_type_code,
                'DESCRIPTION' => $request->provider_type_description,
                'DATE_TIME_CREATED' => date('y-m-d'),
                'USER_ID_CREATED' => '',
                'USER_ID' => '',
                'DATE_TIME_MODIFIED' => '',
                'FORM_ID' => '',
                // 'COMPLETE_CODE_IND' => ''
            ]
        );

        return  $this->respondWithToken($this->token(), 'Successfully added', $procedurecode);
    }


    public function delete(Request $request)
    {
        return DB::table('PROVIDER_TYPES')->where('PROVIDER_TYPE', $request->id)->delete() 
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }
}
