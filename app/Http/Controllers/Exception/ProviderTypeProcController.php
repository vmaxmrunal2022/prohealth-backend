<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProviderTypeProcController extends Controller
{
    use AuditTrait;
    public function search(Request $request)
    {
        $data = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
            ->select('PROV_TYPE_PROC_ASSOC_ID', 'DESCRIPTION')
            // ->where('PROV_TYPE_PROC_ASSOC_ID', 'like', '%' . $request->search . '%')

            ->whereRaw('LOWER(PROV_TYPE_PROC_ASSOC_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orWhere('DESCRIPTION', 'like', '%' . $request->search . '%')
            ->get();


        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getList($id)
    {

        $data = DB::table('PROV_TYPE_PROC_ASSOC')
            ->join('PROV_TYPE_PROC_ASSOC_NAMES', 'PROV_TYPE_PROC_ASSOC_NAMES.PROV_TYPE_PROC_ASSOC_ID', '=', 'PROV_TYPE_PROC_ASSOC.PROV_TYPE_PROC_ASSOC_ID')
            ->where('PROV_TYPE_PROC_ASSOC.prov_type_proc_assoc_id', 'like', '%' . $id . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getDetails(Request $request)
    {

        $Details = DB::table('PROV_TYPE_PROC_ASSOC')


            ->select(
                'PROV_TYPE_PROC_ASSOC.*',
                'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_strategy_description',
                'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_strategy_name_description',
                'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_NAME as pricing_strategy_name_description',
                'PROC_CODE_LIST_NAMES.DESCRIPTION as proc_code_list_description',
                'PROV_TYPE_PROC_ASSOC_NAMES.DESCRIPTION as description',
                'SERVICE_MODIFIERS.DESCRIPTION as service_modifier_description',
                'PROVIDER_TYPES.DESCRIPTION as provider_type_description'
            )
            ->leftjoin('ACCUM_BENE_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID', '=', 'PROV_TYPE_PROC_ASSOC.ACCUM_BENE_STRATEGY_ID')
            ->leftjoin('COPAY_STRATEGY_NAMES', 'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID', '=', 'PROV_TYPE_PROC_ASSOC.COPAY_STRATEGY_ID')
            ->leftjoin('PRICING_STRATEGY_NAMES', 'PRICING_STRATEGY_NAMES.PRICING_STRATEGY_ID', '=', 'PROV_TYPE_PROC_ASSOC.PRICING_STRATEGY_ID')
            ->leftjoin('PROC_CODE_LIST_NAMES', 'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID', '=', 'PROV_TYPE_PROC_ASSOC.PROC_CODE_LIST_ID')
            ->leftjoin('PROV_TYPE_PROC_ASSOC_NAMES', 'PROV_TYPE_PROC_ASSOC_NAMES.PROV_TYPE_PROC_ASSOC_ID', '=', 'PROV_TYPE_PROC_ASSOC.PROV_TYPE_PROC_ASSOC_ID')
            ->leftjoin('SERVICE_MODIFIERS', 'SERVICE_MODIFIERS.SERVICE_MODIFIER', '=', 'PROV_TYPE_PROC_ASSOC.SERVICE_MODIFIER')
            ->leftjoin('PROVIDER_TYPES', 'PROVIDER_TYPES.PROVIDER_TYPE', '=', 'PROV_TYPE_PROC_ASSOC.PROVIDER_TYPE')


            ->where('PROV_TYPE_PROC_ASSOC.PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_pro_ass_id)
            ->where('PROV_TYPE_PROC_ASSOC.PROVIDER_TYPE', $request->provider_type)
            ->where('PROV_TYPE_PROC_ASSOC.PROC_CODE_LIST_ID', $request->procedure_code)
            ->where('PROV_TYPE_PROC_ASSOC.SERVICE_MODIFIER', $request->servive_modifier)
            ->where('PROV_TYPE_PROC_ASSOC.EFFECTIVE_DATE', $request->effective_date)
            ->first();

        return $this->respondWithToken($this->token(), '', $Details);
    }


    public function addcopy(Request $request)
    {

        $createddate = date('y-m-d');

        $recordcheck = DB::table('PROV_TYPE_PROC_ASSOC')
            ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
            ->first();


        if ($request->has('new')) {


            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'ProviderType ID Already Exists ', $recordcheck);
            } else {

                $insert1 = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')->insert(
                    [
                        'PROV_TYPE_PROC_ASSOC_ID' => $request->prov_type_proc_assoc_id,
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => $createddate,
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => $createddate,


                    ]
                );




                $insert = DB::table('PROV_TYPE_PROC_ASSOC')->insert(
                    [
                        'PROV_TYPE_PROC_ASSOC_ID' => $request->prov_type_proc_assoc_id,
                        'PROVIDER_TYPE' => $request->provider_type,
                        'SERVICE_MODIFIER' => $request->service_modifier,
                        'UCR' => $request->ucr,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,
                        'DATE_TIME_CREATED' => '',
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                        'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                        'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                        'MESSAGE' => $request->message,
                        'MESSAGE_STOP_DATE' => $request->message_stop_date,
                        'MIN_AGE' => $request->min_age,
                        'MAX_AGE' => $request->max_age,
                        'MIN_PRICE' => $request->min_price,
                        'MAX_PRICE' => $request->max_price,
                        'MIN_PRICE_OPT' => $request->min_price_opt,
                        'MAX_PRICE_OPT' => $request->max_price_opt,
                        'VALID_RELATION_CODE' => $request->valid_relation_code,
                        'SEX_RESTRICTION' => $request->sex_restriction,
                        'MODULE_EXIT' => $request->module_exit,
                        'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                        'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                        'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                        'COVERAGE_START_DAYS' => $request->coverage_start_days,
                        'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                        'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,



                    ]
                );
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $insert);
            }
        } else {


            $update1 = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
                ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                ->update(
                    [
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => '',
                        'USER_ID_CREATED' => '',
                        'USER_ID' => ''


                    ]
                );




            $update = DB::table('PROV_TYPE_PROC_ASSOC')
                ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                ->update(
                    [
                        'PROVIDER_TYPE' => $request->provider_type,
                        'SERVICE_MODIFIER' => $request->service_modifier,
                        'UCR' => $request->ucr,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,
                        'DATE_TIME_CREATED' => '',
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                        'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                        'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                        'MESSAGE' => $request->message,
                        'MESSAGE_STOP_DATE' => $request->message_stop_date,
                        'MIN_AGE' => $request->min_age,
                        'MAX_AGE' => $request->max_age,
                        'MIN_PRICE' => $request->min_price,
                        'MAX_PRICE' => $request->max_price,
                        'MIN_PRICE_OPT' => $request->min_price_opt,
                        'MAX_PRICE_OPT' => $request->max_price_opt,
                        'VALID_RELATION_CODE' => $request->valid_relation_code,
                        'SEX_RESTRICTION' => $request->sex_restriction,
                        'MODULE_EXIT' => $request->module_exit,
                        'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                        'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                        'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                        'COVERAGE_START_DAYS' => $request->coverage_start_days,
                        'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                        'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,

                    ]
                );


            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
        }
    }


    public function add(Request $request)
    {
        //    return 'hii';
        $createddate = date('y-m-d');

        $validation = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
            ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
            ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'prov_type_proc_assoc_id' => [
                    'required',
                    'max:10', Rule::unique('PROV_TYPE_PROC_ASSOC_NAMES')->where(function ($q) {
                        $q->whereNotNull('prov_type_proc_assoc_id');
                    })
                ],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => [
                //     'required',
                //     'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //         $q->whereNotNull('effective_date');
                //     })
                // ],

                // 'ndc_exception_list' => [
                //     'required',
                //     'max:10', Rule::unique('PROV_TYPE_PROC_ASSOC_NAMES')->where(function ($q) {
                //         $q->whereNotNull('ndc_exception_list');
                //     })
                // ],

                "description" => ['required', 'max:36'],
                "provider_type" => ['required', 'max:2'],
                "service_modifier" => ['required', 'max:2'],
                'ucr' => ['nullable', 'max:10'],
                'effective_date' => ['required', 'max:10'],
                'termination_date' => ['required', 'max:10', 'after:effective_date'],
                'prov_type_proc_assoc_id' => ['required', 'max:36'],
                'proc_code_list_id' => ['required'],
                'max_age' => ['nullable', 'gt:min_age'],

            ], [
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' => 'Max Age must be Greater than Min Age'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), [['Provider Type Procedure Association Already Exists']], $validation, 'false', 200, 1);
                }
                $effectiveDate = $request->effective_date;
                $terminationDate = $request->termination_date;
                $overlapExists = DB::table('PROV_TYPE_PROC_ASSOC')
                    ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                    ->where(function ($query) use ($effectiveDate, $terminationDate) {
                        $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                            ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                            ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                                $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                                    ->where('TERMINATION_DATE', '>=', $terminationDate);
                            });
                    })
                    ->exists();
                if ($overlapExists) {
                    // return redirect()->back()->withErrors(['overlap' => 'Date overlap detected.']);
                    return $this->respondWithToken($this->token(), 'For same Provider Type, Procedure Code and Service Modifier,  dates range cannot overlap.', $validation, true, 200, 1);
                }
                $add_names = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')->insert(
                    [
                        'prov_type_proc_assoc_id' => $request->prov_type_proc_assoc_id,
                        'description' => $request->description,

                    ]
                );
                $parent = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
                        ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)->first();
                if($parent) {
                    $record_snap = json_encode($parent);
                    $save_audit = $this->auditMethod('IN', $record_snap, 'PROV_TYPE_PROC_ASSOC_NAMES');
                }       

                $add = DB::table('PROV_TYPE_PROC_ASSOC')
                    ->insert(
                        [
                            'PROV_TYPE_PROC_ASSOC_ID' => $request->prov_type_proc_assoc_id,
                            'PROVIDER_TYPE' => $request->provider_type,
                            'SERVICE_MODIFIER' => $request->service_modifier,
                            'UCR' => $request->ucr,
                            'EFFECTIVE_DATE' => $request->effective_date,
                            'TERMINATION_DATE' => $request->termination_date,
                            'DATE_TIME_CREATED' => '',
                            'USER_ID_CREATED' => '',
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                            'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                            'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                            'MESSAGE' => $request->message,
                            'MESSAGE_STOP_DATE' => $request->message_stop_date,
                            'MIN_AGE' => $request->min_age,
                            'MAX_AGE' => $request->max_age,
                            'MIN_PRICE' => $request->min_price,
                            'MAX_PRICE' => $request->max_price,
                            'MIN_PRICE_OPT' => $request->min_price_opt,
                            'MAX_PRICE_OPT' => $request->max_price_opt,
                            'VALID_RELATION_CODE' => $request->valid_relation_code,
                            'SEX_RESTRICTION' => $request->sex_restriction,
                            'MODULE_EXIT' => $request->module_exit,
                            'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                            'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                            'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                            'COVERAGE_START_DAYS' => $request->coverage_start_days,
                            'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                            'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,



                        ]
                    );

                $add = DB::table('PROV_TYPE_PROC_ASSOC')
                    ->where('prov_type_proc_assoc_id', 'like', '%' . $request->prov_type_proc_assoc_id . '%')
                    ->first();

                $child = DB::table('PROV_TYPE_PROC_ASSOC')
                            ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                            ->where('provider_type', $request->provider_type)
                            ->where('service_modifier', $request->service_modifier)
                            ->where('proc_code_list_id', $request->proc_code_list_id)
                            ->where('effective_date', $request->effective_date)->first();
                if($child) {
                    $record_snap = json_encode($child);
                    $save_audit = $this->auditMethod('IN', $record_snap, 'PROV_TYPE_PROC_ASSOC');
                }    
                
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
            }
        } elseif ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                'prov_type_proc_assoc_id' => ['required', 'max:10',],
                "description" => ['required', 'max:36'],
                "provider_type" => ['required', 'max:2'],
                "service_modifier" => ['required', 'max:2'],
                'ucr' => ['nullable', 'max:10'],
                'effective_date' => ['required', 'max:10'],
                'termination_date' => ['required', 'max:10', 'after:effective_date'],
                'prov_type_proc_assoc_id' => ['required', 'max:36'],
                'proc_code_list_id' => ['required'],
                'max_age' => ['nullable', 'gt:min_age'],

            ], [
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' => 'Max Age must be Greater than Min Age'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }





                if ($request->update_new == 0) {

                    $effectiveDate = $request->effective_date;
                    $terminationDate = $request->termination_date;
                    $overlapExists = DB::table('PROV_TYPE_PROC_ASSOC')
                        ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                        ->where('provider_type', $request->provider_type)
                        ->where('service_modifier', $request->service_modifier)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('EFFECTIVE_DATE', '!=', $effectiveDate)
                        ->where(function ($query) use ($effectiveDate, $terminationDate) {
                            $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                                ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                                ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                                    $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                                        ->where('TERMINATION_DATE', '>=', $terminationDate);
                                });
                        })
                        ->exists();
                    if ($overlapExists) {
                        return $this->respondWithToken($this->token(), [['For same Provider Type, Procedure Code and Service Modifier, effective date range cannot overlap.']], '', 'false');
                    }

                    $add_names = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
                        ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                        ->update(
                            [
                                'description' => $request->description,
                            ]
                        );
                    $parent = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
                         ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)->first();
                    if($parent) {
                        $record_snap = json_encode($parent);
                        $save_audit = $this->auditMethod('UP', $record_snap, 'PROV_TYPE_PROC_ASSOC_NAMES');
                    }    

                    $update = DB::table('PROV_TYPE_PROC_ASSOC')
                        ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                        ->where('provider_type', $request->provider_type)
                        ->where('service_modifier', $request->service_modifier)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('effective_date', $request->effective_date)
                        ->update(
                            [
                                // 'TERMINATION_DATE'=>$request->termination_date,
                                'PROV_TYPE_PROC_ASSOC_ID' => $request->prov_type_proc_assoc_id,
                                'PROVIDER_TYPE' => $request->provider_type,
                                'SERVICE_MODIFIER' => $request->service_modifier,
                                'UCR' => $request->ucr,
                                'EFFECTIVE_DATE' => $request->effective_date,
                                'TERMINATION_DATE' => $request->termination_date,
                                'DATE_TIME_CREATED' => '',
                                'USER_ID_CREATED' => '',
                                'USER_ID' => '',
                                'DATE_TIME_MODIFIED' => '',
                                'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                                'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                                'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                                'MESSAGE' => $request->message,
                                'MESSAGE_STOP_DATE' => $request->message_stop_date,
                                'MIN_AGE' => $request->min_age,
                                'MAX_AGE' => $request->max_age,
                                'MIN_PRICE' => $request->min_price,
                                'MAX_PRICE' => $request->max_price,
                                'MIN_PRICE_OPT' => $request->min_price_opt,
                                'MAX_PRICE_OPT' => $request->max_price_opt,
                                'VALID_RELATION_CODE' => $request->valid_relation_code,
                                'SEX_RESTRICTION' => $request->sex_restriction,
                                'MODULE_EXIT' => $request->module_exit,
                                'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                                'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                                'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                                'COVERAGE_START_DAYS' => $request->coverage_start_days,
                                'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                                'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,

                            ]
                        );
                    $update = DB::table('PROV_TYPE_PROC_ASSOC')
                        ->where('prov_type_proc_assoc_id', 'like', '%' . $request->prov_type_proc_assoc_id . '%')
                        ->first();
                    $child = DB::table('PROV_TYPE_PROC_ASSOC')
                                ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                                ->where('provider_type', $request->provider_type)
                                ->where('service_modifier', $request->service_modifier)
                                ->where('proc_code_list_id', $request->proc_code_list_id)
                                ->where('effective_date', $request->effective_date)->first();
                    if($child) {
                        $record_snap = json_encode($child);
                        $save_audit = $this->auditMethod('UP', $record_snap, 'PROV_TYPE_PROC_ASSOC');
                    }
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                } elseif ($request->update_new == 1) {

                    $checkGPI = DB::table('PROV_TYPE_PROC_ASSOC')
                        ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                        ->where('provider_type', $request->provider_type)
                        ->where('service_modifier', $request->service_modifier)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('effective_date', $request->effective_date)
                        ->get();
                    if (count($checkGPI) >= 1) {
                        return $this->respondWithToken($this->token(), [["For same Provider Type, Procedure Code and Service Modifier, effective date range cannot overlap."]], '', 'false');
                    } else {

                        $effectiveDate = $request->effective_date;
                        $terminationDate = $request->termination_date;
                        $overlapExists = DB::table('PROV_TYPE_PROC_ASSOC')
                            ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                            ->where('provider_type', $request->provider_type)
                            ->where('service_modifier', $request->service_modifier)
                            ->where('proc_code_list_id', $request->proc_code_list_id)
                            // ->where('EFFECTIVE_DATE','!=', $effectiveDate)
                            ->where(function ($query) use ($effectiveDate, $terminationDate) {
                                $query->whereBetween('EFFECTIVE_DATE', [$effectiveDate, $terminationDate])
                                    ->orWhereBetween('TERMINATION_DATE', [$effectiveDate, $terminationDate])
                                    ->orWhere(function ($query) use ($effectiveDate, $terminationDate) {
                                        $query->where('EFFECTIVE_DATE', '<=', $effectiveDate)
                                            ->where('TERMINATION_DATE', '>=', $terminationDate);
                                    });
                            })
                            ->exists();
                        if ($overlapExists) {
                            return $this->respondWithToken($this->token(), [['For same Provider Type, Procedure Code and Service Modifier, effective date range cannot overlap.']], '', 'false');
                        }

                        $update = DB::table('PROV_TYPE_PROC_ASSOC')
                            ->insert(
                                [
                                    'PROV_TYPE_PROC_ASSOC_ID' => $request->prov_type_proc_assoc_id,
                                    'PROVIDER_TYPE' => $request->provider_type,
                                    'SERVICE_MODIFIER' => $request->service_modifier,
                                    'UCR' => $request->ucr,
                                    'EFFECTIVE_DATE' => $request->effective_date,
                                    'TERMINATION_DATE' => $request->termination_date,
                                    'DATE_TIME_CREATED' => '',
                                    'USER_ID_CREATED' => '',
                                    'USER_ID' => '',
                                    'DATE_TIME_MODIFIED' => '',
                                    'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                                    'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                                    'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                                    'MESSAGE' => $request->message,
                                    'MESSAGE_STOP_DATE' => $request->message_stop_date,
                                    'MIN_AGE' => $request->min_age,
                                    'MAX_AGE' => $request->max_age,
                                    'MIN_PRICE' => $request->min_price,
                                    'MAX_PRICE' => $request->max_price,
                                    'MIN_PRICE_OPT' => $request->min_price_opt,
                                    'MAX_PRICE_OPT' => $request->max_price_opt,
                                    'VALID_RELATION_CODE' => $request->valid_relation_code,
                                    'SEX_RESTRICTION' => $request->sex_restriction,
                                    'MODULE_EXIT' => $request->module_exit,
                                    'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                                    'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                                    'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                                    'COVERAGE_START_DAYS' => $request->coverage_start_days,
                                    'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                                    'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,



                                ]
                            );
                        $update = DB::table('PROV_TYPE_PROC_ASSOC')
                            ->where('prov_type_proc_assoc_id', 'like', '%' . $request->prov_type_proc_assoc_id . '%')
                            ->first();
                        $child = DB::table('PROV_TYPE_PROC_ASSOC')
                                    ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                                    ->where('provider_type', $request->provider_type)
                                    ->where('service_modifier', $request->service_modifier)
                                    ->where('proc_code_list_id', $request->proc_code_list_id)
                                    ->where('effective_date', $request->effective_date)->first();
                        if($child) {
                            $record_snap = json_encode($child);
                            $save_audit = $this->auditMethod('IN', $record_snap, 'PROV_TYPE_PROC_ASSOC');
                        }
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }
                }

            }
        }
    }
    public function providertype_proc_delete(Request $request)
    {
        if (isset($request->prov_type_proc_assoc_id) && isset($request->provider_type) && isset($request->service_modifier)&& isset($request->proc_code_list_id) && isset($request->effective_date)) {

            $child = DB::table('PROV_TYPE_PROC_ASSOC')
                        ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                        ->where('provider_type', $request->provider_type)
                        ->where('service_modifier', $request->service_modifier)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('effective_date', $request->effective_date)->first();
            if($child) {
                $record_snap = json_encode($child);
                $save_audit = $this->auditMethod('DE', $record_snap, 'PROV_TYPE_PROC_ASSOC');
            }
            $all_exceptions_lists =  DB::table('PROV_TYPE_PROC_ASSOC')
                                        ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                                        ->where('provider_type', $request->provider_type)
                                        ->where('service_modifier', $request->service_modifier)
                                        ->where('proc_code_list_id', $request->proc_code_list_id)
                                        ->where('effective_date', $request->effective_date)
                                        ->delete();

            $childrecords = DB::table('PROV_TYPE_PROC_ASSOC')->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)->count();                            

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully',$childrecords);
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }elseif(isset($request->prov_type_proc_assoc_id)) {

            $parent = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
                         ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)->first();
            if($parent) {
                $record_snap = json_encode($parent);
                $save_audit = $this->auditMethod('DE', $record_snap, 'PROV_TYPE_PROC_ASSOC_NAMES');
            }

            $exception_delete =  DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
                                    ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                                    ->delete();

            $childs = DB::table('PROV_TYPE_PROC_ASSOC')
                        ->where('prov_type_proc_assoc_id', $request->prov_type_proc_assoc_id)
                        ->get();
            if($childs){
                foreach($childs as $rec){
                    $record_snap = json_encode($rec);
                    $save_audit = $this->auditMethod('DE', $record_snap, 'PROV_TYPE_PROC_ASSOC');
                }
            }                        
            $all_exceptions_lists =  DB::table('PROV_TYPE_PROC_ASSOC')
                                        ->where('PROV_TYPE_PROC_ASSOC_ID', $request->prov_type_proc_assoc_id)
                                        ->delete();
            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
    
}
