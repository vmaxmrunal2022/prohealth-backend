<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimHistoryController extends Controller
{
    use AuditTrait;
    // public function searchHistory(Request $request)
    // {
    //     if ($request->data_type == 'date_filled') {
    //         if ($request->date_filled != null) {
    //             $date_filled_filled = str_replace('-', '', $request->date_filled);
    //             // print_r(($date_filled_filled));
    //         } else {
    //             $date_filled_filled = null;
    //         }

    //         if ($request->date_submitted != null) {
    //             $date_submitted_filled = str_replace('-', '', $request->date_submitted);
    //         } else {
    //             $date_submitted_filled = null;
    //         }
    //     } else {
    //         if ($request->date_filled != null) {
    //             $date_filled_submitted = str_replace('-', '', $request->date_filled);
    //         } else {
    //             $date_filled_submitted = null;
    //         }

    //         if ($request->date_submitted != null) {
    //             $date_submitted_submitted = str_replace('-', '', $request->date_submitted);
    //         } else {
    //             $date_submitted_submitted = null;
    //         }
    //     }

    //     // print_r($date_filled_filled);
    //     $search_result = DB::table('rx_transaction_detail')
    //         //  ->when($cardholder_id, function ($query) use ($cardholder_id) {
    //         //     return $query->where('cardholder_id', 'like', '%'. $cardholder_id. '%');
    //         //  })
    //         ->where('cardholder_id',$request->cardholder_id)
    //         ->where('person_code', 'like', '%' . $request->person_code . '%')
    //         ->where('patient_pin_number', 'like', '%' . $request->patient_pin_number . '%')

    //         // ->when($date_filled_filled, function ($query) use ($date_filled_filled) {
    //         //     return $query->where('date_filled', '>=', $date_filled_filled);
    //         //  })

    //         //  ->when($date_submitted_filled, function ($query) use ($date_submitted_filled) {
    //         //     return $query->where('date_filled', '<=', $date_submitted_filled);
    //         //  })

    //         //  ->when($date_filled_submitted, function ($query) use ($date_filled_submitted) {
    //         //     return $query->where('date_submitted', '>=', $date_filled_submitted);
    //         //  })

    //         //  ->when($date_submitted_submitted, function ($query) use ($date_submitted_submitted) {
    //         //     return $query->where('date_submitted', '<=', $date_submitted_submitted);
    //         //  })


    //         // ->where('date_filled', '>=', str_replace('-', '', $request->date_filled) )
    //         // ->where('date_submitted', '<=', str_replace('-', '', $request->date_submitted) )

    //         // ->where('patient_pin_number', 'like', '%'. $request->patient_pin_number .'%')
    //         // ->where('patient_pin_number', 'like', '%'. $request->patient_pin_number .'%')
    //         // ->where('patient_pin_number', 'like', '%'. $request->patient_pin_number .'%')
    //         // ->where('provider_id', 'like', '%'. $request->provider_id .'%')
    //         // ->where('cardholder', 'like', '%'. $request->cardholder_id .'%')
    //         ->get();

    //     return $this->respondWithToken($this->token(), '', $search_result);
    // }

    public function searchHistory(Request $request){


        // dd($request->all());

        


          if($request->transaction_status == 'P'){

            if($request->sort == '1'){
                $data=DB::table('RX_TRANSACTION_DETAIL')
            ->where('DATE_FILLED', '>=', $request->date_filled)
            ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
            ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
            ->where('TRANSACTION_STATUS','P')
            ->orderByDesc('DATE_SUBMITTED')
            ->orderByDesc('TIME_SUBMITTED')

            ->get();
            return $this->respondWithToken($this->token(), '', $data);


            }else if($request->sort == '2'){


                $data=DB::table('RX_TRANSACTION_DETAIL')
                ->where('DATE_FILLED', '>=', $request->date_filled)
                ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
                ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
                ->where('TRANSACTION_STATUS','P')
                ->orderByDesc('PHARMACY_NABP')
                ->orderByDesc('RX_NUMBER')
                ->orderByDesc('DATE_FILLED')
                ->get();
                return $this->respondWithToken($this->token(), '', $data);
            }

            

         }

         else if($request->transaction_status == 'R'){

            if($request->sort == 1){

                $data=DB::table('RX_TRANSACTION_DETAIL')
                ->where('DATE_FILLED', '>=', $request->date_filled)
               ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
               ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
               ->where('TRANSACTION_STATUS','R')
               ->orderByDesc('DATE_SUBMITTED')
               ->orderByDesc('TIME_SUBMITTED')->get();
               return $this->respondWithToken($this->token(), '', $data);


            }else if($request->sort == 2){

                $data=DB::table('RX_TRANSACTION_DETAIL')
                ->where('DATE_FILLED', '>=', $request->date_filled)
               ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
               ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
               ->where('TRANSACTION_STATUS','R')
               ->orderByDesc('PHARMACY_NABP')
                ->orderByDesc('RX_NUMBER')
                ->orderByDesc('DATE_FILLED')
                ->get();
               return $this->respondWithToken($this->token(), '', $data);

            }

           

         }

         else if($request->transaction_status == 'X'){

            if($request->sort ==1){

                $data=DB::table('RX_TRANSACTION_DETAIL')
                ->where('DATE_FILLED', '>=', $request->date_filled)
                ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
                ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
                ->where('TRANSACTION_STATUS','X')
                ->orderByDesc('DATE_SUBMITTED')
                ->orderByDesc('TIME_SUBMITTED')->get();
                return $this->respondWithToken($this->token(), '', $data);

            }
            else if($request->sort == 2){
                
                $data=DB::table('RX_TRANSACTION_DETAIL')
                ->where('DATE_FILLED', '>=', $request->date_filled)
                ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
                ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
                ->where('TRANSACTION_STATUS','X')
                ->orderByDesc('PHARMACY_NABP')
                ->orderByDesc('RX_NUMBER')
                ->orderByDesc('DATE_FILLED')->get();
                return $this->respondWithToken($this->token(), '', $data);
            }

           


         }

         else{

            if($request->sort ==1){
                $data =  DB::table('RX_TRANSACTION_DETAIL')
                ->where('DATE_FILLED', '>=', $request->date_filled)
                ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
                ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
                ->get();
                return $this->respondWithToken($this->token(), '', $data);
    

            }else if($request->sort == 2){

                $data =  DB::table('RX_TRANSACTION_DETAIL')
                ->where('DATE_FILLED', '>=', $request->date_filled)
                ->where('DATE_SUBMITTED', '<=', $request->date_submitted)
                ->where('CARDHOLDER_ID', '=', $request->cardholder_id)
                ->get();
                return $this->respondWithToken($this->token(), '', $data);
            }




         


         }


               
    

    }

    public function claimReferenceDetails(Request $request)
    {
        $data = DB::table('rx_transaction_detail')
            ->where('claim_reference_number', $request->claim_reference_number)
            ->first();
        $record_snap = json_encode($data);
        $save_audit = $this->auditMethod('UP', $record_snap, 'rx_transaction_detail');
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getNDCDropdown(Request $request)
    {
        $ndcs = DB::table('NDC_EXCEPTION_LISTS')
            ->select('ndc')
            ->where('ndc', 'like', '%' . $request->search . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $ndcs);
    }

    public function getGPIDropdown(Request $request)
    {
        $gpis = DB::table('GPI_EXCEPTION_LISTS')
            ->select('GPI_EXCEPTION_LIST')
            ->where(DB::raw('UPPER(GPI_EXCEPTION_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $gpis);
    }

    public function getProcedureCode(Request $request)
    {
        $procedureCodes = DB::table('PROC_CODE_LISTS')
            // ->select('PROC_CODE_LISTS.PROCEDURE_CODE', 'PROC_CODE_LIST_NAMES.DESCRIPTION')
            // ->join('PROC_CODE_LIST_NAMES', 'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID', '=', 'PROC_CODE_LISTS.PROCEDURE_CODE')
            // ->where(DB::raw('UPPER(PROC_CODE_LISTS.PROCEDURE_CODE)'), 'like', '%'. strtoupper($request->search) .'%')
            // ->get();

            ->select('procedure_code')
            ->where('procedure_code', 'like', '%' . $request->search . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $procedureCodes);
    }

    public function getCustomerId(Request $request)
    {
        $customerIds = DB::table('CUSTOMER')
            ->select('customer_id', 'customer_name')
            ->where('customer_id', 'like', '%' . $request->search . '%')
            ->orWhere(DB::raw('UPPER(customer_name)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $customerIds);
    }

    public function getClientId(Request $request)
    {
        $clientIds = DB::table('CLIENT')
            ->select('CLIENT_ID')
            ->where('CLIENT_ID', 'like', '%' . $request->search . '%')
            ->orWhere(DB::raw('UPPER(CLIENT_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $clientIds);
    }

    public function getClientGroup(Request $request)
    {
        $clientGroups = DB::table('CLIENT_GROUP')
            ->select('CLIENT_GROUP_ID')
            ->where('CLIENT_GROUP_ID', 'like', '%' . $request->search . '%')
            ->orWhere(DB::raw('UPPER(GROUP_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $clientGroups);
    }

    public function searchOptionalData(Request $request)
    {
        // $searchOptionResult = DB::table('RX_TRANSACTION_LOG')     
        //                    ->where('rx_number', 'like', '%'. $request->rx_rx_number .'%')                      
        //                    ->where('claim_reference_number', 'like', '%'. $request->claim_reference_number .'%')                      
        //                    ->where('ndc', 'like', '%'. $request->ndc .'%')       
        //                    ->where('procedure_code', 'like', '%'. $request->procedure_code['procvalue'] .'%')                                     
        //                    ->where('customer_id', 'like', '%'. $request->customer_id['custvalue'] .'%')                                     
        //                    ->where('client_id', 'like', '%'. $request->client_id['clientvalue'] .'%')                                     
        //                    ->where('client_group_id', 'like', '%'. $request->client_group_id['client_groupvalue'] .'%')                                     
        //                    ->get();

        if ($request->ndc != null) {
            $ndc = $request->ndc;
        } else {
            $ndc = null;
        }

        if ($request->procedure_code != null) {
            $procedure_code = $request->procedure_code;
        } else {
            $procedure_code = null;
        }

        if ($request->customer_id != null) {
            $customer_id = $request->customer_id['custvalue'];
        } else {
            $customer_id = null;
        }

        if ($request->client_id != null) {
            $client_id = $request->client_id['clientvalue'];
        } else {
            $client_id = null;
        }

        if ($request->client_group_id != null) {
            $client_group_id = $request->clietnt_group_id['client_groupvalue'];
        } else {
            $client_group_id = null;
        }

        $searchOptionResult = DB::table('RX_TRANSACTION_DETAIL')
            ->where('rx_number', 'like', '%' . $request->rx_number . '%')
            ->where('claim_reference_number', 'like', '%' . $request->claim_reference_number . '%')
            ->when($ndc, function ($query) use ($ndc) {
                return $query->where('ndc', 'like', '%' . $ndc . '%');
            })

            ->when($procedure_code, function ($query) use ($procedure_code) {
                return $query->where('procedure_code', 'like', '%' . $procedure_code . '%');
            })

            ->when($customer_id, function ($query) use ($customer_id) {
                return $query->where('customer_id', 'like', '%' . $customer_id . '%');
            })

            ->when($client_id, function ($query) use ($client_id) {
                return $query->where('client_id', 'like', '%' . $client_id . '%');
            })

            ->when($client_group_id, function ($query) use ($client_group_id) {
                return $query->where('client_group_id', 'like', '%' . $client_group_id . '%');
            })
            ->get();

        return $this->respondWithToken($this->token(), '', $searchOptionResult);
    }

    public function searchClaimHistory(Request $request)
    {

        $date_of_service = date('Ymd', strtotime($request->date_of_service));
        $search_date_type = $request->search_date_type;
        $dates = ["search_date" => $date_of_service, "search_date_type" => $search_date_type];
        // dd($dates);
        $patient_pin_number = $request->patient_pin_number;
        $person_code = $request->person_code;
        $pharmacy_nabp = $request->provider_id;

        $rx_number = $request->rx_number;
        $claim_reference_number = $request->claim_reference_number;
        $ndc = $request->ndc;
        $gpi = $request->gpi;
        $procedure_id = $request->procedure_id;
        $customer_id = $request->customer_id;
        $client_id = $request->client_id;
        $client_group_id = $request->client_group_id;

        $search_claim_history = DB::table('RX_TRANSACTION_DETAIL')
            ->where(DB::raw('UPPER(CARDHOLDER_ID)'), 'like', '%' . strtoupper($request->cardholder_id) . '%')

            ->when($person_code, function ($query, $person_code) {
                return $query->where('person_code', 'like', '%' . $person_code . '%');
            })

            ->when($patient_pin_number, function ($query, $patient_pin_number) {
                return $query->where('PATIENT_PIN_NUMBER', 'like', '%' . $patient_pin_number . '%');
            })

            ->when($pharmacy_nabp, function ($query, $pharmacy_nabp) {
                return $query->where(DB::raw('UPPER(pharmacy_nabp)'), 'like', '%' . strtoupper($pharmacy_nabp) . '%');
            })

            ->when($dates, function ($query, $dates) {
                if ($dates['search_date_type'] == "from") {
                    return $query->where('DATE_FILLED', '>=', $dates['search_date']);
                } else {
                    return $query->where('DATE_FILLED', '<=', $dates['search_date']);
                }
            })

            ->when($rx_number, function ($query, $rx_number) {
                return $query->where('rx_number', $rx_number);
            })

            ->when($claim_reference_number, function ($query, $claim_reference_number) {
                return $query->where('claim_reference_number', $claim_reference_number);
            })

            ->when($ndc, function ($query, $ndc) {
                return $query->where('ndc', $ndc);
            })

            ->when($gpi, function ($query, $gpi) {
                return $query->where('GENERIC_PRODUCT_ID', $gpi);
            })

            ->when($procedure_id, function ($query, $procedure_id) {
                return $query->where('procedure_id', $procedure_id);
            })

            ->when($customer_id, function ($query, $customer_id) {
                return $query->where('customer_id', $customer_id);
            })

            ->when($client_id, function ($query, $client_id) {
                return $query->where('client_id', $client_id);
            })

            ->when($client_group_id, function ($query, $client_group_id) {
                return $query->where('client_group_id', $client_group_id);
            })


            ->get();


        // dd($search_claim_history);



        return $this->respondWithToken($this->token(), '', $search_claim_history);
    }
}
