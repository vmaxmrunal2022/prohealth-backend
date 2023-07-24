<?php

namespace App\Http\Controllers\AccumlatedBenifits;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class GpiExclusionController extends Controller
{
    use AuditTrait;
    public function GPIS(Request $request)
    {
        $gpis =  DB::table('DRUG_MASTER')->paginate(100);
        return $this->respondWithToken($this->token(), 'data fetched successfully ', $gpis);
    }

    public function GPISNEW(Request $request)
    {
        $gpis =  DB::table('DRUG_MASTER')
            ->select('generic_product_id as gpi')
            ->whereRaw('LOWER(GENERIC_PRODUCT_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orWhereRaw('LOWER(GENERIC_NAME) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->paginate(100);
        return $this->respondWithToken($this->token(), 'data fetched successfully ', $gpis);
    }


    public function add(Request $request)
    {
        $createddate = date('Ymd');
        $recordcheck = DB::table('GPI_EXCLUSIONS')
            ->where(DB::raw('UPPER(GPI_EXCLUSION_LIST)'), strtoupper($request->gpi_exclusion_list))
            ->first();

        $recordCheckGpiList = DB::table('GPI_EXCLUSION_LISTS')
            ->where(DB::raw('UPPER(GPI_EXCLUSION_LIST)'), strtoupper($request->gpi_exclusion_list))
            ->where('GENERIC_PRODUCT_ID', $request->generic_product_id)
            ->first();



            $validator = Validator::make($request->all(), [

                'gpi_exclusion_list' =>['required','string','max:10'],
                'generic_product_id' => ['string','max:14'],
                'exclusion_name' => ['required','max:35'],
               
                

          ]);


if ($validator->fails()) {
    return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");

}
else{

    if ($request->has('new')) {

        $recordcheck = DB::table('GPI_EXCLUSIONS')
        ->where(DB::raw('UPPER(GPI_EXCLUSION_LIST)'), strtoupper($request->gpi_exclusion_list))
        ->first();
        if ($recordcheck) {
            return $this->respondWithToken($this->token(), 'GPI Exclusion List ID already exists', $recordcheck, false);
        } else {
            $accum_benfit_stat = DB::table('GPI_EXCLUSION_LISTS')->insert(
                [
                    'generic_product_id' => $request->generic_product_id,
                    'gpi_exclusion_list' => $request->gpi_exclusion_list,
                    'DATE_TIME_CREATED' => $createddate,
                    'DATE_TIME_MODIFIED' => $createddate,
                    'USER_ID' => Cache::get('userId'),
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

            $getRecord =   DB::table('GPI_EXCLUSIONS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)->first();
        }

        if ($insert) {
            return $this->respondWithToken($this->token(), 'Recored Added Successfully', $getRecord);
        }
    } else {
        if ($recordCheckGpiList) {
            if ($request->addUpdate == 0) {
                return $this->respondWithToken($this->token(), 'GPI ID already exists', $recordCheckGpiList, false);
            }
            $update = DB::table('GPI_EXCLUSIONS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->update(
                    [
                        'exclusion_name' => $request->exclusion_name,
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                        'USER_ID' => Cache::get('userId'),
                    ]
                );
        } else {
            if ($request->generic_product_id) {
                $createGpiList = DB::table('GPI_EXCLUSION_LISTS')->insert(
                    [
                        'generic_product_id' => $request->generic_product_id,
                        'gpi_exclusion_list' => $request->gpi_exclusion_list,
                        'USER_ID' => Cache::get('userId'),
                        'DATE_TIME_CREATED' => date('Ymd'),
                        'DATE_TIME_MODIFIED' => date('Ymd'),
                    ]
                );
                $update = DB::table('GPI_EXCLUSIONS')
                    ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                    ->update(
                        [
                            'exclusion_name' => $request->exclusion_name,
                            'DATE_TIME_MODIFIED' => date('Ymd'),
                            'USER_ID' => Cache::get('userId'),
                        ]
                    );
                if ($createGpiList) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $update, true, 201);
                }
            } else {
                return $this->respondWithToken($this->token(), 'Please Select GPI List ID', $recordCheckGpiList, false);
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
        if (isset($request->gpi_exclusion_list) && isset($request->generic_product_id)) {

            // if ($all_gpi_exclusions_list == 1) {
            //     $delete_gpi_exclusions =  DB::table('GPI_EXCLUSIONS')
            //         ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
            //         ->delete();

            //     $delete_gpi_exclusions_list =  DB::table('GPI_EXCLUSION_LISTS')
            //         ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
            //         ->delete();
            // } 
            // else {
            $all_gpi_exclusions_list =  DB::table('GPI_EXCLUSION_LISTS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->where('generic_product_id', $request->generic_product_id)
                ->delete();
            $child_count =  DB::table('GPI_EXCLUSION_LISTS')
                ->where('gpi_exclusion_list', $request->gpi_exclusion_list)
                ->count();
            // }
            if ($all_gpi_exclusions_list) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', $child_count);
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
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', $delete_gpi_exclusions, true, 201);
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
        // $ndc = DB::table('GPI_EXCLUSION_LISTS')
        //     ->join('GPI_EXCLUSIONS', 'GPI_EXCLUSION_LISTS.GPI_EXCLUSION_LIST', '=', 'GPI_EXCLUSIONS.GPI_EXCLUSION_LIST')
        //     ->whereRaw('LOWER(GPI_EXCLUSIONS.GPI_EXCLUSION_LIST) LIKE ?', ['%' . strtolower($request->search) . '%'])
        //     ->orWhere('GPI_EXCLUSIONS.EXCLUSION_NAME', 'like', '%' . $request->search . '%')
        //     ->get();
        $ndc = DB::table('GPI_EXCLUSIONS')
            ->whereRaw('LOWER(GPI_EXCLUSION_LIST) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orderBy('GPI_EXCLUSION_LIST', 'ASC')
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
