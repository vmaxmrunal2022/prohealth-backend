<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BenefitDerivationController extends Controller
{
use AuditTrait;
    public function addcopy(Request $request)
    {

        $createddate = date('y-m-d');
        // $effective_date = date( 'Ymd', strtotime( $request->effective_date ) );
        // $terminate_date = date( 'Ymd', strtotime( $request->termination_date ) );


        $recordcheck = DB::table('BENEFIT_DERIVATION')
            ->where('BENEFIT_DERIVATION_ID', strtoupper($request->benefit_derivation_id))
            ->first();


        if ($request->has('new')) {

            if ($recordcheck) {

                return $this->respondWithToken($this->token(), 'Benefit Derivation ID Already Exists', $recordcheck);
            }

            $accum_benfit_stat_names = DB::table('BENEFIT_DERIVATION_NAMES')->insert(
                [
                    'BENEFIT_DERIVATION_ID' => strtoupper($request->benefit_derivation_id),
                    'DESCRIPTION' => $request->description,
                    'DATE_TIME_CREATED' => $createddate

                ]
            );

            $accum_benfit_stat = DB::table('BENEFIT_DERIVATION')->insert(
                [
                    'BENEFIT_DERIVATION_ID' => strtoupper($request->benefit_derivation_id),
                    'SERVICE_TYPE' => $request->service_type,
                    'SERVICE_MODIFIER' => $request->service_modifier,
                    'BENEFIT_CODE' => $request->benefit_code,
                    'EFFECTIVE_DATE' => $request->effective_date,
                    'TERMINATION_DATE' => $request->termination_date,
                    'DATE_TIME_CREATED' => $createddate,
                    'PROC_CODE_LIST_ID' => $request->proc_code_list_id,

                ]
            );
            $benefitcode = DB::table('BENEFIT_DERIVATION')
                ->where(DB::raw('UPPER(benefit_derivation_id)'), strtoupper($request->benefit_derivation_id))
                ->first();

            return $this->respondWithToken($this->token(), 'Record Added Successfully', $accum_benfit_stat);



        } else {

            $benefitcode = DB::table('BENEFIT_DERIVATION_NAMES')
                ->where('benefit_derivation_id', $request->benefit_derivation_id)

                ->update(
                    [
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => $createddate
                    ]
                );

            $accum_benfit_stat = DB::table('BENEFIT_DERIVATION')
                ->where('benefit_derivation_id', $request->benefit_derivation_id)
                ->where('benefit_code', $request->benefit_code)
                ->where('service_type', $request->service_type)


                ->update(
                    [
                        'effective_date' => $request->effective_date,
                        'termination_date' => $request->termination_date,


                    ]
                );

            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $accum_benfit_stat);

            $benefitcode = DB::table('BENEFIT_DERIVATION')
                ->where(DB::raw('UPPER(benefit_derivation_id)'), strtoupper($request->benefit_derivation_id))
                ->first();

            $record_sanp = json_encode($benefitcode);
            $save_audit = $this->auditMethod('IN', $record_sanp, 'BENEFIT_DERIVATION');

            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $accum_benfit_stat);
        }
    }


    public function add(Request $request)
    {




        $createddate = date('y-m-d');

        $validation = DB::table('BENEFIT_DERIVATION_NAMES')
            ->where('benefit_derivation_id', $request->benefit_derivation_id)
            ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'benefit_derivation_id' => [
                    'required',
                    'max:10', Rule::unique('BENEFIT_DERIVATION_NAMES')->where(function ($q) {
                        $q->whereNotNull('benefit_derivation_id');
                    })
                ],
                // 'ndc' => ['required', 'max:11', Rule::unique('BENEFIT_DERIVATION')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('BENEFIT_DERIVATION')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                'benefit_derivation_id' => [
                    'required',
                    'max:10', Rule::unique('BENEFIT_DERIVATION')->where(function ($q) {
                        $q->whereNotNull('benefit_derivation_id');
                    })
                ],

                // "benefit_derivation_id" => ['required','max:36'],
                "description" => ['required', 'max:35'],
                "service_type" => ['required'],
                'service_modifier' => ['required'],
                'proc_code_list_id' => ['required'],
                // 'benefit_code'=>['max:10'],
                'effective_date' => ['required'],
                'termination_date' => ['required', 'after:effective_date'],
            ], [
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Benefit List  Already Exists', $validation, true, 200, 1);
                }

                $effectiveDate = $request->effective_date;
                $terminationDate = $request->termination_date;
                $overlapExists = DB::table('BENEFIT_DERIVATION')
                    ->where('BENEFIT_DERIVATION_ID', $request->benefit_derivation_id)
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

                    return $this->respondWithToken($this->token(), 'For same Benefit Code , dates cannot overlap.', $validation, true, 200, 1);
                }

                $add_names = DB::table('BENEFIT_DERIVATION_NAMES')->insert(
                    [
                        'benefit_derivation_id' => $request->benefit_derivation_id,
                        'description' => $request->description,

                    ]
                );

                $add = DB::table('BENEFIT_DERIVATION')
                    ->insert(
                        [
                            'BENEFIT_DERIVATION_ID' => $request->benefit_derivation_id,
                            'SERVICE_TYPE' => $request->service_type,
                            'SERVICE_MODIFIER' => $request->service_modifier,
                            'BENEFIT_CODE' => $request->benefit_code,
                            'EFFECTIVE_DATE' => $request->effective_date,
                            'TERMINATION_DATE' => $request->termination_date,
                            'DATE_TIME_CREATED' => $createddate,
                            'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                            'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                            'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                            'MESSAGE' => $request->message,
                            'MESSAGE_STOP_DATE' => $request->message_stop_date,
                            'MIN_AGE' => $request->min_age,
                            'MAX_AGE' => $request->max_age,
                            'MIN_PRICE' => $request->min_price,
                            'MAX_PRICE' => $request->max_price,
                            'MIN_PRICE_OPT' => $request->max_price_opt,
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


                $add = DB::table('BENEFIT_DERIVATION')->where('benefit_derivation_id', 'like', '%' . $request->benefit_derivation_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
            }
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                "benefit_derivation_id" => ['required', 'max:36'],
                "description" => ['required', 'max:35'],
                "service_type" => ['required'],
                'service_modifier' => ['required'],
                'proc_code_list_id' => ['required'],
                // 'benefit_code'=>['max:10'],
                'effective_date' => ['required'],
                'termination_date' => ['required', 'after:effective_date'],


            ], [
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
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
                    $overlapExists = DB::table('BENEFIT_DERIVATION')
                        ->where('BENEFIT_DERIVATION_ID', $request->benefit_derivation_id)
                        // ->where('service_type',$request->service_type)
                        // ->where('service_modifier',$request->service_modifier)
                        // ->where('proc_code_list_id',$request->proc_code_list_id)
                        ->where('benefit_code', $request->benefit_code)
                        ->where('effective_date', '!=', $request->effective_date)
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
                        return $this->respondWithToken($this->token(), [['For same Benefit Code , dates cannot overlap.']], '', 'false');
                    }



                    $add_names = DB::table('BENEFIT_DERIVATION_NAMES')
                        ->where('benefit_derivation_id', $request->benefit_derivation_id)
                        ->update(
                            [
                                'description' => $request->description,

                            ]
                        );

                    $update = DB::table('BENEFIT_DERIVATION')
                        ->where('benefit_derivation_id', $request->benefit_derivation_id)
                        ->where('service_type', $request->service_type)
                        ->where('service_modifier', $request->service_modifier)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('benefit_code', $request->benefit_code)
                        ->where('effective_date', $request->effective_date)
                        ->update(
                            [

                                'TERMINATION_DATE' => $request->termination_date,
                                'DATE_TIME_CREATED' => $createddate,
                                'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                                'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                                'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                                'MESSAGE' => $request->message,
                                'MESSAGE_STOP_DATE' => $request->message_stop_date,
                                'MIN_AGE' => $request->min_age,
                                'MAX_AGE' => $request->max_age,
                                'MIN_PRICE' => $request->min_price,
                                'MAX_PRICE' => $request->max_price,
                                'MIN_PRICE_OPT' => $request->max_price_opt,
                                'MAX_PRICE_OPT' => $request->max_price_opt,
                                'VALID_RELATION_CODE' => $request->valid_relation_code,
                                'SEX_RESTRICTION' => $request->sex_restriction,
                                'MODULE_EXIT' => $request->module_exit,
                                'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                                'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                                'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                                'COVERAGE_START_DAYS' => $request->coverage_start_days,
                                'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,

                            ]


                        );
                    $update = DB::table('BENEFIT_DERIVATION')->where('benefit_derivation_id', 'like', '%' . $request->benefit_derivation_id . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                } elseif ($request->update_new == 1) {
                    $checkGPI = DB::table('BENEFIT_DERIVATION')
                        ->where('benefit_derivation_id', $request->benefit_derivation_id)
                        ->where('service_type', $request->service_type)
                        ->where('service_modifier', $request->service_modifier)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('benefit_code', $request->benefit_code)
                        ->where('effective_date', $request->effective_date)
                        ->get();

                    if (count($checkGPI) >= 1) {
                        return $this->respondWithToken($this->token(), [['For same Benefit Code , dates cannot overlap.']], '', 'false');
                    } else {
                        $effectiveDate = $request->effective_date;
                        $terminationDate = $request->termination_date;
                        $overlapExists = DB::table('BENEFIT_DERIVATION')
                            ->where('BENEFIT_DERIVATION_ID', $request->benefit_derivation_id)
                            // ->where('service_type',$request->service_type)
                            // ->where('service_modifier',$request->service_modifier)
                            // ->where('proc_code_list_id',$request->proc_code_list_id)
                            ->where('benefit_code', $request->benefit_code)
                            // ->where('effective_date','!=',$request->effective_date)
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
                            return $this->respondWithToken($this->token(), [['For same Benefit Code , dates cannot overlap.']], '', 'false');
                        }


                        $update = DB::table('BENEFIT_DERIVATION')
                            ->insert(
                                [
                                    'BENEFIT_DERIVATION_ID' => $request->benefit_derivation_id,
                                    'SERVICE_TYPE' => $request->service_type,
                                    'SERVICE_MODIFIER' => $request->service_modifier,
                                    'BENEFIT_CODE' => $request->benefit_code,
                                    'EFFECTIVE_DATE' => $request->effective_date,
                                    'TERMINATION_DATE' => $request->termination_date,
                                    'DATE_TIME_CREATED' => $createddate,
                                    'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                                    'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                                    'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                                    'MESSAGE' => $request->message,
                                    'MESSAGE_STOP_DATE' => $request->message_stop_date,
                                    'MIN_AGE' => $request->min_age,
                                    'MAX_AGE' => $request->max_age,
                                    'MIN_PRICE' => $request->min_price,
                                    'MAX_PRICE' => $request->max_price,
                                    'MIN_PRICE_OPT' => $request->max_price_opt,
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


                        $update = DB::table('BENEFIT_DERIVATION')->where('benefit_derivation_id', 'like', '%' . $request->benefit_derivation_id . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }
                }



                // $update_names = DB::table('BENEFIT_DERIVATION_NAMES')
                //     ->where('benefit_derivation_id', $request->benefit_derivation_id)
                //     ->first();


                // $checkGPI = DB::table('BENEFIT_DERIVATION')
                //     ->where('benefit_derivation_id', $request->benefit_derivation_id)
                //     ->where('service_type',$request->service_type)
                //     ->where('service_modifier',$request->service_modifier)
                //     ->where('proc_code_list_id',$request->proc_code_list_id)
                //     ->where('benefit_code', $request->benefit_code)
                //     ->where('effective_date', $request->effective_date)
                //     ->get()
                //     ->count();
                // dd($checkGPI);
                // if result >=1 then update BENEFIT_DERIVATION table record
                //if result 0 then add BENEFIT_DERIVATION record


                // if ($checkGPI <= "0") {
                //     $update = DB::table('BENEFIT_DERIVATION')
                //         ->insert(
                //             [
                //                 'BENEFIT_DERIVATION_ID' => $request->benefit_derivation_id,
                //                 'SERVICE_TYPE' => $request->service_type,
                //                 'SERVICE_MODIFIER' => $request->service_modifier,
                //                 'BENEFIT_CODE' => $request->benefit_code,
                //                 'EFFECTIVE_DATE' => $request->effective_date,
                //                 'TERMINATION_DATE' => $request->termination_date,
                //                 'DATE_TIME_CREATED' => $createddate,
                //                 'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                //                 'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                //                 'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                //                 'MESSAGE' => $request->message,
                //                 'MESSAGE_STOP_DATE' => $request->message_stop_date,
                //                 'MIN_AGE' => $request->min_age,
                //                 'MAX_AGE' => $request->max_age,
                //                 'MIN_PRICE' => $request->min_price,
                //                 'MAX_PRICE' => $request->max_price,
                //                 'MIN_PRICE_OPT' => $request->max_price_opt,
                //                 'MAX_PRICE_OPT' => $request->max_price_opt,
                //                 'VALID_RELATION_CODE' => $request->valid_relation_code,
                //                 'SEX_RESTRICTION' => $request->sex_restriction,
                //                 'MODULE_EXIT' => $request->module_exit,
                //                 'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                //                 'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                //                 'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                //                 'COVERAGE_START_DAYS' => $request->coverage_start_days,
                //                 'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                //                 'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,

                //             ]
                //         );


                //     $update = DB::table('BENEFIT_DERIVATION')->where('benefit_derivation_id', 'like', '%' . $request->benefit_derivation_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                // } else {


                //     $add_names = DB::table('BENEFIT_DERIVATION_NAMES')
                //         ->where('benefit_derivation_id', $request->benefit_derivation_id)
                //         ->update(
                //             [
                //                 'description' => $request->description,

                //             ]
                //         );

                //     $update = DB::table('BENEFIT_DERIVATION')
                //     ->where('benefit_derivation_id', $request->benefit_derivation_id)
                //     ->where('service_type',$request->service_type)
                //     ->where('service_modifier',$request->service_modifier)
                //     ->where('proc_code_list_id',$request->proc_code_list_id)
                //     ->where('benefit_code', $request->benefit_code)
                //     ->where('effective_date', $request->effective_date)

                //         ->update(
                //             [
                //             'TERMINATION_DATE' => $request->termination_date,
                //             'DATE_TIME_CREATED' => $createddate,
                //             'PRICING_STRATEGY_ID' => $request->pricing_strategy_id,
                //             'ACCUM_BENE_STRATEGY_ID' => $request->accum_bene_strategy_id,
                //             'COPAY_STRATEGY_ID' => $request->copay_strategy_id,
                //             'MESSAGE' => $request->message,
                //             'MESSAGE_STOP_DATE' => $request->message_stop_date,
                //             'MIN_AGE' => $request->min_age,
                //             'MAX_AGE' => $request->max_age,
                //             'MIN_PRICE' => $request->min_price,
                //             'MAX_PRICE' => $request->max_price,
                //             'MIN_PRICE_OPT' => $request->max_price_opt,
                //             'MAX_PRICE_OPT' => $request->max_price_opt,
                //             'VALID_RELATION_CODE' => $request->valid_relation_code,
                //             'SEX_RESTRICTION' => $request->sex_restriction,
                //             'MODULE_EXIT' => $request->module_exit,
                //             'REJECT_ONLY_MSG_FLAG' => $request->reject_only_msg_flag,
                //             'MAX_QTY_OVER_TIME' => $request->max_qty_over_time,
                //             'MAX_RX_QTY_OPT' => $request->max_rx_qty_opt,
                //             'COVERAGE_START_DAYS' => $request->coverage_start_days,
                //             'RX_QTY_OPT_MULTIPLIER' => $request->rx_qty_opt_multiplier,

                //             ]


                //         );
                //     $update = DB::table('BENEFIT_DERIVATION')->where('benefit_derivation_id', 'like', '%' . $request->benefit_derivation_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                // }



            }
        }
    }

    public function getAll(Request $request)
    {


        $data = DB::table('BENEFIT_DERIVATION_NAMES')
            ->where('BENEFIT_DERIVATION_ID', 'LIKE', '%' . strtoupper($request->benefit_derivation_id) . '%')
            ->get();

        return $this->respondWithToken($this->token(), 'data fetched Successfully', $data);

        return $this->respondWithToken($this->token(), 'data fetched Successfully', $data);
    }

    public function search(Request $request)
    {
        $ndc = DB::table('BENEFIT_DERIVATION_NAMES')
            ->select('BENEFIT_DERIVATION_ID', 'DESCRIPTION')
            ->whereRaw('LOWER(BENEFIT_DERIVATION_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            // ->where('BENEFIT_DERIVATION_ID', 'like', '%' . $request->search. '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getBLList($ndcid)
    {
        $ndclist = DB::table('BENEFIT_DERIVATION')
            ->join('BENEFIT_DERIVATION_NAMES', 'BENEFIT_DERIVATION_NAMES.BENEFIT_DERIVATION_ID', '=', 'BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID')
            // ->join('PROC_CODE_LIST_NAMES', 'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID', '=', 'BENEFIT_DERIVATION.PROC_CODE_LIST_ID')
            // ->select(
            //     'BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID',
            //     'BENEFIT_DERIVATION.SERVICE_TYPE',
            //     'BENEFIT_DERIVATION_NAMES.DESCRIPTION',
            //     'BENEFIT_DERIVATION.*'
            // )

            ->where('BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID', $ndcid)

            // ->orWhere( 'EXCEPTION_NAME', 'like', '%' . strtoupper( $ndcid ) . '%' )
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getBLItemDetails(Request $request)
    {
        // return 'all';
    //    return  $request->all();
        $ndclist = DB::table('BENEFIT_DERIVATION')
            ->leftjoin('BENEFIT_DERIVATION_NAMES as benefitnames', 'benefitnames.BENEFIT_DERIVATION_ID', '=', 'BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID')
            ->leftjoin('SERVICE_TYPES', 'SERVICE_TYPES.SERVICE_TYPE', '=', 'BENEFIT_DERIVATION.SERVICE_TYPE')
            ->leftjoin('SERVICE_MODIFIERS', 'SERVICE_MODIFIERS.SERVICE_MODIFIER', '=', 'BENEFIT_DERIVATION.SERVICE_MODIFIER')
            ->leftjoin('PROC_CODE_LIST_NAMES', 'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID', '=', 'BENEFIT_DERIVATION.PROC_CODE_LIST_ID')
            ->leftjoin('BENEFIT_CODES', 'BENEFIT_CODES.BENEFIT_CODE', '=', 'BENEFIT_DERIVATION.BENEFIT_CODE')
            ->select(
                'BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID',
                'BENEFIT_DERIVATION.SERVICE_TYPE',
                'BENEFIT_DERIVATION.SERVICE_MODIFIER',
                'BENEFIT_DERIVATION.BENEFIT_CODE',
                'BENEFIT_DERIVATION.EFFECTIVE_DATE',
                'BENEFIT_DERIVATION.TERMINATION_DATE',
                'SERVICE_TYPES.DESCRIPTION as service_type_description',
                'SERVICE_MODIFIERS.DESCRIPTION as service_modifier_description',
                'PROC_CODE_LIST_NAMES.PROC_CODE_LIST_ID as proc_code_list_id',
                'PROC_CODE_LIST_NAMES.DESCRIPTION as procedure_code_description',
                'BENEFIT_CODES.DESCRIPTION as benefit_code_description',
                'benefitnames.description as description'
            )

            ->where('BENEFIT_DERIVATION.BENEFIT_DERIVATION_ID', $request->ben_deriv_id)
            ->where('BENEFIT_DERIVATION.BENEFIT_CODE', $request->ben_code)
            ->where('BENEFIT_DERIVATION.EFFECTIVE_DATE', $request->effective_date)

            // ->orWhere( 'EXCEPTION_NAME', 'like', '%' . strtoupper( $ndcid ) . '%' )
            ->first();

        return $this->respondWithToken($this->token(), '', $ndclist);


    }
    public function benefitderivationdelete(Request $request)
    {
        if (isset($request->benefit_derivation_id) && isset($request->proc_code_list_id) && isset($request->service_type) && isset($request->service_modifier) && isset($request->benefit_code) && isset($request->effective_date)) {
          
            $all_exceptions_lists =  DB::table('BENEFIT_DERIVATION')
                                        ->where('BENEFIT_DERIVATION_ID', $request->benefit_derivation_id)
                                        ->where('service_type',$request->service_type)
                                        ->where('service_modifier',$request->service_modifier)
                                        ->where('proc_code_list_id',$request->proc_code_list_id)
                                        ->where('benefit_code', $request->benefit_code)
                                        ->where('effective_date', $request->effective_date)
                                        ->delete();
            $childcount = DB::table('BENEFIT_DERIVATION')->where('BENEFIT_DERIVATION_ID', $request->benefit_derivation_id)->count(); 
            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully',$childcount);
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } elseif(isset($request->benefit_derivation_id)) {

            $exception_delete =  DB::table('BENEFIT_DERIVATION_NAMES')
                ->where('BENEFIT_DERIVATION_ID', $request->benefit_derivation_id)
                ->delete();

            $all_exceptions_lists =  DB::table('BENEFIT_DERIVATION')
                                    ->where('BENEFIT_DERIVATION_ID', $request->benefit_derivation_id)
                                    ->delete();    

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
    
}