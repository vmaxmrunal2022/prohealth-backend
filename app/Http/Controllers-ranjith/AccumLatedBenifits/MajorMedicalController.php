<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MajorMedicalController extends Controller
{



    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "customer_id" => ['required'],
            "client_id" => ['required'],
            "client_group_id" => ['required'],
            'mm_claim_max' => ['max:10'],
            'mm_life_maximum' => ['max:10'],
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        }
        if ($request->has('new')) {
            $insert = DB::table('MM_LIFE_MAX')->insert(
                [
                    'CUSTOMER_ID' => $request->customer_id,
                    'CLIENT_ID' => $request->client_id,
                    'CLIENT_GROUP_ID' => $request->client_group_id,
                    'MM_LIFE_MAXIMUM' => $request->mm_life_maximum,
                    'EFFECTIVE_DATE' => $request->effective_date,
                    'TERMINATION_DATE' => $request->termination_date,
                    'GROUPING_TYPE' => $request->grouping_type,
                    'USER_ID_CREATED'=>Cache::get('userId'),
                    'DATE_TIME_CREATED'=>date('Ymd'),
                    'USER_ID_MODIFIED'=>date('Ymd'),
                    'MM_CLAIM_MAX'=>$request->mm_claim_max,
                    'MM_CLAIM_MAX_PERIOD'=>$request->mm_claim_max_period,
                    'MM_CLAIM_MAX_MULT'=>$request->mm_claim_max_mult,
                    'MM_CLAIM_MAX_GROUP_TYPE'=>$request->mm_claim_max_group_type,
                 
                ]
            );
            if ($insert) {
                return $this->respondWithToken($this->token(), 'Recored Added Successfully', $insert);
            }
        } else {
            $effectiveDate=$request->effective_date;
            $terminationDate=$request->termination_date;
            $overlapExists = DB::table('MM_LIFE_MAX')
            ->where('customer_id', $request->customer_id)
            ->where('client_id',$request->client_id)
            ->where('client_group_id',$request->client_group_id)
            ->where('effective_date','!=',$request->effective_date)
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
                return $this->respondWithToken($this->token(), [['For same Client Id , Customer Id , Client Group ID , dates cannot overlap.']], '', 'false');
            }
            $update = DB::table('MM_LIFE_MAX')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->where('effective_date',$request->effective_date)
                ->update(
                    [
                        'MM_LIFE_MAXIMUM' => $request->mm_life_maximum,
                        'TERMINATION_DATE' => $request->termination_date,
                        'GROUPING_TYPE' => $request->grouping_type,
                        'USER_ID_CREATED'=>Cache::get('userId'),
                        'DATE_TIME_CREATED'=>date('Ymd'),
                        'USER_ID_MODIFIED'=>date('Ymd'),
                        'MM_CLAIM_MAX'=>$request->mm_claim_max,
                        'MM_CLAIM_MAX_PERIOD'=>$request->mm_claim_max_period,
                        'MM_CLAIM_MAX_MULT'=>$request->mm_claim_max_mult,
                        'MM_CLAIM_MAX_GROUP_TYPE'=>$request->mm_claim_max_group_type,
                     
                    ]
                );

            $recordcheck = DB::table('MM_LIFE_MAX')
                ->where('customer_id',$request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->first();


            // dd($request->all());

            if ($request->new == 1) {
                if ($recordcheck) {
                    return $this->respondWithToken($this->token(), 'Record already exists in the system..!!!', $recordcheck, false);
                } else {
                    $effectiveDate=$request->effective_date;
                    $terminationDate=$request->termination_date;
                    $overlapExists = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', $request->customer_id)
                    ->where('client_id',$request->client_id)
                    ->where('client_group_id',$request->client_group_id)
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
                        return $this->respondWithToken($this->token(), [['For same Client Id , Customer Id , Client Group ID , dates cannot overlap.']], '', 'false');
                    }
                    $insert = DB::table('MM_LIFE_MAX')->insert(
                        [
                            'customer_id' => $request->customer_id,
                            'client_id' => $request->client_id,
                            'client_group_id' => $request->client_group_id,
                            'mm_life_maximum' => $request->mm_life_maximum,
                            'grouping_type' => $request->grouping_type,
                            'mm_claim_max' => $request->mm_claim_max,
                            'effective_date' => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'mm_claim_max_group_type' =>$request->mm_claim_max_group_type,

                        ]
                    );
                    // $benefitcode = DB::table('TEMP_MM_LIFE_MAX')->where('customer_id',$request->customer_id)->first();    
                    return $this->respondWithToken($this->token(), 'Record added Successfully', $insert);
                }
            } else {
                $effectiveDate=$request->effective_date;
                $terminationDate=$request->termination_date;
                $overlapExists = DB::table('MM_LIFE_MAX')
                ->where('customer_id', $request->customer_id)
                ->where('client_id',$request->client_id)
                ->where('client_group_id',$request->client_group_id)
                ->where('effective_date','!=',$request->effective_date)
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
                    return $this->respondWithToken($this->token(), [['For same Client Id , Customer Id , Client Group ID , dates cannot overlap.']], '', 'false');
                }

                $update = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', $request->customer_id)
                    ->where('client_id',$request->client_id)
                    ->where('client_group_id',$request->client_group_id)
                    ->where('effective_date',$request->effective_date)

                    ->update(
                        [
                            'mm_life_maximum' => $request->mm_life_maximum,
                            'grouping_type' => $request->grouping_type,
                            'mm_claim_max' => $request->mm_claim_max,
                            'effective_date' => $request->effective_date,
                            'termination_date' => $request->termination_date,
                            'mm_claim_max_group_type' => $request->mm_claim_max_group_type,
                        ]
                    );
                $benefitcode = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', $request->customer_id)
                    ->where('client_id', $request->client_id)
                    ->where('client_group_id', $request->client_group_id)
                    ->first();

                // $benefitcode = DB::table('MM_LIFE_MAX')->where('mm_life_maximum', 'like', '%'.$request->mm_life_maximum .'%')->first();
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $recordcheck);
            }
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->customer_id) && isset($request->client_id) && isset($request->client_group_id)) {
            $delete_mm_life_max =  DB::table('MM_LIFE_MAX')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->delete();
            if ($delete_mm_life_max) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found');
        }
    }

    public function search(Request $request)

    {
        $ndc = DB::table('CUSTOMER')
            ->where('CUSTOMER_ID', 'like', '%' . $request->search. '%')
            ->orWhere('CUSTOMER_NAME', 'like', '%' .$request->search. '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getClient($ndcid)
    {
        $ndc = DB::table('CLIENT')
            ->where('CUSTOMER_ID', 'like', '%' . $ndcid . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getClientGroup($ndcid)
    {

        $ndc = DB::table('MM_LIFE_MAX')
            ->where('CLIENT_ID', $ndcid)
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }



    public function getDetails(Request $request)
    {

       
        $ndc = DB::table('MM_LIFE_MAX')
            ->select('MM_LIFE_MAX.CUSTOMER_ID', 'MM_LIFE_MAX.CLIENT_ID', 'MM_LIFE_MAX.CLIENT_GROUP_ID', 'MM_LIFE_MAX.EFFECTIVE_DATE', 'MM_LIFE_MAX.TERMINATION_DATE', 'MM_LIFE_MAX.MM_LIFE_MAXIMUM', 'MM_LIFE_MAX.MM_CLAIM_MAX', 'MM_LIFE_MAX.GROUPING_TYPE', 'MM_LIFE_MAX.MM_CLAIM_MAX_GROUP_TYPE', 'MM_LIFE_MAX.MM_CLAIM_MAX_PERIOD', 'CUSTOMER.CUSTOMER_NAME', 'CLIENT.CLIENT_NAME', 'CLIENT_GROUP.GROUP_NAME')
            ->join('CUSTOMER', 'CUSTOMER.CUSTOMER_ID', '=', 'MM_LIFE_MAX.CUSTOMER_ID')
            ->join('CLIENT', 'CLIENT.CLIENT_ID', '=', 'MM_LIFE_MAX.CLIENT_ID')
            ->join('CLIENT_GROUP', 'CLIENT_GROUP.CLIENT_GROUP_ID', '=', 'MM_LIFE_MAX.CLIENT_GROUP_ID')
            ->where('MM_LIFE_MAX.CUSTOMER_ID', $request->customer_id)
            ->where('MM_LIFE_MAX.CLIENT_ID', $request->client_id)
            ->where('MM_LIFE_MAX.CLIENT_GROUP_ID', $request->client_group_id)
            ->where('MM_LIFE_MAX.EFFECTIVE_DATE',$request->effective_date)

            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}