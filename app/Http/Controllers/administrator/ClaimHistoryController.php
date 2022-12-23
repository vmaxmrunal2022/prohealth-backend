<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimHistoryController extends Controller
{
    public function searchHistory(Request $request)
    {
        

        if($request->data_type == 'date_filled')
        {
            if($request->from_date != null)
            {
                $from_date_filled = str_replace('-','',$request->from_date);
                // print_r(($from_date_filled));
            }else{
                $from_date_filled = null;
            }

            if($request->to_date != null)
            {
                $to_date_filled = str_replace('-','',$request->to_date);
            }else{
                $to_date_filled = null;
            }
            
            
        }else{
            if($request->from_date != null)
            {
                $from_date_submitted = str_replace('-','',$request->from_date);
            }else{
                $from_date_submitted = null;
            }

            if($request->to_date != null)
            {
                $to_date_submitted = str_replace('-','',$request->to_date);
            }else{
                $to_date_submitted = null;
            }
            
            
        }
        
// print_r($from_date_filled);
        $search_result = DB::table('rx_transaction_detail')
                        //  ->when($cardholder_id, function ($query) use ($cardholder_id) {
                        //     return $query->where('cardholder_id', 'like', '%'. $cardholder_id. '%');
                        //  })
                        ->where('cardholder_id', 'like', '%'. $request->cardholder_id .'%')
                        ->where('person_code', 'like', '%'. $request->person_code .'%')
                        ->where('patient_pin_number', 'like', '%'. $request->patient_pin_number .'%')    

                        // ->when($from_date_filled, function ($query) use ($from_date_filled) {
                        //     return $query->where('date_filled', '>=', $from_date_filled);
                        //  })

                        //  ->when($to_date_filled, function ($query) use ($to_date_filled) {
                        //     return $query->where('date_filled', '<=', $to_date_filled);
                        //  })

                        //  ->when($from_date_submitted, function ($query) use ($from_date_submitted) {
                        //     return $query->where('date_submitted', '>=', $from_date_submitted);
                        //  })

                        //  ->when($to_date_submitted, function ($query) use ($to_date_submitted) {
                        //     return $query->where('date_submitted', '<=', $to_date_submitted);
                        //  })
                        
                        
                        // ->where('date_filled', '>=', str_replace('-', '', $request->date_filled) )
                        // ->where('date_submitted', '<=', str_replace('-', '', $request->date_submitted) )

                        // ->where('patient_pin_number', 'like', '%'. $request->patient_pin_number .'%')
                        // ->where('patient_pin_number', 'like', '%'. $request->patient_pin_number .'%')
                        // ->where('patient_pin_number', 'like', '%'. $request->patient_pin_number .'%')
                        // ->where('provider_id', 'like', '%'. $request->provider_id .'%')
                        // ->where('cardholder', 'like', '%'. $request->cardholder_id .'%')
                         ->get();

        return $this->respondWithToken($this->token(), '', $search_result);
    }
}
