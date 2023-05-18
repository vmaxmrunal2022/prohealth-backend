<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class NdcExlusionController extends Controller
{


    public function add(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "ndc_exclusion_list" => ['required', 'max:10'],
            "exclusion_name" => ['required', 'max:35'],
            "ndc" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        }

        $createddate = date('y-m-d');
        $recordcheck = DB::table('NDC_EXCLUSIONS')
            ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
            ->first();

        $recordCheckNdcList = DB::table('NDC_EXCLUSION_LISTS')
            ->where('NDC', $request->ndc)
            ->where('NDC_EXCLUSION_LIST', $request->ndc_exclusion_list)
            ->first();

        if ($request->has('new')) {
            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'Ndc Exclusion List Id already exists in the system..!!!', $recordcheck);
            } else {
                $accum_benfit_stat = DB::table('NDC_EXCLUSION_LISTS')->insert(
                    [
                        'ndc' => $request->ndc,
                        'ndc_exclusion_list' => $request->ndc_exclusion_list,
                        'DATE_TIME_CREATED' => $createddate,
                    ]
                );
                $insert = DB::table('NDC_EXCLUSIONS')->insert(
                    [
                        'ndc_exclusion_list' => $request->ndc_exclusion_list,
                        'exclusion_name' => $request->exclusion_name,
                        'DATE_TIME_CREATED' => $createddate,
                    ]
                );
                if ($insert) {
                    return $this->respondWithToken($this->token(), 'Recored Added Successfully', $insert);
                }
            }
        } else {
            if ($recordCheckNdcList) {
                $update = DB::table('NDC_EXCLUSIONS')
                    ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                    ->update(
                        [
                            'exclusion_name' => $request->exclusion_name,
                        ]
                    );
                return $this->respondWithToken($this->token(), 'NDC Exclusion List ID already exists in the system..!!!', $update, false);
            } else {
                $createdNdcList = DB::table('NDC_EXCLUSION_LISTS')->insert(
                    [
                        'ndc' => $request->ndc,
                        'ndc_exclusion_list' => $request->ndc_exclusion_list,
                        'DATE_TIME_CREATED' => $createddate,
                    ]
                );

                $update = DB::table('NDC_EXCLUSIONS')
                    ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                    ->update(
                        [
                            'exclusion_name' => $request->exclusion_name,
                        ]
                    );
            }

            if ($update) {
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
            }
        }
    }



    public function delete(Request $request)
    {
        if (isset($request->ndc_exclusion_list) && isset($request->ndc)) {
            $delete_ndc_exclusion_list =  DB::table('NDC_EXCLUSION_LISTS')
                ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                ->where('ndc', $request->ndc)
                ->delete();
            if ($delete_ndc_exclusion_list) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found',);
            }
        } elseif (isset($request->ndc_exclusion_list)) {
            $delete_ndc_exclusions =  DB::table('NDC_EXCLUSIONS')
                ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                ->delete();

            $delete_ndc_exclusion_list =  DB::table('NDC_EXCLUSION_LISTS')
                ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                ->delete();

            if ($delete_ndc_exclusions) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found');
        }
    }

    public function AllNdcGpisExcusions(Request $request)
    {

        $data = DB::table('NDC_EXCLUSION_LISTS')
            ->get();

        if ($data) {
            return $this->respondWithToken($this->token(), 'Data fetched succefully', $data);
        } else {
            return $this->respondWithToken($this->token(), 'Something went wrong', $data);
        }
    }


    public function NdcExclusiondropdowns(Request $request)
    {

        $data = DB::table('NDC_EXCLUSIONS')
            ->get();

        if ($data) {
            return $this->respondWithToken($this->token(), 'Data fetched succefully', $data);
        } else {
            return $this->respondWithToken($this->token(), 'Something went wrong', $data);
        }
    }




    public function search(Request $request)

    {
        $ndc = DB::table('NDC_EXCLUSIONS')
            ->where(DB::raw('UPPER(NDC_EXCLUSIONS.NDC_EXCLUSION_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere(DB::raw('UPPER(NDC_EXCLUSIONS.EXCLUSION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getList($ndcid)
    {
        $ndc = DB::table('NDC_EXCLUSION_LISTS')
            ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
            ->where('NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', $ndcid)
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($ndcid, $ndc_exclusion_list)
    {
        $ndc = DB::table('NDC_EXCLUSION_LISTS')
            ->join('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
            // ->join('DRUG_MASTER', 'NDC_EXCLUSION_LISTS.NDC', '=', 'DRUG_MASTER.NDC')
            ->where('NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', $ndcid)
            ->where('NDC_EXCLUSION_LISTS.NDC', $ndc_exclusion_list)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
