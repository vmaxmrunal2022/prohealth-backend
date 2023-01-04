<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimHistoryController extends Controller
{
   public function get(Request $request)
   {
     $data = DB::table('RX_TRANSACTION_DETAIL')
                 ->get();

    return $this->respondWithToken($this->token(), '', $data);

   }

   public function search(Request $request){
      $data = DB::table('RX_TRANSACTION_DETAIL')
               ->where(DB::raw('UPPER(CARDHOLDER_ID)'),'like', '%' .strtoupper($request->cardholder_id). '%')
               ->get();
   }
}
