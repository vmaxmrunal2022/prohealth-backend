<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GpiExclusionController extends Controller
{
    public function GPIS(Request $request)
    {
        $gpis =  DB::table('DRUG_MASTER')->get();
        return $this->respondWithToken($this->token(), 'data fetched successfully ', $gpis);
    }


    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "gpi_exclusion_list" => ['required', 'max:10'],
            "exclusion_name" => ['required','max:35'],
            "generic_product_id" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        }

        $recordcheck = DB::table('GPI_EXCLUSION_LISTS')
        ->where('GPI_EXCLUSION_LIST', strtoupper($request->gpi_exclusion_list))
        ->first();


        $recordcheck = DB::table('GPI_EXCLUSION_LISTS')
        ->where('GPI_EXCLUSION_LIST', strtoupper($request->gpi_exclusion_list))
        ->first();

       


        if ($request->has('new')) {
            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'GPI Exclusion List ID already exists in the system..!!!', $recordcheck);
            } else {
                $accum_benfit_stat = DB::table('GPI_EXCLUSION_LISTS')->insert(
                    [
                        'generic_product_id' => $request->generic_product_id,
                        'gpi_exclusion_list' => $request->gpi_exclusion_list,
                    ]
                );

                $insert = DB::table('GPI_EXCLUSIONS')->insert(
                    [
                        'gpi_exclusion_list' => $request->gpi_exclusion_list,
                        'exclusion_name' => $request->exclusion_name,
                    ]
                );
            }

            if ($insert) {
                return $this->respondWithToken($this->token(), 'Recored Added Successfully', $insert);
            }
        } else {
            $createddate = DB::table('GPI_EXCLUSION_LISTS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->update(
                    [
                        'generic_product_id' => $request->generic_product_id,
                    ]
                );
            $update = DB::table('GPI_EXCLUSIONS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
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
                return $this->respondWithToken($this->token(), 'Record Not Found',);
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
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found');
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
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
            ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->where('GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', 'like', '%' . $request->search . '%')
            ->orWhere('GPI_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' . $request->search . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getList($ndcid)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
            ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->where('GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', $ndcid)
            ->get();
        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function getDetails($gpi)
    {
        $ndc = DB::table('GPI_EXCLUSION_LISTS')
            ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
            ->join('DRUG_MASTER', 'GPI_EXCLUSION_LISTS.GENERIC_PRODUCT_ID', '=', 'DRUG_MASTER.GENERIC_PRODUCT_ID')
            ->where('GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', $gpi)

            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
