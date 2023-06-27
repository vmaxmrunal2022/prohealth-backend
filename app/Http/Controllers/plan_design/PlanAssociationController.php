<?php

namespace App\Http\Controllers\plan_design;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlanAssociationController extends Controller
{
    use AuditTrait;
    public function getDetails($binnumber,$process_control_number,$group_number)
    {

        $planAssociation = DB::table('PLAN_LOOKUP_TABLE')
                ->select('PLAN_LOOKUP_TABLE.*', 'client.client_name', 'client_group.group_name', 'customer.customer_name')
                ->leftjoin('customer', 'PLAN_LOOKUP_TABLE.customer_id', '=', 'customer.customer_id')
                ->leftjoin('client', 'PLAN_LOOKUP_TABLE.client_id', '=', 'client.client_id')
                ->leftjoin('client_group', 'PLAN_LOOKUP_TABLE.client_group_id', '=', 'client_group.client_group_id')
                ->where('PLAN_LOOKUP_TABLE.BIN_NUMBER', strtoupper($binnumber))
                ->where('PLAN_LOOKUP_TABLE.PROCESS_CONTROL_NUMBER',$process_control_number)
                ->where('PLAN_LOOKUP_TABLE.GROUP_NUMBER',$group_number)

                // ->where('PLAN_LOOKUP_TABLE.BIN_NUMBER', $id)
                ->get();
        return $this->respondWithToken($this->token(), '', $planAssociation);
    }


    public function search(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);
        if ($validator->fails()) {
            //return response($validator->errors(), 400);
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
        } else {
            $planAssociation = DB::table('PLAN_LOOKUP_TABLE')
                ->select('PLAN_LOOKUP_TABLE.*', 'client.client_name', 'client_group.group_name', 'customer.customer_name')
                ->leftjoin('client', 'PLAN_LOOKUP_TABLE.client_id', '=', 'client.client_id')
                ->leftjoin('client_group', 'PLAN_LOOKUP_TABLE.client_group_id', '=', 'client_group.client_group_id')
                ->leftjoin('customer', 'PLAN_LOOKUP_TABLE.customer_id', '=', 'customer.customer_id')
                ->where('PLAN_LOOKUP_TABLE.BIN_NUMBER', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('PLAN_LOOKUP_TABLE.PROCESS_CONTROL_NUMBER', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('PLAN_LOOKUP_TABLE.GROUP_NUMBER', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('PLAN_LOOKUP_TABLE.PLAN_ID', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('PLAN_LOOKUP_TABLE.PIN_NUMBER_SUFFIX', 'like', '%' . strtoupper($request->search) . '%')
                ->paginate(100);


            return $this->respondWithToken($this->token(), '', $planAssociation);
        }
    }

    public function submitPlanAssociation(Request $request)
    {
        $client_group_id = is_array($request->client_group_id) != null ?  $request->client_group_id['client_group_value']  : $request->client_group_id;

        $client_id = is_array($request->client_id) != null ? $request->client_id['client_value'] : $request->client_id;

        $customer_id = is_array($request->customer_id) != null ? $request->customer_id['cust_value'] : $request->customer_id;

        // $membership_processing_flag = is_array($request->membership_processing_flag) != null ? $request->membership_processing_flag['form_id_value'] : null;

        $pharmacy_chain = is_array($request->pharmacy_chain) != null ? $request->pharmacy_chain['pharm_value'] : $request->pharmacy_chain;

        $transaction_type = is_array($request->transaction_type) != null ? $request->transaction_type['tt_value'] : $request->transaction_type;

        $use_default_ccg = is_array($request->use_default_ccg) != null ? $request->use_default_ccg['ta_value'] : $request->use_default_ccg;


        $recordcheck = DB::table('plan_lookup_table')
            ->where('bin_number', strtoupper($request->bin_number))
            ->first();


        if ($request->add_new) {


            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'Bin Number already exists', $recordcheck);
            } else {

                $validator = Validator::make($request->all(), [
                    "bin_number" => ['required', 'max:6', Rule::unique('plan_lookup_table')->where(function ($q) {
                        $q->whereNotNull('bin_number')
                            ->whereNotNull('process_control_number')
                            ->whereNotNull('group_number');
                    })],
                    "process_control_number" => ['required', 'max:10'],
                    "group_number" => ['required', 'max:14'],
                ]);

                if ($validator->fails()) {
                    return response($validator->errors(), 400);
                } else {
                    // dd($request->use_default_ccg);exit();   
                    $planAssociation = DB::table('plan_lookup_table')
                        ->insert([
                            'bin_number' => strtoupper($request->bin_number),
                            'client_group_id' => $client_group_id,
                            'client_id' => $client_id,
                            'customer_id' => $customer_id,
                            'form_id' => $request->form_id,
                            'group_number' => strtoupper($request->group_number),
                            'membership_processing_flag' => $request->membership_processing_flag,
                            'pharmacy_chain' => $pharmacy_chain,
                            'pin_number_suffix' => $request->pin_number_suffix,
                            'plan_id' => $request->plan_id,
                            'plan_id_mail_order' => $request->plan_id_mail_order,
                            'process_control_number' => strtoupper($request->process_control_number),
                            'transaction_type' => $transaction_type,
                            'use_default_ccg' => $use_default_ccg,
                            'user_id' => $request->user_id,
                        ]);

                    // $planAssociation = DB::table('plan_lookup_table')
                    //     ->where('bin_number', 'like', '%' . $request->bin_number . '%')
                    //     ->where('process_control_number', 'like', '%' . $request->process_control_number . '%')
                    //     ->where('group_number', 'like', '%' . $request->group_number . '%')
                    //     ->first();

                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $planAssociation);
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                "bin_number" => ['required', 'max:6'],
                "process_control_number" => ['required', 'max:10'],
                "group_number" => ['required', 'max:14'],
            ]);

            if ($validator->fails()) {
                return response($validator->errors(), 400);
            } else {

                $planAssociation = DB::table('plan_lookup_table')
                    ->where('bin_number', $request->bin_number)
                    ->where('process_control_number',$request->process_control_number)
                    ->where('group_number', $request->group_number)
                    ->update([
                        'client_group_id' => $client_group_id,
                        'client_id' => $client_id,
                        'customer_id' => $customer_id,
                        'form_id' => $request->form_id,
                        'membership_processing_flag' => $request->membership_processing_flag,
                        'pharmacy_chain' => $pharmacy_chain,
                        'pin_number_suffix' => $request->pin_number_suffix,
                        'plan_id' => $request->plan_id,
                        'plan_id_mail_order' => $request->plan_id_mail_order,
                        'transaction_type' => $transaction_type,
                        'use_default_ccg' => $use_default_ccg,
                        'user_id' => $request->user_id,
                    ]);

                // $planAssociation = DB::table('plan_lookup_table')
                //     ->where('bin_number', 'like', '%' . $request->bin_number . '%')
                //     ->where('process_control_number', 'like', '%' . $request->process_control_number . '%')
                //     ->where('group_number', 'like', '%' . $request->group_number . '%')
                //     ->first();
                // print_r($planAssociation);

                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $planAssociation);
            }
        }
    }

    public function getPharmacyChain(Request $request)
    {
        $pharmacy_chain = DB::table('PHARMACY_CHAIN')
            ->where(DB::raw('UPPER(pharmacy_chain)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $pharmacy_chain);
    }

    public function getPharmacyChain_New(Request $request)
    {
        $pharmacy_chain = DB::table('PHARMACY_CHAIN')
            ->where(DB::raw('UPPER(pharmacy_chain)'), 'like', '%' . strtoupper($request->search) . '%')
            ->paginate(100);
        return $this->respondWithToken($this->token(), '', $pharmacy_chain);
    }

    public function getFormId(Request $request)
    {
        $formIds = null;

        return $this->respondWithToken($this->token(), '', $formIds);
    }

    public function getMemProcFlag(Request $request)
    {
        $memprocflags = [
            ['membership_processing_flag' => '0', 'label' => 'Not Required'],
            ['membership_processing_flag' => '1', 'label' => 'Required']
        ];

        return $this->respondWithToken($this->token(), '', $memprocflags);
    }

    public function getCustomer(Request $rqeuest)
    {
        $customers = DB::table('customer')
            ->select('customer_id', 'CUSTOMER_NAME','effective_date','termination_date')
            ->where(DB::raw('UPPER(customer_id)'), 'like', '%' . strtoupper($rqeuest->sarch) . '%')
            ->orWhere(DB::raw('UPPER(CUSTOMER_NAME)'), 'like', '%' . strtoupper($rqeuest->sarch) . '%')
            //->get();
            ->paginate(100);
        return $this->respondWithToken($this->token(), '', $customers);
    }

    
    public function getCustomerNew(Request $request)
    {
        // return $request;
        $customers = DB::table('customer')
                        ->select('customer_id', 'CUSTOMER_NAME','effective_date','termination_date')
                        ->whereRaw('LOWER(customer_id) LIKE ?', ['%' . strtolower($request->search) . '%'])
                        ->orwhereRaw('LOWER(CUSTOMER_NAME) LIKE ?', ['%' . strtolower($request->search) . '%'])
                        // ->where(DB::raw('UPPER(customer_id)'), 'like', '%' . strtoupper($rqeuest->sarch) . '%')
                        // ->orwhereRaw(DB::raw('UPPER(CUSTOMER_NAME)'), 'like', '%' . strtoupper($request->sarch) . '%')
                        ->paginate(100);
                        // ->get();

                       
        return $this->respondWithToken($this->token(), '', $customers);
    }

    public function getClient(Request $rqeuest)
    {
        $clients = DB::table('client')
            ->where(DB::raw('UPPER(client_id)'), 'like', '%' . strtoupper($rqeuest->search) . '%')
            ->orWhere(DB::raw('UPPER(client_name)'), 'like', '%' . strtoupper($rqeuest->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $clients);
    }
    public function getClientNew(Request $request)
    {
        $clients = DB::table('client')
                        ->where(function ($query) use ($request) {
                            if (isset($request->customer_id)) {
                                $query->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id))
                                    ->when(isset($request->search), function ($subquery) use ($request) {
                                        return $subquery->where(function ($subquery2) use ($request) {
                                            $subquery2->whereRaw('LOWER(client_id) LIKE ?', ['%' . strtolower($request->search) . '%'])
                                                ->orWhereRaw('LOWER(client_name) LIKE ?', ['%' . strtolower($request->search) . '%']);
                                        });
                                    });
                            }
                        })
                    ->paginate(100);
        return $this->respondWithToken($this->token(), '', $clients);
    }

    public function getClientCustomer(Request $request)
    {

        // return ;
        // dd($request->all());
        $cust_id = strtoupper(explode("?", $request->customerData)[0]);
        // dd($cust_id);
        $clients = DB::table('client')
            ->where(DB::raw('UPPER(customer_id)'), strtoupper($cust_id))
            //->get();
            ->distinct()
            ->paginate(100);
        return $this->respondWithToken($this->token(), '', $clients);
    }

    public function getClientGroup(Request $rqeuest)
    {
        $client_groups =  DB::table('client_group')
            ->where(DB::raw('UPPER(client_group_id)'), 'like', '%' . strtoupper($rqeuest->search) . '%')
            ->orWhere(DB::raw('UPPER(group_name)'), 'like', '%' . strtoupper($rqeuest->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $client_groups);
    }

    public function getClientGroupNew(Request $request)
    {
        $client_groups =  DB::table('client_group')
            ->where(function ($query) use ($request) {
                if (isset($request->customer_id) && isset($request->client_id) ){
                    $query->where(DB::raw('UPPER(customer_id)'), strtoupper($request->customer_id));
                    $query->where(DB::raw('UPPER(client_id)'), strtoupper($request->client_id))
                        ->when(isset($request->search), function ($subquery) use ($request) {
                            return $subquery->where(function ($subquery2) use ($request) {
                                $subquery2->whereRaw('LOWER(client_group_id) LIKE ?', ['%' . strtolower($request->search) . '%'])
                                    ->orWhereRaw('LOWER(group_name) LIKE ?', ['%' . strtolower($request->search) . '%']);
                            });
                        });
                }
            })
        ->paginate(100);
       
        return $this->respondWithToken($this->token(), '', $client_groups);
    }

    public function getTransactionType(Request $request)
    {
        $transaction_types = [
            ['trans_type_value' => 'EV', 'trans_type_label' => 'EV - Eligibility Verification'],
            ['trans_type_value' => 'CC', 'trans_type_label' => 'CC - Claims Capture'],
            ['trans_type_value' => 'CA', 'trans_type_label' => 'CA - Claims Adjudication'],
            ['trans_type_value' => 'WC', 'trans_type_label' => 'WC - Workers Compensation Group'],
        ];
        return $this->respondWithToken($this->token(), '', $transaction_types);
    }

    public function getTransactionAssociation(Request $request)
    {
        $trans_association = [
            ['trans_ass_value' => '0', 'trans_ass_label' => 'Not Applicable'],
            ['trans_ass_value' => '1', 'trans_ass_label' => 'Billable Source For Plans W/O Eligibility'],
            ['trans_ass_value' => '2', 'trans_ass_label' => 'Restrictive Eligibility'],
        ];
        return $this->respondWithToken($this->token(), '', $trans_association);
    }

    public function getClientGroupLabel(Request $request)
    {
        $client_group_label = DB::table('client_group')
            ->select('group_name')
            ->where('client_group_id', $request->search)
            ->first();

        return $this->respondWithToken($this->token(), '', $client_group_label);
    }

    public function getPlanId(Request $request)
    {
        $planIds = DB::table('plan_table')
            // ->select('id')
            ->paginate(100);

        return $this->respondWithToken($this->token(), '', $planIds);
    }

    public function getPlanId_New(Request $request)
    {
        $planIds = DB::table('plan_table')
            // ->select('id')
            ->paginate(100);

        return $this->respondWithToken($this->token(), '', $planIds);
    }

    public function planassoociationDelete(Request $request){
        if(isset($request->bin_number ) && isset($request->process_control_number) &&  isset($request->group_number ) ) {
            $planAssociation = DB::table('plan_lookup_table')
                    ->where('bin_number', $request->bin_number)
                    ->where('process_control_number', $request->process_control_number)
                    ->where('group_number', $request->group_number )->delete() ;

            if ($planAssociation) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }

    
}


