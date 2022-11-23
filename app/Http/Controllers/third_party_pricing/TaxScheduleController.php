<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxScheduleController extends Controller
{
    //
    public function get(Request $request)
    {
        $taxData = DB::table('tax_schedule')->get();
        
        return $this->respondWithToken($this->token(), '', $taxData);
    }
}
