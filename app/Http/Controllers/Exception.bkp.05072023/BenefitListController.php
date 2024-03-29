<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class BenefitListController extends Controller
{
    use AuditTrait;
    public function index(Request $request)
    {
        $ndc = DB::table('BENEFIT_CODES')->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function indexNew(Request $request)
    {
        $searchQuery = $request->search;
        $ndc = DB::table('BENEFIT_CODES')
        ->when($searchQuery, function ($query) use ($searchQuery) {
            $query->where(DB::raw('UPPER(BENEFIT_CODE)'), 'like', '%' . strtoupper($searchQuery) . '%');
            $query->orWhere(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($searchQuery) . '%');
         })->paginate(100);

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function BenefitLists(Request $request){

        $ndc = DB::table('BENEFIT_LIST_NAMES')->get();

        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function BenefitListsNew(Request $request){
        $searchQuery = $request->search;
        $ndc = DB::table('BENEFIT_LIST_NAMES')->when($searchQuery, function ($query) use ($searchQuery) {
            $query->where(DB::raw('UPPER(BENEFIT_LIST_ID)'), 'like', '%' . strtoupper($searchQuery) . '%');
            $query->orWhere(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($searchQuery) . '%');
         })->paginate(100);

        return $this->respondWithToken($this->token(), '', $ndc);

    }

    

    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $validation = DB::table('BENEFIT_LIST_NAMES')
            ->where('benefit_list_id', $request->benefit_list_id)
            ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'benefit_list_id' => ['required', 'max:10', Rule::unique('BENEFIT_LIST_NAMES')->where(function ($q) {
                    $q->whereNotNull('benefit_list_id');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('BENEFIT_LIST')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('BENEFIT_LIST')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'benefit_list_id' => ['required', 'max:10', Rule::unique('BENEFIT_LIST')->where(function ($q) {
                //     $q->whereNotNull('benefit_list_id');
                // })],

                "description" => ['required', 'max:20'],
                "effective_date" => ['required', 'max:10'],
                'termination_date' => ['required', 'after:effective_date'],
                // 'module_exit'=>['max:10'],
                // 'pricing_strategy_id'=>['max:10'],
                // 'accum_bene_strategy_id'=>['max:10'],
                // 'copay_strategy_id'=>['max:10'],
                // 'min_price'=>['max:11'],
                // 'max_price'=>['max:11'],
                'min_age' => ['nullable',],
                'max_age' => ['nullable', 'gt:min_age'],
                // 'coverage_start_days'=>['max:40'],
                // 'max_qty_over_time'=>['max:10'],
                // 'days_per_disability'=>['max:6'],
                // 'max_price_per_day'=>['max:6'],
                // 'max_price_per_diag'=>['nullable','max:6'],
                // 'max_base_amount'=>['nullable','max:6'],
                // 'base_apply_percent'=>['nullable','max:6'],
                // 'base_apply_percent_opt'=>['nullable','max:6'],
                // 'apply_mm_claim_max_opt'=>['nullable','max:6'],
                // 'message'=>['nullable','max:6'],
                // 'message_stop_date'=>['nullable','max:10'],
                // 'reject_only_msg_flag'=>['nullable','max:6'],
                // 'valid_relation_code'=>['nullable','max:6'],



            ], [
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' =>  'MaX Age must be greater than Min Age'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'Benefit List  Already Exists', $validation, true, 200, 1);
                }

                $effectiveDate = $request->effective_date;
                $terminationDate = $request->termination_date;
                $overlapExists = DB::table('BENEFIT_LIST')
                    ->where('BENEFIT_LIST_ID', $request->benefit_list_id)
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
                    return $this->respondWithToken($this->token(), 'For same Benefit Code , dates cannot overlap.', $validation, true, 200, 1);
                }

                $add_names = DB::table('BENEFIT_LIST_NAMES')->insert(
                    [
                        'benefit_list_id' => $request->benefit_list_id,
                        'description' => $request->description,

                    ]
                );

                $add = DB::table('BENEFIT_LIST')
                    ->insert(
                        [
                            'BENEFIT_LIST_ID' => $request->benefit_list_id,
                            'BENEFIT_CODE' => $request->benefit_code,
                            'EFFECTIVE_DATE' => $request->effective_date,
                            'TERMINATION_DATE' => $request->termination_date,
                            'DATE_TIME_CREATED' => $createddate,
                            'USER_ID_CREATED' => '',
                            'USER_ID' => '',
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
                            'DAYS_PER_DISABILITY' => $request->days_per_disability,
                            'MAX_PRICE_PER_DAY' => $request->max_price_per_day,
                            'MAX_PRICE_PER_DAY_OPT' => $request->max_price_per_day_opt,
                            'MAX_PRICE_PER_DIAG' => $request->max_price_per_diag,
                            'MAX_PRICE_PER_DIAG_OPT' => $request->max_price_per_diag_opt,
                            'MAX_PRICE_PER_DIAG_PERIOD' => $request->max_price_per_diag_period,
                            'MAX_PRICE_PER_DIAG_MULT' => $request->max_price_per_diag_mult,
                            'DAYS_PER_DISABILITY_OPT' => $request->days_per_disability_opt,
                            'BASE_APPLY_PERCENT_OPT' => $request->base_apply_percent_opt,
                            'BASE_APPLY_PERCENT' => $request->base_apply_percent,
                            'MAX_BASE_AMOUNT' => $request->max_base_amount,
                            'APPLY_MM_CLAIM_MAX_OPT' => $request->apply_mm_claim_max_opt,
                            'PRESCRIBER_EXCEPTIONS_FLAG' => $request->prescriber_exceptions_flag,
                        ]
                    );


                $add = DB::table('BENEFIT_LIST')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                $record_snap = json_encode($add);
                $save_audit = $this->auditMethod('IN', $record_snap, 'BENEFIT_LIST');
                $benefit_list_names = DB::table('BENEFIT_LIST_NAMES')
                    ->where(DB::raw('UPPER(benefit_list_id)'), strtoupper($request->benefit_list_id))
                    ->where(DB::raw('UPPER(BENEFIT_CODE)'), strtoupper($request->benefit_code))
                    ->first();
                $save_audit_list_name = $this->auditMethod('IN', json_encode($benefit_list_names), 'BENEFIT_LIST_NAMES');
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
            }
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                "benefit_list_id" => ['required', 'max:36'],
                "description" => ['required', 'max:35'],
                "effective_date" => ['required'],
                'termination_date' => ['required', 'after:effective_date'],
                // 'module_exit'=>['max:10'],
                // 'pricing_strategy_id'=>['max:10'],
                // 'accum_bene_strategy_id'=>['max:10'],
                // 'copay_strategy_id'=>['max:10'],
                // 'min_price'=>['max:11'],
                // 'max_price'=>['max:11'],
                'min_age' => ['nullable'],
                'max_age' => ['nullable', 'gt:min_age'],
                // 'coverage_start_days'=>['max:40'],
                // 'max_qty_over_time'=>['max:10'],
                // 'days_per_disability'=>['max:6'],
                // 'max_price_per_day'=>['nullable','max:6'],
                // 'max_price_per_diag'=>['nullable','max:6'],
                // 'max_base_amount'=>['nullable','max:6'],
                // 'base_apply_percent'=>['nullable','max:6'],
                // 'base_apply_percent_opt'=>['nullable','max:6'],
                // 'apply_mm_claim_max_opt'=>['nullable','max:6'],
                // 'message'=>['nullable','max:6'],
                // 'message_stop_date'=>['nullable','max:10'],
                // 'reject_only_msg_flag'=>['nullable','max:6'],
                // 'valid_relation_code'=>['nullable','max:6'],


            ], [
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date',
                'max_age.gt' =>  'MaX Age must be greater than Min Age'
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
                    $overlapExists = DB::table('BENEFIT_LIST')
                        ->where('BENEFIT_LIST_ID', $request->benefit_list_id)
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
                        return $this->respondWithToken($this->token(), 'For same Benefit Code , dates cannot overlap.', $validation, true, 200, 1);
                    }

                    $add_names = DB::table('BENEFIT_LIST_NAMES')
                        ->where('benefit_list_id', $request->benefit_list_id)
                        ->update(
                            [
                                'description' => $request->description,
                            ]
                        );

                    $update = DB::table('BENEFIT_LIST')
                        ->where('benefit_list_id', $request->benefit_list_id)
                        ->where('benefit_code', $request->benefit_code)
                        ->where('effective_date', $request->effective_date)
                        ->update(
                            [
                                'BENEFIT_CODE' => $request->benefit_code,
                                'EFFECTIVE_DATE' => $request->effective_date,
                                'TERMINATION_DATE' => $request->termination_date,
                                'DATE_TIME_CREATED' => $createddate,
                                'USER_ID_CREATED' => '',
                                'USER_ID' => '',
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
                                'DAYS_PER_DISABILITY' => $request->days_per_disability,
                                'MAX_PRICE_PER_DAY' => $request->max_price_per_day,
                                'MAX_PRICE_PER_DAY_OPT' => $request->max_price_per_day_opt,
                                'MAX_PRICE_PER_DIAG' => $request->max_price_per_diag,
                                'MAX_PRICE_PER_DIAG_OPT' => $request->max_price_per_diag_opt,
                                'MAX_PRICE_PER_DIAG_PERIOD' => $request->max_price_per_diag_period,
                                'MAX_PRICE_PER_DIAG_MULT' => $request->max_price_per_diag_mult,
                                'DAYS_PER_DISABILITY_OPT' => $request->days_per_disability_opt,
                                'BASE_APPLY_PERCENT_OPT' => $request->base_apply_percent_opt,
                                'BASE_APPLY_PERCENT' => $request->base_apply_percent,
                                'MAX_BASE_AMOUNT' => $request->max_base_amount,
                                'APPLY_MM_CLAIM_MAX_OPT' => $request->apply_mm_claim_max_opt,
                                'PRESCRIBER_EXCEPTIONS_FLAG' => $request->prescriber_exceptions_flag,


                            ]


                        );
                    $update = DB::table('BENEFIT_LIST_NAMES')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                    $save_audit = $this->auditMethod('UP', json_encode($update), 'BENEFIT_LIST_NAMES');
                    $benefit_list_names = DB::table('BENEFIT_LIST')
                        ->where(DB::raw('UPPER(benefit_list_id)'), strtoupper($request->benefit_list_id))
                        ->where(DB::raw('UPPER(BENEFIT_CODE)'), strtoupper($request->benefit_code))
                        ->first();
                    $save_audit_list_name = $this->auditMethod('UP', json_encode($benefit_list_names), 'BENEFIT_LIST');
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                } elseif ($request->update_new == 1) {
                    $checkGPI = DB::table('BENEFIT_LIST')
                        ->where('benefit_list_id', $request->benefit_list_id)
                        ->where('benefit_code', $request->benefit_code)
                        ->where('effective_date', $request->effective_date)
                        ->get();
                    if (count($checkGPI) >= 1) {
                        return $this->respondWithToken($this->token(), [['For same Benefit Code , dates cannot overlap.']], '', 'false');
                    } else {
                        $effectiveDate = $request->effective_date;
                        $terminationDate = $request->termination_date;
                        $overlapExists = DB::table('BENEFIT_LIST')
                            ->where('BENEFIT_LIST_ID', $request->benefit_list_id)
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
                        $update = DB::table('BENEFIT_LIST')
                            ->insert(
                                [
                                    'BENEFIT_LIST_ID' => $request->benefit_list_id,
                                    'BENEFIT_CODE' => $request->benefit_code,
                                    'EFFECTIVE_DATE' => $request->effective_date,
                                    'TERMINATION_DATE' => $request->termination_date,
                                    'DATE_TIME_CREATED' => $createddate,
                                    'USER_ID_CREATED' => '',
                                    'USER_ID' => '',
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
                                    'DAYS_PER_DISABILITY' => $request->days_per_disability,
                                    'MAX_PRICE_PER_DAY' => $request->max_price_per_day,
                                    'MAX_PRICE_PER_DAY_OPT' => $request->max_price_per_day_opt,
                                    'MAX_PRICE_PER_DIAG' => $request->max_price_per_diag,
                                    'MAX_PRICE_PER_DIAG_OPT' => $request->max_price_per_diag_opt,
                                    'MAX_PRICE_PER_DIAG_PERIOD' => $request->max_price_per_diag_period,
                                    'MAX_PRICE_PER_DIAG_MULT' => $request->max_price_per_diag_mult,
                                    'DAYS_PER_DISABILITY_OPT' => $request->days_per_disability_opt,
                                    'BASE_APPLY_PERCENT_OPT' => $request->base_apply_percent_opt,
                                    'BASE_APPLY_PERCENT' => $request->base_apply_percent,
                                    'MAX_BASE_AMOUNT' => $request->max_base_amount,
                                    'APPLY_MM_CLAIM_MAX_OPT' => $request->apply_mm_claim_max_opt,
                                    'PRESCRIBER_EXCEPTIONS_FLAG' => $request->prescriber_exceptions_flag,
                                ]
                            );
                        $update_record = DB::table('BENEFIT_LIST')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                        $record_snap = json_encode($update_record);
                        $save_audit = $this->auditMethod('IN', $record_snap, 'BENEFIT_LIST');
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }
                }

                // $update_names = DB::table('BENEFIT_LIST_NAMES')
                // ->where('benefit_list_id', $request->benefit_list_id )
                // ->first();


                // $checkGPI = DB::table('BENEFIT_LIST')
                //     ->where('benefit_list_id', $request->benefit_list_id)
                //     ->where('benefit_code',$request->benefit_code)
                //     ->where('effective_date',$request->effective_date)
                //     ->get()
                //     ->count();
                //     // dd($checkGPI);
                // // if result >=1 then update BENEFIT_LIST table record
                // //if result 0 then add BENEFIT_LIST record

                // if ($checkGPI <= "0") {
                //     $update = DB::table('BENEFIT_LIST')
                //     ->insert(
                //         [
                //             'BENEFIT_LIST_ID'=>$request->benefit_list_id,
                //             'BENEFIT_CODE'=>$request->benefit_code,
                //             'EFFECTIVE_DATE'=>$request->effective_date,
                //             'TERMINATION_DATE'=>$request->termination_date,
                //             'DATE_TIME_CREATED'=>$createddate,
                //             'USER_ID_CREATED'=>'',
                //             'USER_ID'=>'',
                //             'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                //             'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                //             'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                //             'MESSAGE'=>$request->message,
                //             'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                //             'MIN_AGE'=>$request->min_age,
                //             'MAX_AGE'=>$request->max_age,
                //             'MIN_PRICE'=>$request->min_price,
                //             'MAX_PRICE'=>$request->max_price,
                //             'MIN_PRICE_OPT'=>$request->max_price_opt,
                //             'MAX_PRICE_OPT'=>$request->max_price_opt,
                //             'VALID_RELATION_CODE'=>$request->valid_relation_code,
                //             'SEX_RESTRICTION'=>$request->sex_restriction,
                //             'MODULE_EXIT'=>$request->module_exit,
                //             'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                //             'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                //             'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                //             'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                //             'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                //             'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                //             'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                //             'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                //             'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                //             'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                //             'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                //             'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                //             'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                //             'BASE_APPLY_PERCENT_OPT'=>$request->base_apply_percent_opt,
                //             'BASE_APPLY_PERCENT'=>$request->base_apply_percent,
                //             'MAX_BASE_AMOUNT'=>$request->max_base_amount,
                //             'APPLY_MM_CLAIM_MAX_OPT'=>$request->apply_mm_claim_max_opt,
                //             'PRESCRIBER_EXCEPTIONS_FLAG'=>$request->prescriber_exceptions_flag,


                //         ]);


                // $update = DB::table('BENEFIT_LIST')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                // return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                // } else {

                //     $add_names = DB::table('BENEFIT_LIST_NAMES')
                //     ->where('benefit_list_id',$request->benefit_list_id)
                //     ->update(
                //         [
                //             'description'=>$request->description,
                //         ]
                //     );

                //     $update = DB::table('BENEFIT_LIST' )
                //     ->where('benefit_list_id', $request->benefit_list_id)
                //     ->where('benefit_code',$request->benefit_code)
                //     ->where('effective_date',$request->effective_date)  
                //     ->update(
                //         [
                //             'BENEFIT_CODE'=>$request->benefit_code,
                //             'EFFECTIVE_DATE'=>$request->effective_date,
                //             'TERMINATION_DATE'=>$request->termination_date,
                //             'DATE_TIME_CREATED'=>$createddate,
                //             'USER_ID_CREATED'=>'',
                //             'USER_ID'=>'',
                //             'PRICING_STRATEGY_ID'=>$request->pricing_strategy_id,
                //             'ACCUM_BENE_STRATEGY_ID'=>$request->accum_bene_strategy_id,
                //             'COPAY_STRATEGY_ID'=>$request->copay_strategy_id,
                //             'MESSAGE'=>$request->message,
                //             'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                //             'MIN_AGE'=>$request->min_age,
                //             'MAX_AGE'=>$request->max_age,
                //             'MIN_PRICE'=>$request->min_price,
                //             'MAX_PRICE'=>$request->max_price,
                //             'MIN_PRICE_OPT'=>$request->max_price_opt,
                //             'MAX_PRICE_OPT'=>$request->max_price_opt,
                //             'VALID_RELATION_CODE'=>$request->valid_relation_code,
                //             'SEX_RESTRICTION'=>$request->sex_restriction,
                //             'MODULE_EXIT'=>$request->module_exit,
                //             'REJECT_ONLY_MSG_FLAG'=>$request->reject_only_msg_flag,
                //             'MAX_QTY_OVER_TIME'=>$request->max_qty_over_time,
                //             'MAX_RX_QTY_OPT'=>$request->max_rx_qty_opt,
                //             'COVERAGE_START_DAYS'=>$request->coverage_start_days,
                //             'RX_QTY_OPT_MULTIPLIER'=>$request->rx_qty_opt_multiplier,
                //             'DAYS_PER_DISABILITY'=>$request->days_per_disability,
                //             'MAX_PRICE_PER_DAY'=>$request->max_price_per_day,
                //             'MAX_PRICE_PER_DAY_OPT'=>$request->max_price_per_day_opt,
                //             'MAX_PRICE_PER_DIAG'=>$request->max_price_per_diag,
                //             'MAX_PRICE_PER_DIAG_OPT'=>$request->max_price_per_diag_opt,
                //             'MAX_PRICE_PER_DIAG_PERIOD'=>$request->max_price_per_diag_period,
                //             'MAX_PRICE_PER_DIAG_MULT'=>$request->max_price_per_diag_mult,
                //             'DAYS_PER_DISABILITY_OPT'=>$request->days_per_disability_opt,
                //             'BASE_APPLY_PERCENT_OPT'=>$request->base_apply_percent_opt,
                //             'BASE_APPLY_PERCENT'=>$request->base_apply_percent,
                //             'MAX_BASE_AMOUNT'=>$request->max_base_amount,
                //             'APPLY_MM_CLAIM_MAX_OPT'=>$request->apply_mm_claim_max_opt,
                //             'PRESCRIBER_EXCEPTIONS_FLAG'=>$request->prescriber_exceptions_flag,


                //         ]


                //     );
                //     $update = DB::table('BENEFIT_LIST')->where('benefit_list_id', 'like', '%' . $request->benefit_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                // }

            }
        }
    }



    public function search(Request $request)
    {
        $ndc = DB::table('BENEFIT_LIST_NAMES')
            ->where(DB::raw('UPPER(BENEFIT_LIST_ID)'), 'like', '%' . $request->search . '%')
            // ->whereRaw('LOWER(BENEFIT_LIST_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getBLList($ndcid)
    {
        $ndclist = DB::table('BENEFIT_LIST')
            ->join('BENEFIT_LIST_NAMES', 'BENEFIT_LIST_NAMES.BENEFIT_LIST_ID', '=', 'BENEFIT_LIST.BENEFIT_LIST_ID')

            // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
            ->where('BENEFIT_LIST.BENEFIT_LIST_ID', $ndcid)
            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getBLItemDetails(Request $request)
    {
        $ndc = DB::table('BENEFIT_LIST')
        ->select('BENEFIT_LIST.*',
        'NAMES.DESCRIPTION',
        'BENEFIT_LIST.PRICING_STRATEGY_ID as pricing_strategy_description',
        'ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_NAME as accum_starategy_description',
        'CODES.DESCRIPTION AS benefit_code_description',
        'COPAY_STRATEGY_NAMES.COPAY_STRATEGY_NAME as copay_starategy_description')
        
        ->leftjoin('BENEFIT_LIST_NAMES AS NAMES', 'NAMES.BENEFIT_LIST_ID', '=', 'BENEFIT_LIST.BENEFIT_LIST_ID')
        ->leftjoin('BENEFIT_CODES AS CODES','CODES.BENEFIT_CODE','=','BENEFIT_LIST.BENEFIT_CODE')
        ->leftjoin('PRICING_STRATEGY as pricing_strategies','pricing_strategies.PRICING_STRATEGY_ID','=','BENEFIT_LIST.PRICING_STRATEGY_ID')
        ->leftjoin('ACCUM_BENE_STRATEGY_NAMES','ACCUM_BENE_STRATEGY_NAMES.ACCUM_BENE_STRATEGY_ID','=','BENEFIT_LIST.ACCUM_BENE_STRATEGY_ID')
        ->leftjoin('COPAY_STRATEGY_NAMES','COPAY_STRATEGY_NAMES.COPAY_STRATEGY_ID','=','BENEFIT_LIST.COPAY_STRATEGY_ID')


        ->where('BENEFIT_LIST.BENEFIT_LIST_ID',$request->benefit_list_id)
        ->where('BENEFIT_LIST.BENEFIT_CODE',$request->benefit_code)
        ->where('BENEFIT_LIST.EFFECTIVE_DATE',$request->effective_date)

        // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
        ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
    public function benefit_list_delete(Request $request)
    {
       
        if (isset($request->benefit_list_id) && ($request->benefit_code) && isset($request->effective_date)) {
            $all_exceptions_lists =  DB::table('BENEFIT_LIST')
                                        ->where('BENEFIT_LIST_ID', $request->benefit_list_id)
                                        ->where('benefit_code',$request->benefit_code)
                                        ->where('effective_date',$request->effective_date)
                                        ->delete();

            $childcount =  DB::table('BENEFIT_LIST')->where('BENEFIT_LIST_ID', $request->benefit_list_id)->count();
            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully',$childcount);
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } elseif(isset($request->benefit_list_id)) {
            // return $request->benefit_list_id;

            $exception_delete =  DB::table('BENEFIT_LIST_NAMES')
                                    ->where('BENEFIT_LIST_ID', $request->benefit_list_id)
                                    ->delete();
            $all_exceptions_lists =  DB::table('BENEFIT_LIST')
                                    ->where('BENEFIT_LIST_ID', $request->benefit_list_id)
                                    ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
    
}
