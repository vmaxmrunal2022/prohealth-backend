<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GpiExclusionController extends Controller
{
    public function GPIS(Request $request)
    {
        $gpis =  DB::table('DRUG_MASTER')->get();
        return $this->respondWithToken($this->token(), 'data fetched successfully ', $gpis);
    }


    public function add(Request $request)
    {
        $recordcheck = DB::table('GPI_EXCLUSIONS')
            ->where(DB::raw('UPPER(GPI_EXCLUSION_LIST)'), strtoupper($request->gpi_exclusion_list))
            ->first();

        $recordCheckGpiList = DB::table('GPI_EXCLUSION_LISTS')
            ->where(DB::raw('UPPER(GPI_EXCLUSION_LIST)'), strtoupper($request->gpi_exclusion_list))
            ->where('GENERIC_PRODUCT_ID', $request->generic_product_id)
            ->first();

        if ($request->has('new')) {
            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'GPI Exclusion ID already exists in the system..!!!', $recordcheck, false);
            } else {
                $accum_benfit_stat = DB::table('GPI_EXCLUSION_LISTS')->insert(
                    [
                        'generic_product_id' => $request->generic_product_id,
                        'gpi_exclusion_list' => $request->gpi_exclusion_list,
                        'USER_ID' => Cache::get('userId'),
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                    ]
                );

                $insert = DB::table('GPI_EXCLUSIONS')->insert(
                    [
                        'gpi_exclusion_list' => $request->gpi_exclusion_list,
                        'exclusion_name' => $request->exclusion_name,
                        'USER_ID' => Cache::get('userId'),
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                    ]
                );
            }

            if ($insert) {
                return $this->respondWithToken($this->token(), 'Recored Added Successfully', $insert);
            }
        } else {
            if ($recordCheckGpiList) {
                $update = DB::table('GPI_EXCLUSIONS')
                    ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                    ->update(
                        [
                            'exclusion_name' => $request->exclusion_name,
                        ]
                    );
                return $this->respondWithToken($this->token(), 'GPI Exclusion List ID already exists in the system..!!!', $recordCheckGpiList, false);
            } else {
                if ($request->generic_product_id) {
                    $createGpiList = DB::table('GPI_EXCLUSION_LISTS')->insert(
                        [
                            'generic_product_id' => $request->generic_product_id,
                            'gpi_exclusion_list' => $request->gpi_exclusion_list,
                        ]
                    );
                    $update = DB::table('GPI_EXCLUSIONS')
                        ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                        ->update(
                            [
                                'exclusion_name' => $request->exclusion_name,
                            ]
                        );
                } else {
                    return $this->respondWithToken($this->token(), 'Please Select GPI List ID', $recordCheckGpiList, false);
                }
            }
        }

        if ($update) {
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
        }
    }


    public function delete(Request $request)
    {
        if (isset($request->gpi_exclusion_list) && isset($request->generic_product_id)) {
            $delete_gpi_exclusions_list =  DB::table('GPI_EXCLUSION_LISTS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->where('generic_product_id', $request->generic_product_id)
                ->delete();
            if ($delete_gpi_exclusions_list) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } elseif (isset($request->gpi_exclusion_list)) {
            $delete_gpi_exclusions =  DB::table('GPI_EXCLUSIONS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->delete();

            $delete_gpi_exclusions_list =  DB::table('GPI_EXCLUSION_LISTS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->delete();

            if ($delete_gpi_exclusions) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
        }
    }

    public function allGpiExclusions(Request $request)
    {
        $data = DB::table('GPI_EXCLUSION_LISTS')
            ->get();
        if ($data) {
            return $this->respondWithToken($this->token(), 'Data fetched succefully', $data);
        } else {
            return $this->respondWithToken($this->token(), 'Something went wrong', $data);
        }
    }

    public function search(Request $request)
    {
        $ndc = DB::table('GPI_EXCLUSIONS')
            // ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->where(DB::raw('UPPER(GPI_EXCLUSIONS.GPI_EXCLUSION_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere(DB::raw('UPPER(GPI_EXCLUSIONS.EXCLUSION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($gpiExclusionId)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
            ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->where('GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', $gpiExclusionId)
            ->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($gpi_exclusion_list, $generic_product_id)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
            ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->join('DRUG_MASTER', 'GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', '=', 'DRUG_MASTER.GENERIC_PRODUCT_ID')
            ->where('GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', $gpi_exclusion_list)
            ->where('GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', $generic_product_id)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
