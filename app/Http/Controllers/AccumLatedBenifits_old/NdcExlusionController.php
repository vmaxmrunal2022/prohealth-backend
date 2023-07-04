<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class NdcExlusionController extends Controller
{

    use AuditTrait;


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
            ->where(DB::raw('UPPER(ndc_exclusion_list)'), strtoupper($request->ndc_exclusion_list))
            ->first();

        $recordCheckNdcList = DB::table('NDC_EXCLUSION_LISTS')
            ->where('NDC', $request->ndc)
            ->where(DB::raw('UPPER(NDC_EXCLUSION_LIST)'), strtoupper($request->ndc_exclusion_list))
            ->first();

        if ($request->has('new')) {
            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'NDC Exclusion List ID already exists', $recordcheck, false);
            } else {
                $accum_benfit_stat = DB::table('NDC_EXCLUSION_LISTS')->insert(
                    [
                        'ndc' => $request->ndc,
                        'ndc_exclusion_list' => $request->ndc_exclusion_list,
                        'USER_ID' => Cache::get('userId'),
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                    ]
                );
                $insert = DB::table('NDC_EXCLUSIONS')->insert(
                    [
                        'ndc_exclusion_list' => $request->ndc_exclusion_list,
                        'exclusion_name' => $request->exclusion_name,
                        'USER_ID' => Cache::get('userId'),
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                    ]
                );
                if ($insert) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $insert);
                }
            }
        } else {
            if ($recordCheckNdcList) {
                if ($request->addUpdate == 0) {
                    return $this->respondWithToken($this->token(), 'NDC ID already exists', $recordCheckNdcList, false);
                }
                $update = DB::table('NDC_EXCLUSIONS')
                    ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                    ->update(
                        [
                            'exclusion_name' => $request->exclusion_name,
                            'DATE_TIME_MODIFIED' => date('Ymd'),
                            'USER_ID' => Cache::get('userId'),
                        ]
                    );
            } else {
                if ($request->ndc_exclusion_list) {
                    $createdNdcList = DB::table('NDC_EXCLUSION_LISTS')->insert(
                        [
                            'ndc' => $request->ndc,
                            'ndc_exclusion_list' => $request->ndc_exclusion_list,
                            'USER_ID' => Cache::get('userId'),
                            'DATE_TIME_CREATED' => date('Ymd'),
                            'DATE_TIME_MODIFIED' => date('Ymd'),
                        ]
                    );

                    $update = DB::table('NDC_EXCLUSIONS')
                        ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                        ->update(
                            [
                                'exclusion_name' => $request->exclusion_name,
                                'DATE_TIME_MODIFIED' => date('Ymd'),
                                'USER_ID' => Cache::get('userId'),
                            ]
                        );

                    if ($createdNdcList) {
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }
                }
            }

            if ($update) {
                return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
            }
        }
    }

    public function delete(Request $request)
    {
        if (isset($request->ndc_exclusion_list) && isset($request->ndc)) {
            $all_ndc_exclusion_list =  DB::table('NDC_EXCLUSION_LISTS')
                ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                ->count();

            if ($all_ndc_exclusion_list == 1) {
                $delete_ndc_exclusions =  DB::table('NDC_EXCLUSIONS')
                    ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                    ->delete();

                $delete_ndc_exclusion_list =  DB::table('NDC_EXCLUSION_LISTS')
                    ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                    ->delete();
            } else {
                $delete_ndc_exclusion_list =  DB::table('NDC_EXCLUSION_LISTS')
                    ->where('ndc_exclusion_list', $request->ndc_exclusion_list)
                    ->where('ndc', $request->ndc)
                    ->delete();
            }
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
        ->select('NDC_EXCLUSION_LISTS.*','DRUG_MASTER.LABEL_NAME')
            ->leftjoin('NDC_EXCLUSIONS', 'NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', '=', 'NDC_EXCLUSIONS.NDC_EXCLUSION_LIST')
            ->leftjoin('DRUG_MASTER', 'NDC_EXCLUSION_LISTS.NDC', '=', 'DRUG_MASTER.NDC')
            ->where('NDC_EXCLUSION_LISTS.NDC_EXCLUSION_LIST', $ndcid)
            ->where('NDC_EXCLUSION_LISTS.NDC', $ndc_exclusion_list)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}