<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProviderTypeValidationController extends Controller
{
    use AuditTrait;
    public function addcopy(Request $request)
    {
        $createddate = date('y-m-d');
        if ($request->has('new')) {


            $recordcheck = DB::table('PROVIDER_TYPE_VALIDATIONS')
                ->where('prov_type_list_id', $request->prov_type_list_id)
                ->first();


            $createddate = date('y-m-d');


            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'Provider Type  ID Already Exists', $recordcheck);
            } else {

                $accum_benfit_stat_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')->insert(
                    [
                        'prov_type_list_id' => $request->prov_type_list_id,
                        'description' => $request->description,
                        'DATE_TIME_CREATED' => $createddate,


                    ]
                );


                $add = DB::table('PROVIDER_TYPE_VALIDATIONS')
                    ->insert(
                        [
                            'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                            'PROV_TYPE_LIST_ID' => $request->prov_type_list_id,
                            'PROVIDER_TYPE' => $request->provider_type,
                            'EFFECTIVE_DATE' => $request->effective_date,
                            'TERMINATION_DATE' => $request->termination_date,
                            'DATE_TIME_CREATED' => $createddate,

                        ]
                    );


                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
            }
        } else {


            $benefitcode = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                ->where('PROV_TYPE_LIST_ID', $request->prov_type_list_id)


                ->update(
                    [
                        'description' => $request->description,

                    ]
                );

            $update = DB::table('PROVIDER_TYPE_VALIDATIONS')
                ->where('proc_code_list_id', $request->proc_code_list_id)
                ->where('provider_type', $request->provider_type)
                ->update(
                    [
                        'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                        'PROVIDER_TYPE' => $request->provider_type,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,
                        'DATE_TIME_CREATED' => $createddate,


                    ]
                );

            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
        }
    }

    public function add(Request $request)
    {
        $createddate = date('y-m-d');

        $validation = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
            ->where('prov_type_list_id', $request->prov_type_list_id)
            ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'prov_type_list_id' => ['required', 'max:10', Rule::unique('PROVIDER_TYPE_VALIDATION_NAMES')->where(function ($q) {
                    $q->whereNotNull('prov_type_list_id');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                'prov_type_list_id' => ['required', 'max:10', Rule::unique('PROVIDER_TYPE_VALIDATIONS')->where(function ($q) {
                    $q->whereNotNull('prov_type_list_id');
                })],

                "description" => ['max:36'],
                "provider_type" => ['required', 'max:2'],
                "effective_date" => ['max:10'],
                'termination_date' => ['max:15', 'after:effective_date'],
                'proc_code_list' => ['max:10'],

            ], [
                'termination_date.after' => 'Effective Date cannot be greater or equal to Termination date'
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'NDC Exception Already Exists', $validation, true, 200, 1);
                }
                $effectiveDate = $request->effective_date;
                $terminationDate = $request->termination_date;
                $overlapExists = DB::table('PROVIDER_TYPE_VALIDATIONS')
                    ->where('PROV_TYPE_LIST_ID', $request->prov_type_list_id)
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
                    return $this->respondWithToken($this->token(), 'For same Provider Type, Procedure Code List ID , dates cannot overlap.', $validation, true, 200, 1);
                }

                $add_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')->insert(
                    [
                        'prov_type_list_id' => $request->prov_type_list_id,
                        'description' => $request->description,

                    ]
                );

                $add = DB::table('PROVIDER_TYPE_VALIDATIONS')
                    ->insert([

                        'PROV_TYPE_LIST_ID' => $request->prov_type_list_id,

                        'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                        'PROVIDER_TYPE' => $request->provider_type,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'TERMINATION_DATE' => $request->termination_date,
                        'DATE_TIME_CREATED' => $createddate,


                    ]);

                $add = DB::table('PROVIDER_TYPE_VALIDATIONS')
                    ->where('prov_type_list_id', 'like', '%' . $request->prov_type_list_id . '%')
                    ->first();
                $record_snap = json_encode($add);
                $save_audit = $this->auditMethod('IN', $record_snap, 'PROVIDER_TYPE_VALIDATIONS');
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
            }
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                'prov_type_list_id' => ['required', 'max:10'],

                "description" => ['max:36'],
                "provider_type" => ['required', 'max:2'],
                "effective_date" => ['max:10'],
                'termination_date' => ['max:15', 'after:effective_date'],
                'proc_code_list' => ['max:10'],



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
                    $overlapExists = DB::table('PROVIDER_TYPE_VALIDATIONS')
                        ->where('PROV_TYPE_LIST_ID', $request->prov_type_list_id)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('provider_type', $request->provider_type)
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
                        return $this->respondWithToken($this->token(), [['For same Provider Type, Procedure Code List ID , dates cannot overlap.']], '', 'false');
                    }

                    $add_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                        ->where('prov_type_list_id', $request->prov_type_list_id)
                        ->update(
                            [
                                'description' => $request->description,

                            ]
                        );

                    $update = DB::table('PROVIDER_TYPE_VALIDATIONS')
                        ->where('prov_type_list_id', $request->prov_type_list_id)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('provider_type', $request->provider_type)
                        ->where('effective_date', $request->effective_date)
                        ->update(
                            [
                                'TERMINATION_DATE' => $request->termination_date,
                                'DATE_TIME_CREATED' => $createddate,

                            ]
                        );
                    $update = DB::table('PROVIDER_TYPE_VALIDATIONS')
                        ->where('prov_type_list_id', 'like', '%' . $request->prov_type_list_id . '%')
                        ->first();
                    $record_snap = json_encode($update);
                    $save_audit = $this->auditMethod('UP', $record_snap, 'PROVIDER_TYPE_VALIDATIONS');

                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                } elseif ($request->update_new == 1) {
                    $checkGPI = DB::table('PROVIDER_TYPE_VALIDATIONS')
                        ->where('prov_type_list_id', $request->prov_type_list_id)
                        ->where('proc_code_list_id', $request->proc_code_list_id)
                        ->where('provider_type', $request->provider_type)
                        ->where('effective_date', $request->effective_date)
                        ->get();

                    if (count($checkGPI) >= 1) {
                        return $this->respondWithToken($this->token(), [['For same Provider Type, Procedure Code List ID , dates cannot overlap.']], '', 'false');
                    } else {

                        $effectiveDate = $request->effective_date;
                        $terminationDate = $request->termination_date;
                        $overlapExists = DB::table('PROVIDER_TYPE_VALIDATIONS')
                            ->where('PROV_TYPE_LIST_ID', $request->prov_type_list_id)
                            ->where('proc_code_list_id', $request->proc_code_list_id)
                            ->where('provider_type', $request->provider_type)
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
                            return $this->respondWithToken($this->token(), [['For same Provider Type, Procedure Code List ID , dates cannot overlap.']], '', 'false');
                        }

                        $update = DB::table('PROVIDER_TYPE_VALIDATIONS')
                            ->insert([
                                'PROC_CODE_LIST_ID' => $request->proc_code_list_id,
                                'PROV_TYPE_LIST_ID' => $request->prov_type_list_id,
                                'PROVIDER_TYPE' => $request->provider_type,
                                'EFFECTIVE_DATE' => $request->effective_date,
                                'TERMINATION_DATE' => $request->termination_date,
                                'DATE_TIME_CREATED' => $createddate,
                            ]);

                        $update = DB::table('PROVIDER_TYPE_VALIDATIONS')
                            ->where('prov_type_list_id', 'like', '%' . $request->prov_type_list_id . '%')
                            ->first();
                        $record_snap = json_encode($update);
                        $save_audit = $this->auditMethod('UP', $record_snap, 'PROVIDER_TYPE_VALIDATIONS');
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
                    }
                }

                // $update_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                // ->where('prov_type_list_id', $request->prov_type_list_id )
                // ->first();


                // $checkGPI = DB::table('PROVIDER_TYPE_VALIDATIONS')


                //     ->where('prov_type_list_id',$request->prov_type_list_id)
                //     ->where('proc_code_list_id', $request->proc_code_list_id)
                //     ->where('provider_type',$request->provider_type)
                //     ->where('effective_date',$request->effective_date)
                //     ->get()
                //     ->count();
                //     // dd($checkGPI);
                // // if result >=1 then update NDC_EXCEPTION_LISTS table record
                // //if result 0 then add NDC_EXCEPTION_LISTS record


                // if ($checkGPI <= "0") {
                //     $update = DB::table('PROVIDER_TYPE_VALIDATIONS')
                //     ->insert([


                //         'PROC_CODE_LIST_ID'=>$request->proc_code_list_id,
                //         'PROV_TYPE_LIST_ID'=>$request->prov_type_list_id,
                //         'PROVIDER_TYPE'=>$request->provider_type,
                //         'EFFECTIVE_DATE'=>$request->effective_date,
                //         'TERMINATION_DATE'=>$request->termination_date,
                //         'DATE_TIME_CREATED'=>$createddate,


                //     ]);

                // $update = DB::table('PROVIDER_TYPE_VALIDATIONS')->where('prov_type_list_id', 'like', '%' . $request->prov_type_list_id . '%')->first();
                // return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);

                // } else {


                //     $add_names = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                //     ->where('prov_type_list_id',$request->prov_type_list_id)
                //     ->update(
                //         [
                //             'description'=>$request->description,

                //         ]
                //     );

                //     $update = DB::table('PROVIDER_TYPE_VALIDATIONS' )
                //     ->where('prov_type_list_id',$request->prov_type_list_id)
                //     ->where('proc_code_list_id', $request->proc_code_list_id)
                //     ->where('provider_type',$request->provider_type)
                //     ->where('effective_date',$request->effective_date)
                //     ->update(
                //         [
                //         'TERMINATION_DATE'=>$request->termination_date,
                //         'DATE_TIME_CREATED'=>$createddate,

                //         ]
                //     );
                //     $update = DB::table('PROVIDER_TYPE_VALIDATIONS')->where('prov_type_list_id', 'like', '%' . $request->prov_type_list_id . '%')->first();
                //     return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
                // }



            }
        }
    }



    public function getAllNames(Request $request)
    {

        $data = DB::table('PROV_TYPE_PROC_ASSOC_NAMES')
            ->where('PROV_TYPE_PROC_ASSOC_ID', 'LIKE', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), 'data fetched  successfully', $data);
    }

    public function get(Request $request)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
            // $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS')
            // ->where('effective_date', 'like', '%'.$request->search.'%')
            // ->Where('prov_type_list_id', 'like', '%' . $request->search . '%')
            ->whereRaw('LOWER(prov_type_list_id) LIKE ?', ['%' . strtolower($request->search) . '%'])
            // ->orWhere(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        // dd($request->all());
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }

    public function getList($ncdid)
    {
        $providerTypeValidations = DB::table('PROVIDER_TYPE_VALIDATIONS')
            ->join('PROVIDER_TYPE_VALIDATION_NAMES', 'PROVIDER_TYPE_VALIDATION_NAMES.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
            ->Where('PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID', $ncdid)
            ->get();
        return $this->respondWithToken($this->token(), '', $providerTypeValidations);
    }




    public function getNDCItemDetails(Request $request)
    {
        $ndc = DB::table('PROVIDER_TYPE_VALIDATIONS')
            ->join('PROVIDER_TYPE_VALIDATION_NAMES as valdation_names', 'valdation_names.PROV_TYPE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROV_TYPE_LIST_ID')
            ->join('PROC_CODE_LIST_NAMES as list_names', 'list_names.PROC_CODE_LIST_ID', '=', 'PROVIDER_TYPE_VALIDATIONS.PROC_CODE_LIST_ID')
            ->join('PROVIDER_TYPES as types', 'types.PROVIDER_TYPE', '=', 'PROVIDER_TYPE_VALIDATIONS.PROVIDER_TYPE')
            ->select(
                'PROVIDER_TYPE_VALIDATIONS.PROC_CODE_LIST_ID',
                'PROVIDER_TYPE_VALIDATIONS.prov_type_list_id',
                'PROVIDER_TYPE_VALIDATIONS.provider_type',
                'PROVIDER_TYPE_VALIDATIONS.date_time_created',
                'PROVIDER_TYPE_VALIDATIONS.date_time_modified',
                'PROVIDER_TYPE_VALIDATIONS.effective_date',
                'PROVIDER_TYPE_VALIDATIONS.form_id',
                'PROVIDER_TYPE_VALIDATIONS.termination_date',
                'PROVIDER_TYPE_VALIDATIONS.user_id',
                'PROVIDER_TYPE_VALIDATIONS.user_id_created',
                'valdation_names.DESCRIPTION as description',
                'list_names.DESCRIPTION as Procedure_code_description',
                'types.DESCRIPTION as provider_type_description'
            )
            ->where('PROVIDER_TYPE_VALIDATIONS.prov_type_list_id', $request->prov_type_list_id)
            ->where('PROVIDER_TYPE_VALIDATIONS.provider_type', $request->provider_type)
            ->where('PROVIDER_TYPE_VALIDATIONS.proc_code_list_id', $request->proc_code_list_id)

            ->where('PROVIDER_TYPE_VALIDATIONS.effective_date', $request->effective_date)








            // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function provider_type_validation_delete(Request $request)
    {

        if (isset($request->prov_type_list_id) && ($request->proc_code_list_id)) {
            $all_exceptions_lists =  DB::table('PROVIDER_TYPE_VALIDATIONS')
                ->where('PROV_TYPE_LIST_ID', $request->prov_type_list_id)
                ->delete();

            if ($all_exceptions_lists) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else if (isset($request->prov_type_list_id)) {

            $exception_delete =  DB::table('PROVIDER_TYPE_VALIDATION_NAMES')
                ->where('PROV_TYPE_LIST_ID', $request->prov_type_list_id)
                ->delete();

            if ($exception_delete) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        }
    }
}
