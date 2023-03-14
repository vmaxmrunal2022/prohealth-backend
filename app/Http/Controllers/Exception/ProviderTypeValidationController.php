<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProviderTypeValidationController extends Controller
{

    public function add(Request $request)
    {

        $createddate = date('y-m-d');

        $validator = Validator::make($request->all(), [
            'prov_type_list_id' => ['required', 'max:10', Rule::unique('PROVIDER_TYPE_VALIDATION_NAMES')->where(function ($q) {
                $q->whereNotNull('prov_type_list_id');
            })],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), '', $validator->errors(), false);
        } else {
            if ($request->new) {

                $accum_benfit_stat_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')->insert(
                    [
                        'prov_type_list_id' => strtoupper($request->prov_type_list_id),
                        'description' => $request->description,
                        'DATE_TIME_CREATED' => date('Ymd H:i:s'),
                        'USER_ID_CREATED' => Auth::id(),
                        'DATE_TIME_MODIFIED' => date('Ymd H:i:s'),
                    ]
                );


                $accum_benfit_stat = DB::table('PROVIDER_TYPE_VALIDATIONS')->insert(
                    [
                        'prov_type_list_id' => strtoupper($request->prov_type_list_id),
                        'provider_type' => $request->provider_type,
                        'DATE_TIME_CREATED' => date('Ymd H:i:s'),
                        'DATE_TIME_CREATED' => date('Ymd H:i:s'),
                        'USER_ID_CREATED' => Auth::id(),
                        'DATE_TIME_MODIFIED' => date('Ymd H:i:s'),
                        'effective_date' => $request->effective_date,
                        'proc_code_list_id' => $request->proc_code_list_id,
                        'provider_type' => $request->provider_type,
                        'termination_date' => $request->termination_date,
                        'proc_code_list_id' => $request->proc_code_list_id,

                    ]
                );

                $benefitcode = DB::table('PROVIDER_TYPE_VALIDATIONS')->where('prov_type_list_id', 'like', '%' . $request->prov_type_list_id . '%')->first();
                return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
            } else {


                $benefitcode = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                    ->where('prov_type_list_id', $request->prov_type_list_id)


                    ->update(
                        [
                            'description' => $request->description,

                        ]
                    );

                $accum_benfit_stat = DB::table('PROVIDER_TYPE_VALIDATIONS')
                    ->where('proc_code_list_id', $request->proc_code_list_id)
                    ->where('provider_type', $request->provider_type)
                    ->update(
                        [
                            'provider_type' => $request->provider_type,
                            'effective_date' => $request->effective_date,


                        ]
                    );

                $benefitcode = DB::table('PROVIDER_TYPE_VALIDATIONS')->where('proc_code_list_id', 'like', $request->proc_code_list_id)->first();
                return $this->respondWithToken($this->token(), 'Successfully added', $benefitcode);
            }
        }
    }


    public function getAllNames(Request $request)
    {

        $data = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
            ->where('PROV_TYPE_PROC_ASSOC_ID', 'LIKE', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), 'data fetched  successfully', $data);
    }

    public function get(Request $request)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
            // $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS')
            // ->where('effective_date', 'like', '%'.$request->search.'%')
            ->Where(DB::raw('UPPER(prov_type_list_id)'), 'like', '%' . strtoupper($request->search) . '%')
            // ->orWhere(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        // dd($request->all());
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }

    public function getList($ncdid)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS')
            ->Where(DB::raw('UPPER(PROV_TYPE_LIST_ID)'), 'like', '%' . strtoupper($ncdid) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }




    public function getNDCItemDetails($ndcid, $ndcid2)
    {
        $ndc = DB::table('PROVIDER_TYPE_VALIDATIONS')
            ->join('PROVIDER_TYPE_VALIDATION_NAMES', 'PROVIDER_TYPE_VALIDATION_NAMES.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
            // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
            ->where('PROVIDER_TYPE_VALIDATIONS.proc_code_list_id', $ndcid)
            ->where('PROVIDER_TYPE_VALIDATIONS.provider_type', $ndcid2)

            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
