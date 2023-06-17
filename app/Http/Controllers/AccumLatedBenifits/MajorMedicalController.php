<?php

namespace App\Http\Controllers\AccumLatedBenifits;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MajorMedicalController extends Controller
{
    use AuditTrait;
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "customer_id" => ['required'],
            "client_id" => ['required'],
            "client_group_id" => ['required'],
            'mm_claim_max' => ['max:10'],
            'mm_life_maximum' => ['max:10'],
            'termination_date' => ['gt:effective_date']

        ], ['termination_date.gt' => 'Effective Date Could Not Be Greater Than or Equal to Termination Date ']);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            if ($request->update_new == 1) {
                $effectiveDate = $request->effective_date;
                $terminationDate = $request->termination_date;
                $overlapExists = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', $request->customer_id)
                    ->where('client_id', $request->client_id)
                    ->where('client_group_id', $request->client_group_id)
                    // ->where('effective_date',$request->effective_date)
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
                        'mm_claim_max_group_type' => $request->mm_claim_max_group_type,

                    ]
                );
                $add = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', $request->customer_id)
                    ->where('client_id', $request->client_id)
                    ->where('client_group_id', $request->client_group_id)
                    ->where('effective_date', $request->effective_date)
                    ->first();
                $record_snap = json_encode($add);
                $save_audit = $this->auditMethod('IN', $record_snap, 'MM_LIFE_MAX');
                return $this->respondWithToken($this->token(), 'Record added Successfully', $insert);
            } else if ($request->update_new == 0) {
                $effectiveDate = $request->effective_date;
                $terminationDate = $request->termination_date;
                $overlapExists = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', $request->customer_id)
                    ->where('client_id', $request->client_id)
                    ->where('client_group_id', $request->client_group_id)
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
                    return $this->respondWithToken($this->token(), [['For same Client Id , Customer Id , Client Group ID , dates cannot overlap.']], '', 'false');
                }

                $update = DB::table('MM_LIFE_MAX')
                    ->where('customer_id', $request->customer_id)
                    ->where('client_id', $request->client_id)
                    ->where('client_group_id', $request->client_group_id)
                    ->where('effective_date', $request->effective_date)

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
                $record_snap = json_encode($benefitcode);
                $save_audit = $this->auditMethod('UP', $record_snap, 'MM_LIFE_MAX');
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
            }
        }
    }

    public function delete(Request $request)

    {

        if (isset($request->customer_id) && isset($request->client_id) && isset($request->client_group_id) && isset($request->effective_date)) {

            $get_mm_life_max =  DB::table('MM_LIFE_MAX')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->where('effective_date', $request->effective_date)
                ->first();

            $save_audit_delete = $this->auditMethod('DE', json_encode($get_mm_life_max), 'MM_LIFE_MAX');

            $delete_mm_life_max =  DB::table('MM_LIFE_MAX')
                ->where('customer_id', $request->customer_id)
                ->where('client_id', $request->client_id)
                ->where('client_group_id', $request->client_group_id)
                ->where('effective_date', $request->effective_date)
                ->delete();

            if ($delete_mm_life_max) {

                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {

                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }



    public function search(Request $request)

    {
        $ndc = DB::table('MM_LIFE_MAX')
            ->select('MM_LIFE_MAX.CUSTOMER_ID', 'CUSTOMER.CUSTOMER_NAME')
            ->leftJoin('CUSTOMER', 'CUSTOMER.CUSTOMER_ID', '=', 'MM_LIFE_MAX.CUSTOMER_ID')
            ->whereRaw('LOWER(MM_LIFE_MAX.CUSTOMER_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->groupBy('MM_LIFE_MAX.CUSTOMER_ID', 'CUSTOMER.CUSTOMER_NAME')
            ->orderBy('MM_LIFE_MAX.CUSTOMER_ID')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getClient($ndcid)
    {
        $ndc = DB::table('MM_LIFE_MAX')
            // ->join('CLIENT','CLIENT.CUSTOMER_ID','=','MM_LIFE_MAX.CUSTOMER_ID')
            //     ->where('MM_LIFE_MAX.CUSTOMER_ID', 'like', '%' . $ndcid . '%')
            //     ->get();


            ->select('MM_LIFE_MAX.CLIENT_ID', 'CLIENT.CLIENT_NAME')
            ->join('CLIENT', 'CLIENT.CUSTOMER_ID', '=', 'MM_LIFE_MAX.CUSTOMER_ID')
            ->whereRaw('LOWER(MM_LIFE_MAX.CUSTOMER_ID) LIKE ?', ['%' . strtolower($ndcid) . '%'])
            ->groupBy('MM_LIFE_MAX.CLIENT_ID', 'CLIENT.CLIENT_NAME')
            ->orderBy('MM_LIFE_MAX.CLIENT_ID')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getClientGroup($ndcid)
    {

        $ndc = DB::table('MM_LIFE_MAX')
            ->select('MM_LIFE_MAX.EFFECTIVE_DATE', 'CLIENT_GROUP.GROUP_NAME', 'MM_LIFE_MAX.CLIENT_GROUP_ID', 'MM_LIFE_MAX.CUSTOMER_ID', 'MM_LIFE_MAX.CLIENT_ID')
            ->join('CLIENT_GROUP', 'MM_LIFE_MAX.CLIENT_GROUP_ID', '=', 'CLIENT_GROUP.CLIENT_GROUP_ID')
            ->where('MM_LIFE_MAX.CLIENT_ID', $ndcid)
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
            ->where('MM_LIFE_MAX.EFFECTIVE_DATE', $request->effective_date)

            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
