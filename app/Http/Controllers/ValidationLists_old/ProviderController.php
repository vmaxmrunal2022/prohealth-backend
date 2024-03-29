<?php

namespace App\Http\Controllers\validationLists;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProviderController extends Controller
{
    use AuditTrait;
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $pharmacyExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                ->where(DB::raw('UPPER(PHARMACY_LIST)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere(DB::raw('UPPER(EXCEPTION_NAME)'), 'like', '%' . strtoupper($request->search) . '%')
                ->orderBy('PHARMACY_LIST', 'ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $pharmacyExceptionData);
        }
    }



    public function getProviderValidationList($pharmacy_list)
    {
        $pharmacyValidationData = DB::table('PHARMACY_VALIDATIONS')
            ->select(
                'PHARMACY_TABLE.PHARMACY_NABP',
                'PHARMACY_VALIDATIONS.PHARMACY_LIST',
                'PHARMACY_VALIDATIONS.PHARMACY_STATUS',
                'PHARMACY_TABLE.PHARMACY_NAME',
                'PHARMACY_EXCEPTIONS.EXCEPTION_NAME'
            )
            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
            ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
            ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $pharmacy_list)
            ->get();
        // return $pharmacyValidationData;
        return $this->respondWithToken($this->token(), '', $pharmacyValidationData);
    }


    public function getProviderDetails($pharmacy_list, $pharmacy_nabp)
    {
        $data = DB::table('PHARMACY_VALIDATIONS as a')
            ->select('a.PHARMACY_LIST', 'a.PHARMACY_NABP', 'a.PHARMACY_STATUS', 'c.PHARMACY_NAME', 'b.EXCEPTION_NAME')
            ->join('PHARMACY_EXCEPTIONS as b', 'b.PHARMACY_LIST', '=', 'a.PHARMACY_LIST')
            ->join('PHARMACY_TABLE as c', 'c.PHARMACY_NABP', '=', 'a.PHARMACY_NABP')
            ->where('a.PHARMACY_LIST',  $pharmacy_list)
            ->where('a.PHARMACY_NABP',  $pharmacy_nabp)
            ->first();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addProviderData_old(Request $request)
    {
        $getProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
            ->where(DB::raw('UPPER(PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
            ->first();

        $getProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
            ->where('PHARMACY_LIST', $request->pharmacy_list)
            ->where('PHARMACY_NABP', $request->pharmacy_nabp)
            ->first();
        if ($request->add_new) {
            $validator = Validator::make($request->all(), [
                "pharmacy_list" => ['required', 'max:10', Rule::unique('PHARMACY_EXCEPTIONS')->where(function ($q) {
                    $q->whereNotNull('pharmacy_list');
                })],
                "exception_name" => ['max:35'],
                "pharmacy_nabp" => ['required'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                if (!$getProviderExceptionData && !$getProviderValidationData) {
                    $addProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                        ->insert([
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'EXCEPTION_NAME' => $request->exception_name,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name
                        ]);

                    $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                        ->insert([
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'PHARMACY_NABP' => $request->pharmacy_nabp,
                            'PHARMACY_STATUS' => $request->pharmacy_status,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name
                        ]);

                    if ($addProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Added Successfully ...!!!', $addProviderExceptionData);
                    }
                } else {
                    if (!$getProviderValidationData) {
                        $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                            ->insert([
                                'PHARMACY_LIST' => $request->pharmacy_list,
                                'PHARMACY_NABP' => $request->pharmacy_nabp,
                                'PHARMACY_STATUS' => $request->pharmacy_status,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'USER_ID' => $request->user_name
                            ]);
                        if ($addProviderValidationData) {
                            return $this->respondWithToken($this->token(), 'Added Successfully!!!', $addProviderValidationData);
                        }
                    } else {
                        return $this->respondWithToken($this->token(), 'This record is already exists ..!!!');
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                "pharmacy_list" => ['required', 'max:10'],
                "exception_name" => ['max:35'],
                "pharmacy_nabp" => ['required'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $updateProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                    ->where('PHARMACY_LIST', $request->pharmacy_list)
                    ->update([
                        'EXCEPTION_NAME' => $request->exception_name,
                        'DATE_TIME_MODIFIED' => date('d-M-y'),
                    ]);

                if (!$getProviderValidationData) {
                    $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                        ->insert([
                            'PHARMACY_LIST' => $request->pharmacy_list,
                            'PHARMACY_NABP' => $request->pharmacy_nabp,
                            'PHARMACY_STATUS' => $request->pharmacy_status,
                            'DATE_TIME_CREATED' => date('d-M-y'),
                            'USER_ID' => $request->user_name
                        ]);
                    if ($addProviderValidationData) {
                        return $this->respondWithToken($this->token(), 'Added Successfully ...!!!', $addProviderValidationData);
                    }
                } else {
                    $updateProviderExceptionData = DB::table('PHARMACY_VALIDATIONS')
                        ->where('PHARMACY_LIST', $request->pharmacy_list)
                        ->where('PHARMACY_NABP', $request->pharmacy_nabp)
                        ->update([
                            'PHARMACY_STATUS' => $request->pharmacy_status,
                            'DATE_TIME_MODIFIED' => date('d-M-y')
                        ]);

                    if ($updateProviderExceptionData) {
                        return $this->respondWithToken($this->token(), 'Update Successfully.. !!!', $updateProviderExceptionData);
                    }
                }
            }
        }
    }


    public function addProviderData(Request $request)
    {
        /** $providerList is to get all the pharmacy validation record which are falling under particular pharmacy list */
        $providerList = DB::table('PHARMACY_VALIDATIONS')
            ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($request->pharmacy_list))
            ->get();

        if ($request->add_new) {
            $validator = Validator::make($request->all(), [
                // "pharmacy_list" => ['required', 'max:10', Rule::unique('PHARMACY_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('pharmacy_list');
                // })],
                "exception_name" => ['max:35'],
                "pharmacy_nabp" => ['required'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } else {
                if (!$request->updateForm) {
                    $ifExist = DB::table('PHARMACY_EXCEPTIONS')
                        ->where(DB::raw('UPPER(PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
                        ->get();
                    if (count($ifExist) >= 1) {
                        return $this->respondWithToken($this->token(), [["Provider List ID already exists"]], $providerList, false);
                    }
                } else {
                }
                if ($request->pharmacy_list && $request->pharmacy_nabp) {
                    $count = DB::table('PHARMACY_EXCEPTIONS')
                        ->where(DB::raw('UPPER(PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
                        ->get()
                        ->count();

                    //if exception is not existing
                    if ($count <= 0) {
                        $addException = DB::table('PHARMACY_EXCEPTIONS')
                            ->insert([
                                'pharmacy_list' => $request->pharmacy_list,
                                'exception_name' => $request->exception_name,
                                'date_time_created' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);
                        $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                            ->insert([
                                'PHARMACY_LIST' => $request->pharmacy_list,
                                'PHARMACY_NABP' => $request->pharmacy_nabp,
                                'PHARMACY_STATUS' => $request->pharmacy_status,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'USER_ID' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-Y'),
                                'form_id' => ''
                            ]);

                        // $reecord = DB::table('PHARMACY_VALIDATIONS')
                        //     ->join('PHARMACY_VALIDATIONS', 'PHARMACY_VALIDATIONS.PHARMACY_LIST', '=', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST')
                        //     ->where(DB::raw('UPPER(PHARMACY_VALIDATIONS.PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
                        //     ->where(DB::raw('UPPER(PHARMACY_VALIDATIONS.PHARMACY_NABP)'), strtoupper($request->pharmacy_nabp))
                        //     ->first();

                        $diag_validation = DB::table('PHARMACY_VALIDATIONS')
                            ->select(
                                'PHARMACY_TABLE.PHARMACY_NABP',
                                'PHARMACY_VALIDATIONS.PHARMACY_LIST',
                                'PHARMACY_VALIDATIONS.PHARMACY_STATUS',
                                'PHARMACY_TABLE.PHARMACY_NAME',
                                'PHARMACY_EXCEPTIONS.EXCEPTION_NAME'
                            )
                            ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
                            ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
                            ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $request->pharmacy_list)
                            ->get();
                        $diag_exception = DB::table('PHARMACY_EXCEPTIONS')
                            ->where(DB::raw('UPPER(pharmacy_list)'), 'like', '%' . strtoupper($request->pharmacy_list) . '%')
                            ->get();

                        return $this->respondWithToken(
                            $this->token(),
                            'Record Added successfully',
                            [[], []],
                        );
                    } else {
                        $updateProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                            ->where(DB::raw('UPPER(PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
                            ->update([
                                'exception_name' => $request->exception_name,
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);

                        $countValidation = DB::table('PHARMACY_VALIDATIONS')
                            ->where(DB::raw('UPPER(PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
                            ->where(DB::raw('UPPER(PHARMACY_NABP)'), strtoupper($request->pharmacy_nabp))
                            // ->where('pharmacy_status', $request->pharmacy_status)
                            ->get()
                            ->count();

                        //if exception exist but validation not exist
                        if ($countValidation >= 1) {
                            return $this->respondWithToken(
                                $this->token(),
                                [['Provider ID already exists']],
                                [['Provider ID already exists']],
                                false
                            );
                        } else {
                            $addProviderValidationData = DB::table('PHARMACY_VALIDATIONS')
                                ->insert([
                                    'PHARMACY_LIST' => $request->pharmacy_list,
                                    'PHARMACY_NABP' => $request->pharmacy_nabp,
                                    'PHARMACY_STATUS' => $request->pharmacy_status,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                    'USER_ID' => Cache::get('userId'),
                                    'date_time_modified' => date('d-M-y'),
                                    'form_id' => ''
                                ]);
                            // $reecord = DB::table('PHARMACY_EXCEPTIONS')
                            //     ->join('PHARMACY_VALIDATIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
                            //     ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $request->pharmacy_list)
                            //     ->where('PHARMACY_VALIDATIONS.PHARMACY_NABP', $request->pharmacy_nabp)
                            //     ->first();
                            $diag_validation = DB::table('PHARMACY_VALIDATIONS')
                                ->select(
                                    'PHARMACY_TABLE.PHARMACY_NABP',
                                    'PHARMACY_VALIDATIONS.PHARMACY_LIST',
                                    'PHARMACY_VALIDATIONS.PHARMACY_STATUS',
                                    'PHARMACY_TABLE.PHARMACY_NAME',
                                    'PHARMACY_EXCEPTIONS.EXCEPTION_NAME'
                                )
                                ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
                                ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
                                ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $request->pharmacy_list)
                                ->get();
                            $diag_exception = DB::table('PHARMACY_EXCEPTIONS')
                                ->where(DB::raw('UPPER(pharmacy_list)'), 'like', '%' . strtoupper($request->pharmacy_list) . '%')
                                ->get();
                            return $this->respondWithToken(
                                $this->token(),
                                'Record Added successfully',
                                [$diag_validation, $diag_exception],
                            );
                        }
                    }
                }
            }
        } else {
            $updateProviderExceptionData = DB::table('PHARMACY_EXCEPTIONS')
                ->where('PHARMACY_LIST', $request->pharmacy_list)
                ->update([
                    'exception_name' => $request->exception_name,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $countValidation = DB::table('PHARMACY_VALIDATIONS')
                ->where(DB::raw('UPPER(PHARMACY_LIST)'), strtoupper($request->pharmacy_list))
                ->where(DB::raw('UPPER(PHARMACY_NABP)'), strtoupper($request->pharmacy_nabp))
                // ->where('pharmacy_status', $request->pharmacy_status)
                ->update([
                    'pharmacy_status' => $request->pharmacy_status,
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);
            $diag_validation = DB::table('PHARMACY_VALIDATIONS')
                ->select(
                    'PHARMACY_TABLE.PHARMACY_NABP',
                    'PHARMACY_VALIDATIONS.PHARMACY_LIST',
                    'PHARMACY_VALIDATIONS.PHARMACY_STATUS',
                    'PHARMACY_TABLE.PHARMACY_NAME',
                    'PHARMACY_EXCEPTIONS.EXCEPTION_NAME'
                )
                ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
                ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
                ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $request->pharmacy_list)
                ->get();
            $diag_exception = DB::table('PHARMACY_EXCEPTIONS')
                ->where(DB::raw('UPPER(pharmacy_list)'), 'like', '%' . strtoupper($request->pharmacy_list) . '%')
                ->get();
            return $this->respondWithToken(
                $this->token(),
                'Record Updated successfully',
                [$diag_validation, $diag_exception],
            );
        }
    }


    public function searchDropDownProviderList($pharmacy_list = '')
    {
        $data = DB::table('PHARMACY_TABLE')
            ->where('PHARMACY_NABP', 'LIKE', '%' . strtoupper($pharmacy_list) . '%')
            ->orWhere('PHARMACY_NAME', 'LIKE', '%' . strtoupper($pharmacy_list) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function searchDropDownProviderListNew($pharmacy_list = '')
    {
        $data = DB::table('PHARMACY_TABLE')
        ->whereRaw('LOWER(PHARMACY_NABP) LIKE ?', ['%' . strtolower($pharmacy_list) . '%'])
        // ->where('PHARMACY_NABP', 'LIKE', '%' . strtoupper($pharmacy_list) . '%')
            ->orWhere('PHARMACY_NAME', 'LIKE', '%' . strtoupper($pharmacy_list) . '%')
            ->paginate(100);

        return $this->respondWithToken($this->token(), '', $data);
    }

    public  function getProviderOptions(Request $request)
    {
        $provider_options = [
            ['provider_id' => '', 'provider_name' => 'Not Specified'],
            ['provider_id' => 'N', 'provider_name' => 'NONE (no provider check)'],
            ['provider_id' => 'F', 'provider_name' => 'Validate Provider Format'],
            ['provider_id' => 'M', 'provider_name' => 'Must Exist Within Provider Master'],
            ['provider_id' => 'P', 'provider_name' => 'Must Exist Nn Provider Network'],
            ['provider_id' => 'V', 'provider_name' => 'Validate Provider In/Out Of Network'],
        ];

        return $this->respondWithToken($this->token(), '', $provider_options);
    }

    public function deleteRecordold(Request $request)
    {
        // return $request->all();
        $count = 0;
        foreach ($request->all() as $key => $value) {
            if (is_array($value)) {
                $count++;
            }
        }
        if ($count > 0) {
            $data = $request->all();
            $delete_pharmacy_nabp = DB::table('PHARMACY_EXCEPTIONS')
                ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($data[0]['pharmacy_list']))
                ->delete();
            $delete_pharmacy_nabp = DB::table('PHARMACY_VALIDATIONS')
                ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($data[0]['pharmacy_list']))
                ->delete();
            $diagnosis_exception =
                DB::table('PHARMACY_EXCEPTIONS')
                ->where(DB::raw('UPPER(pharmacy_list)'), 'like', '%' . strtoupper($request->pharmacy_list) . '%')
                ->get();
            $diag_validation = DB::table('PHARMACY_VALIDATIONS')
                ->select(
                    'PHARMACY_TABLE.PHARMACY_NABP',
                    'PHARMACY_VALIDATIONS.PHARMACY_LIST',
                    'PHARMACY_VALIDATIONS.PHARMACY_STATUS',
                    'PHARMACY_TABLE.PHARMACY_NAME',
                    'PHARMACY_EXCEPTIONS.EXCEPTION_NAME'
                )
                ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
                ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
                ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $request->pharmacy_list)
                ->get();
            return $this->respondWithToken($this->token(), "Record Deleted Successfully ", $diag_validation);
        } else        
        if ($request->pharmacy_list) {
            if ($request->pharmacy_nabp) {
                $delete_pharmacy_nabp = DB::table('PHARMACY_VALIDATIONS')
                    ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($request->pharmacy_list))
                    ->where(DB::raw('UPPER(pharmacy_nabp)'), strtoupper($request->pharmacy_nabp))
                    ->delete();
                $diagnosis_validation = DB::table('PHARMACY_VALIDATIONS')
                    ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($request->pharmacy_list))
                    ->get();
                $diag_validation = DB::table('PHARMACY_VALIDATIONS')
                    ->select(
                        'PHARMACY_TABLE.PHARMACY_NABP',
                        'PHARMACY_VALIDATIONS.PHARMACY_LIST',
                        'PHARMACY_VALIDATIONS.PHARMACY_STATUS',
                        'PHARMACY_TABLE.PHARMACY_NAME',
                        'PHARMACY_EXCEPTIONS.EXCEPTION_NAME'
                    )
                    ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
                    ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
                    ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $request->pharmacy_list)
                    ->get();
                if (count($diagnosis_validation) <= 0) {
                    $delete_pharmacy_list = DB::table('PHARMACY_EXCEPTIONS')
                        ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($request->pharmacy_list))
                        ->delete();
                    $diagnosis_validation1 = DB::table('PHARMACY_EXCEPTIONS')
                        ->where(DB::raw('UPPER(pharmacy_list)'), 'like', '%' . strtoupper($request->pharmacy_list) . '%')
                        ->get();
                    $diag_validation = DB::table('PHARMACY_VALIDATIONS')
                        ->select(
                            'PHARMACY_TABLE.PHARMACY_NABP',
                            'PHARMACY_VALIDATIONS.PHARMACY_LIST',
                            'PHARMACY_VALIDATIONS.PHARMACY_STATUS',
                            'PHARMACY_TABLE.PHARMACY_NAME',
                            'PHARMACY_EXCEPTIONS.EXCEPTION_NAME'
                        )
                        ->join('PHARMACY_TABLE', 'PHARMACY_TABLE.PHARMACY_NABP', '=', 'PHARMACY_VALIDATIONS.PHARMACY_NABP')
                        ->join('PHARMACY_EXCEPTIONS', 'PHARMACY_EXCEPTIONS.PHARMACY_LIST', '=', 'PHARMACY_VALIDATIONS.PHARMACY_LIST')
                        ->where('PHARMACY_VALIDATIONS.PHARMACY_LIST', $request->pharmacy_list)
                        ->get();
                    return $this->respondWithToken($this->token(), "Parent and Child Deleted Successfully ", $diag_validation, false);
                }
                return $this->respondWithToken($this->token(), "Record Deleted Successfully ", $diag_validation);
            } else {
                $delete_pharmacy_nabp = DB::table('PHARMACY_EXCEPTIONS')
                    ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($request->pharmacy_list))
                    ->delete();
                $delete_pharmacy_nabp = DB::table('PHARMACY_VALIDATIONS')
                    ->where(DB::raw('UPPER(pharmacy_list)'), strtoupper($request->pharmacy_list))
                    ->delete();
                $diagnosis_exception =
                    DB::table('PHARMACY_EXCEPTIONS')
                    ->where(DB::raw('UPPER(pharmacy_list)'), 'like', '%' . strtoupper($request->pharmacy_list) . '%')
                    ->get();
                return $this->respondWithToken($this->token(), "Record Deleted Successfully ", '');
            }
        }
    }

    public function deleteRecord(Request $request)
    {
        if (isset($request->pharmacy_list) && isset($request->pharmacy_nabp)) {
            $all_copay_strategy = DB::table('PHARMACY_VALIDATIONS')
                ->where('pharmacy_list', $request->pharmacy_list)
                ->where('pharmacy_nabp', $request->pharmacy_nabp)
                ->first();
            if ($all_copay_strategy) {
                $copay_strategy = DB::table('PHARMACY_VALIDATIONS')
                ->where('pharmacy_list', $request->pharmacy_list)
                ->where('pharmacy_nabp', $request->pharmacy_nabp)
                    ->delete();
                if ($copay_strategy) {
                    $val = DB::table('PHARMACY_VALIDATIONS')
                        // ->join('SPECIALTY_EXCEPTIONS', 'PHARMACY_VALIDATIONS.copay_strategy_id', '=', 'SPECIALTY_EXCEPTIONS.copay_strategy_id')
                        ->where('PHARMACY_VALIDATIONS.pharmacy_list', $request->pharmacy_list)
                        ->count();
                    return $this->respondWithToken($this->token(), 'Record Deleted Successfully ', $val);
                }
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } elseif (isset($request->pharmacy_list)) {
            $all_accum_bene_strategy_names = DB::table('PHARMACY_EXCEPTIONS')
                ->where('pharmacy_list', $request->pharmacy_list)
                ->delete();
            $copay_strategy = DB::table('PHARMACY_VALIDATIONS')
                ->where('pharmacy_list', $request->pharmacy_list)
                ->delete();
            if ($all_accum_bene_strategy_names) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not found', 'false');
            }
        }
    }
}
