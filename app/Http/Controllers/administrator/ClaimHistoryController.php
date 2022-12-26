<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimHistoryController extends Controller
{
    public function searchHistory(Request $request)
    {
        if ($request->data_type == 'date_filled') {
            if ($request->from_date != null) {
                $from_date_filled = str_replace('-', '', $request->from_date);
                // print_r(($from_date_filled));
            } else {
                $from_date_filled = null;
            }

            if ($request->to_date != null) {
                $to_date_filled = str_replace('-', '', $request->to_date);
            } else {
                $to_date_filled = null;
            }
        } else {
            if ($request->from_date != null) {
                $from_date_submitted = str_replace('-', '', $request->from_date);
            } else {
                $from_date_submitted = null;
            }

            if ($request->to_date != null) {
                $to_date_submitted = str_replace('-', '', $request->to_date);
            } else {
                $to_date_submitted = null;
            }
        }

        // print_r($from_date_filled);
        $search_result = DB::table('rx_transaction_detail')
            //  ->when($cardholder_id, function ($query) use ($cardholder_id) {
            //     return $query->where('cardholder_id', 'like', '%'. $cardholder_id. '%');
            //  })
            ->where('cardholder_id', 'like', '%' . $request->cardholder_id . '%')
            ->where('person_code', 'like', '%' . $request->person_code . '%')
            ->where('patient_pin_number', 'like', '%' . $request->patient_pin_number . '%')

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
            ->where('procedure_code', 'like', '%'. $request->search .'%')
            ->get();
        return $this->respondWithToken($this->token(), '', $procedureCodes);
    }

    public function getCustomerId(Request $request)
    {
        $customerIds = DB::table('CUSTOMER')
                           ->select('customer_id', 'customer_name')
                           ->where('customer_id', 'like', '%'. $request->search .'%')
                           ->orWhere(DB::raw('UPPER(customer_name)'), 'like', '%'. strtoupper($request->search) .'%')
                           ->get();

        return $this->respondWithToken($this->token(), '', $customerIds);
    }

    public function getClientId(Request $request)
    {
        $clientIds = DB::table('CLIENT')
                           ->select('CLIENT_ID')
                           ->where('CLIENT_ID', 'like', '%'. $request->search .'%')
                           ->orWhere(DB::raw('UPPER(CLIENT_NAME)'), 'like', '%'. strtoupper($request->search) .'%')
                           ->get();

        return $this->respondWithToken($this->token(), '', $clientIds);
    }
    
    public function getClientGroup(Request $request)
    {
        $clientGroups = DB::table('CLIENT_GROUP')
                           ->select('CLIENT_GROUP_ID')
                           ->where('CLIENT_GROUP_ID', 'like', '%'. $request->search .'%')
                           ->orWhere(DB::raw('UPPER(GROUP_NAME)'), 'like', '%'. strtoupper($request->search) .'%')
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

        if($request->ndc != null)
        {
            $ndc = $request->ndc['ndcvalue'];
        }else{
            $ndc = null;
        }

        if($request->procedure_code != null)
        {
            $procedure_code = $request->procedure_code['procvalue'];
        }else{
            $procedure_code = null;
        }

        if($request->customer_id != null)
        {
            $customer_id = $request->customer_id['custvalue'];
        }else{
            $customer_id = null;
        }

        if($request->client_id != null)
        {
            $client_id = $request->client_id['clientvalue'];
        }else{
            $client_id = null;
        }

        if($request->client_group_id != null)
        {
            $client_group_id = $request->clietnt_group_id['client_groupvalue'];
        }else{
            $client_group_id = null;
        }


        $searchOptionResult = DB::table('RX_TRANSACTION_LOG')     
                           ->where('rx_number', 'like', '%'. $request->rx_number .'%')                      
                           ->where('claim_reference_number', 'like', '%'. $request->claim_reference_number .'%')                      
                           ->when($ndc, function($query) use ($ndc){
                            return $query->where('ndc', 'like', '%'. $ndc .'%');
                           })
                           
                           ->when($procedure_code, function($query) use ($procedure_code){
                            return $query->where('procedure_code', 'like', '%'. $procedure_code .'%');
                           })

                           ->when($customer_id, function($query) use ($customer_id){
                            return $query->where('customer_id', 'like', '%'. $customer_id .'%');
                           })

                           ->when($client_id, function($query) use ($client_id){
                            return $query->where('client_id', 'like', '%'. $client_id .'%');
                           })

                           ->when($client_group_id, function($query) use ($client_group_id){
                            return $query->where('client_group_id', 'like', '%'. $client_group_id .'%');
                           })
                           ->get();

        return $this->respondWithToken($this->token(), '', $searchOptionResult);
    }
    
}
