<?php

namespace App\Http\Controllers\plan_design;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanAssociationController extends Controller
{
    public function get(Request $request)
    {
        $planAssociation = DB::table('PLAN_LOOKUP_TABLE')
                           ->where('BIN_NUMBER', 'like', '%'. strtoupper($request->search) .'%')
                           ->orWhere('PROCESS_CONTROL_NUMBER', 'like', '%'. strtoupper($request->search) .'%')
                           ->orWhere('GROUP_NUMBER', 'like', '%'. strtoupper($request->search) .'%')
                           ->orWhere('PLAN_ID', 'like', '%'. strtoupper($request->search) .'%')
                           ->orWhere('PIN_NUMBER_SUFFIX', 'like', '%'. strtoupper($request->search) .'%')
                           ->get();

        return $this->respondWithToken($this->token(), '', $planAssociation);
    }

}
