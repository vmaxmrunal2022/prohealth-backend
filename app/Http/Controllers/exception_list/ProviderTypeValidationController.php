<?php

namespace App\Http\Controllers\exception_list;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderTypeValidationController extends Controller
{
    public function test(Request $req)
    {
        echo "test";
    }

    public function get(Request $request)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS_TEMP')
                            // ->where('effective_date', 'like', '%'.$request->search.'%')
                            ->Where('PROV_TYPE_LIST_ID', 'like', '%'.strtoupper($request->search).'%')
                            ->orWhere('proc_code_list_id', 'like', '%'.strtoupper($request->search).'%')
                            ->get();
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }

    public function getFormData(Request $request)
    {
        dd($request->id);
    }
}


