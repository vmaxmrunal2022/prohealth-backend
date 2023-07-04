<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class PrescriberValidationController extends Controller
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
            $physicianExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                // ->where('PHYSICIAN_LIST', 'like', '%' . $request->search . '%')
                ->whereRaw('LOWER(PHYSICIAN_LIST) LIKE ?', ['%' . strtolower($request->search) . '%'])
                // ->where('PHYSICIAN_LIST', 'like', '%' . $request->search . '%')
                ->whereRaw('LOWER(PHYSICIAN_LIST) LIKE ?', ['%' . strtolower($request->search) . '%'])
                ->orWhere('EXCEPTION_NAME', 'like', '%' . $request->search . '%')
                ->orderBy('PHYSICIAN_LIST', 'ASC')
                ->get();
            return $this->respondWithToken($this->token(), '', $physicianExceptionData);
        }
    }

    public function getProviderValidationList($physician_list)
    {
        // $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS as a')
        //     // ->select('a.PHYSICIAN_VALIDATIONS', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME','a.EXCEPTION_NAME')
        //     // ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.PHYSICIAN_LAST_NAME', 'b.PHYSICIAN_FIRST_NAME', 'a.EXCEPTION_NAME')
        //     ->join('PHYSICIAN_TABLE as b ', 'b.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
        //     ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
        //     ->where('a.PHYSICIAN_LIST', 'like', '%' . $physician_list . '%')
        //     ->get();
        $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS')
            ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
            ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
            ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $physician_list)
            ->distinct()
            ->get();

        return $this->respondWithToken($this->token(), '', $physician_validation_list);
    }


    public function getProviderDetails($physicain_list, $physicain_id)
    {
        $data = DB::table('PHYSICIAN_VALIDATIONS as a')
            ->select('a.PHYSICIAN_LIST', 'a.PHYSICIAN_ID', 'a.PHYSICIAN_STATUS', 'b.EXCEPTION_NAME', 'c.PHYSICIAN_LAST_NAME', 'c.PHYSICIAN_FIRST_NAME')
            ->join('PHYSICIAN_EXCEPTIONS as b', 'b.PHYSICIAN_LIST', '=', 'a.PHYSICIAN_LIST')
            ->join('PHYSICIAN_TABLE as c', 'c.PHYSICIAN_ID', '=', 'a.PHYSICIAN_ID')
            ->where('a.PHYSICIAN_LIST', $physicain_list)
            ->where('a.PHYSICIAN_ID', $physicain_id)
            ->first();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function addPrescriberDatacopy(Request $request)
    {

        $getProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->first();

        $getProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('PHYSICIAN_LIST', $request->physician_list)
            ->where('PHYSICIAN_ID', $request->physician_id)
            ->first();

        $recordcheck = DB::table('PHYSICIAN_VALIDATIONS')
            ->where('physician_id', $request->physician_id)
            ->first();

        if ($request->has('new')) {

            if (!$request->updateForm) {
                $ifExist = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where(DB::raw('physician_list'), strtoupper($request->physician_list))
                    ->get();

                if (count($ifExist) >= 1) {
                    return $this->respondWithToken($this->token(), [["Prescriber List ID already exists"]], '', false);
                }
            } else {
            }


            if (!$getProviderExceptionData && !$getProviderValidationData) {

                $addProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->insert([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'EXCEPTION_NAME' => $request->exception_name,
                        'USER_ID' => $request->user_name,
                        'DATE_TIME_CREATED' => date('d-M-y')
                    ]);

                $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                    ->insert([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        'PHYSICIAN_ID' => $request->physician_id,
                        'PHYSICIAN_STATUS' => $request->physician_status,
                        'USER_ID' => $request->user_name,
                        'DATE_TIME_CREATED' => date('d-M-y')
                    ]);

                if ($addProviderExceptionData) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $addProviderExceptionData);
                }
            }
        } else if ($request->updateForm == 'update') {


            $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('PHYSICIAN_LIST', $request->physician_list)
                ->update([
                    'EXCEPTION_NAME' => $request->exception_name,
                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                ]);

            if ($updateProviderExceptionData) {
                $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where('PHYSICIAN_ID', $request->physician_id)
                    ->where('PHYSICIAN_LIST', $request->physician_list)


                    ->update([
                        'PHYSICIAN_LIST' => $request->physician_list,
                        // 'PHYSICIAN_ID' => $request->physician_id,
                        'PHYSICIAN_STATUS' => $request->physician_status,
                        'DATE_TIME_CREATED' => date('d-M-y'),
                        'USER_ID' => $request->user_name
                    ]);
                if ($addProviderValidationData) {
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $addProviderValidationData);
                }
            }
        }
    }

    public function addPrescriberData(Request $request)
    {


        $createddate = date('d-M-y');
        $validation = DB::table('PHYSICIAN_EXCEPTIONS')
            ->where('physician_list', $request->physician_list)
            ->get();

        if ($request->new) {
            $validator = Validator::make($request->all(), [
                // 'physician_list' => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('physician_list');
                // })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('PHYSICIAN_EXCEPTIONS')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "exception_name" => ['required', 'max:36'],
                "physician_id" => ['required_if:copy,0', 'max:10'],
                "physician_id" => ['required_if:copy,0', 'max:10'],
                "physician_status" => ['max:10'],
                "DATE_TIME_CREATED" => ['max:10'],
                "DATE_TIME_MODIFIED" => ['max:10']



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                //to copy prescriber
                if ($request->copy) {

                    $ifExist = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->destination_id))
                        ->get();
                    $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                        ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                        ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                        ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                        ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                        ->distinct()
                        ->get();
                    $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                        ->get();
                    if (count($ifExist) >= 1) {
                        return $this->respondWithToken($this->token(), [["Prescriber List ID already exists"]], [$diag_validation, $ifExist], false);
                    }

                    $source_validations = DB::table('PHYSICIAN_VALIDATIONS')
                        ->where(function ($query) use ($request) {
                            if (isset($request->source_id)) {
                                return $query->where(DB::raw('UPPER(physician_list)'), strtoupper($request->source_id));
                            }
                        })
                        ->get();
                    $add_destination = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->insert([
                            'physician_list' => $request->destination_id,
                            'EXCEPTION_NAME' => $request->exception_name,
                            'date_time_created' => date('d-M-y'),
                            'user_id' => Cache::get('userId'),
                            'date_time_modified' => date('d-M-y'),
                            'form_id' => ''
                        ]);
                    $get_parent = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->destination_id))
                        ->first();
                    $save_audit_parent = $this->auditMethod('IN', json_encode($get_parent), 'PHYSICIAN_EXCEPTIONS');

                    foreach ($source_validations as $child) {
                        $add_destination_child = DB::table('PHYSICIAN_VALIDATIONS')
                            ->insert([
                                'PHYSICIAN_LIST' => $request->destination_id,
                                'PHYSICIAN_ID' => $child->physician_id,
                                'PHYSICIAN_STATUS' => $child->physician_status,
                                'date_time_created' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);

                        $get_parent = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->destination_id))
                            ->first();
                        $save_audit_parent = $this->auditMethod('IN', json_encode($get_parent), 'PHYSICIAN_EXCEPTIONS');

                        $get_child = DB::table('PHYSICIAN_VALIDATIONS')
                            ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->destination_id))
                            ->where(DB::raw('UPPER(physician_id)'), strtoupper($child->physician_id))
                            ->first();
                        $save_audit_child = $this->auditMethod('IN', json_encode($get_child), 'PHYSICIAN_VALIDATIONS');
                    }

                    return $this->respondWithToken(
                        $this->token(),
                        "$request->destination_id Has Been Cloned Successfully",
                        [$diag_validation, $diag_exception],
                    );
                }

                if (!$request->updateForm) {
                    $ifExist = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                        ->get();
                    if (count($ifExist) >= 1) {
                        return $this->respondWithToken($this->token(), [["Prescriber List ID already exists"]], '', false);
                    }
                } else {
                }
                if ($request->physician_list && $request->physician_id) {
                    $count = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                        ->get()
                        ->count();
                    if ($count <= 0) {
                        $add_names = DB::table('PHYSICIAN_EXCEPTIONS')->insert(
                            [
                                'physician_list' => $request->physician_list,
                                'EXCEPTION_NAME' => $request->exception_name,
                                'date_time_created' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]
                        );
                        $add = DB::table('PHYSICIAN_VALIDATIONS')
                            ->insert([
                                'PHYSICIAN_LIST' => $request->physician_list,
                                'PHYSICIAN_ID' => $request->physician_id,
                                'PHYSICIAN_STATUS' => $request->physician_status,
                                'date_time_created' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);

                        $get_child = DB::table('PHYSICIAN_VALIDATIONS')
                            ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->destination_id))
                            ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                            ->first();
                        $save_audit_parent = $this->auditMethod('IN', json_encode($get_child), 'PHYSICIAN_VALIDATIONS');

                        $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                            ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                            ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                            ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                            ->distinct()
                            ->get();
                        $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                            ->get();

                        // $add = DB::table('PHYSICIAN_VALIDATIONS')->where('physician_list', 'like', '%' . $request->physician_list . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', [[], []]);
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', [[], []]);
                    } else {
                        $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->where('PHYSICIAN_LIST', $request->physician_list)
                            ->update([
                                'exception_name' => $request->exception_name,
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                'form_id' => ''
                            ]);
                        $get_parent = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->destination_id))
                            ->first();
                        $save_audit = $this->auditMethod('UP', json_encode($get_parent), 'PHYSICIAN_EXCEPTIONS');
                        $countValidation = DB::table('PHYSICIAN_VALIDATIONS')
                            ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), strtoupper($request->physician_list))
                            ->where(DB::raw('UPPER(PHYSICIAN_ID)'), strtoupper($request->physician_id))
                            ->get();

                        $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                            ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                            ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                            ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                            ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                            ->distinct()
                            ->get();
                        $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                            ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                            ->get();

                        if (count($countValidation) >= 1) {
                            return $this->respondWithToken(
                                $this->token(),
                                [['Physician ID already exists']],
                                [$diag_validation, $diag_exception],
                                false
                            );
                        } else {
                            $addProviderValidationData = DB::table('PHYSICIAN_VALIDATIONS')
                                ->insert([
                                    'physician_list' => $request->physician_list,
                                    'physician_id' => $request->physician_id,
                                    'physician_status' => $request->physician_status,
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                    'USER_ID' => Cache::get('userId'),
                                    'date_time_modified' => date('d-M-y'),
                                    'form_id' => ''
                                ]);
                            $get_parent = DB::table('PHYSICIAN_VALIDATIONS')
                                ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->destination_id))
                                ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                                ->first();
                            $save_audit = $this->auditMethod('IN', json_encode($get_parent), 'PHYSICIAN_VALIDATIONS');
                            $reecord = DB::table('PHYSICIAN_EXCEPTIONS')
                                ->join('PHYSICIAN_VALIDATIONS', 'PHYSICIAN_EXCEPTIONS.physician_list', '=', 'PHYSICIAN_VALIDATIONS.physician_list')
                                ->where('PHYSICIAN_VALIDATIONS.physician_list', $request->physician_list)
                                ->where('PHYSICIAN_VALIDATIONS.physician_id', $request->physician_id)
                                ->first();
                            $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                                ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                                ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                                ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                                ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                                ->distinct()
                                ->get();
                            $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                                ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                                ->get();
                            return $this->respondWithToken(
                                $this->token(),
                                'Record Added Successfully',
                                [[], []],
                                [[], []],
                                201
                            );
                        }
                    }
                }
            }
        } else {
            $updateProviderExceptionData = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('physician_list', $request->physician_list)
                ->update([
                    'exception_name' => $request->exception_name,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);
            $get_exp = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('physician_list', $request->physician_list)
                ->first();
            $this->auditMethod('UP', json_encode($get_exp), 'PHYSICIAN_EXCEPTIONS');

            $countValidation = DB::table('PHYSICIAN_VALIDATIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                // ->where('pharmacy_status', $request->pharmacy_status)
                ->update([
                    'physician_status' => $request->physician_status,
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $get_val = DB::table('PHYSICIAN_VALIDATIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                ->first();
            $saveh_audit_val = $this->auditMethod('UP', json_encode($get_val), 'PHYSICIAN_VALIDATIONS');

            $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                ->distinct()
                ->get();
            $diag_exception = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where(DB::raw('UPPER(PHYSICIAN_LIST)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                ->get();
            return $this->respondWithToken(
                $this->token(),
                'Record Updated Successfully',
                [$diag_validation, $diag_exception],
                true,
                201
            );
        }
    }

    public function searchDropDownPrescriberList()
    {
        $data = DB::table('PHYSICIAN_TABLE')
            ->orWhere('PHYSICIAN_LAST_NAME', 'LIKE', '%' . strtoupper('campB') . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function searchDropDownPrescriberListNew(Request $request)
    {
        $data = DB::table('PHYSICIAN_TABLE')
            ->whereRaw('LOWER(PHYSICIAN_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->orWhereRaw('LOWER(PHYSICIAN_LAST_NAME) LIKE ?', ['%' . strtolower($request->search) . '%'])
            ->paginate(100);

        return $this->respondWithToken($this->token(), '', $data);
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
            $get_exp = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($data[0]['physician_list']))
                ->first();
            $save_audit_val = $this->auditMethod('DE', json_encode($get_exp), 'PHYSICIAN_EXCEPTIONS');
            $delete_physician_id = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($data[0]['physician_list']))
                ->delete();

            $get_val = DB::table('PHYSICIAN_VALIDATIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($data[0]['physician_list']))
                ->first();
            $save_audit_val = $this->auditMethod('DE', json_encode($get_val), 'PHYSICIAN_VALIDATIONS');
            $delete_physician_id = DB::table('PHYSICIAN_VALIDATIONS')
                ->where(DB::raw('UPPER(physician_list)'), strtoupper($data[0]['physician_list']))
                ->delete();
            $diagnosis_exception =
                DB::table('PHYSICIAN_EXCEPTIONS')
                ->where(DB::raw('UPPER(physician_list)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                ->get();
            $physician_validation_list = DB::table('PHYSICIAN_VALIDATIONS')
                ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                ->distinct()
                ->get();
            return $this->respondWithToken($this->token(), "Record Deleted Successfully1", $physician_validation_list);
        } else        
        if ($request->physician_list) {
            if ($request->physician_id) {
                $get_val = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                    ->first();
                $save_val = $this->auditMethod('DE', json_encode($get_val), 'PHYSICIAN_VALIDATIONS');
                $delete_physician_id = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                    ->delete();
                $diagnosis_validation = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->get();
                $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                    ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                    ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                    ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                    ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                    ->distinct()
                    ->get();
                if (count($diagnosis_validation) <= 0) {
                    $get_exp = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                        ->first();
                    $this->auditMethod('DE', json_encode($get_exp), 'PHYSICIAN_EXCEPTIONS');
                    $delete_physician_list = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                        ->delete();
                    $diagnosis_validation1 = DB::table('PHYSICIAN_EXCEPTIONS')
                        ->where(DB::raw('UPPER(physician_list)'), 'like', '%' . strtoupper($request->physician_list) . '%')->get();
                    $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                        ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                        ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                        ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                        ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                        ->distinct()
                        ->get();
                    return $this->respondWithToken($this->token(), "Parent and Child Deleted Successfully", $diag_validation, false);
                }
                return $this->respondWithToken($this->token(), "Record Deleted Successfully", $diag_validation);
            } else {
                $get_exp = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->first();
                $this->auditMethod('DE', json_encode($get_exp), 'PHYSICIAN_EXCEPTIONS');
                $delete_physician_id = DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->delete();
                $get_val = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->where(DB::raw('UPPER(physician_id)'), strtoupper($request->physician_id))
                    ->first();
                $save_val = $this->auditMethod('DE', json_encode($get_val), 'PHYSICIAN_VALIDATIONS');
                $delete_physician_id = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where(DB::raw('UPPER(physician_list)'), strtoupper($request->physician_list))
                    ->delete();
                $diagnosis_exception =
                    DB::table('PHYSICIAN_EXCEPTIONS')
                    ->where(DB::raw('UPPER(physician_list)'), 'like', '%' . strtoupper($request->physician_list) . '%')
                    ->get();
                $diag_validation = DB::table('PHYSICIAN_VALIDATIONS')
                    ->select('PHYSICIAN_VALIDATIONS.*', 'PHYSICIAN_EXCEPTIONS.*', 'PHYSICIAN_TABLE.*')
                    ->join('PHYSICIAN_TABLE', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_ID', '=', 'PHYSICIAN_TABLE.PHYSICIAN_ID')
                    ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', '=', 'PHYSICIAN_EXCEPTIONS.PHYSICIAN_LIST')
                    ->where('PHYSICIAN_VALIDATIONS.PHYSICIAN_LIST', $request->physician_list)
                    ->distinct()
                    ->get();
                return $this->respondWithToken($this->token(), "Record Deleted Successfully", '');
            }
        }
    }


    public function deleteRecord(Request $request)
    {
        if (isset($request->physician_list)  && isset($request->physician_id)) {
            $all_physician = DB::table('PHYSICIAN_VALIDATIONS')
                ->where('physician_list', $request->physician_list)
                ->where('physician_id', $request->physician_id)
                ->first();
            if ($all_physician) {
                $physician_list = DB::table('PHYSICIAN_VALIDATIONS')
                    ->where('physician_list', $request->physician_list)
                    ->where('physician_id', $request->physician_id)
                    ->delete();
                if ($physician_list) {
                    $val = DB::table('PHYSICIAN_VALIDATIONS')
                        // ->join('PHYSICIAN_EXCEPTIONS', 'PHYSICIAN_VALIDATIONS.pricing_strategy_id', '=', 'PHYSICIAN_EXCEPTIONS.pricing_strategy_id')
                        ->where('PHYSICIAN_VALIDATIONS.physician_list', $request->physician_list)
                        ->count();
                    return $this->respondWithToken($this->token(), 'Record Deleted Successfully ', $val);
                }
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found', 'false');
            }
        } elseif (isset($request->physician_list)) {
            $physician_exceptions = DB::table('PHYSICIAN_EXCEPTIONS')
                ->where('physician_list', $request->physician_list)
                ->delete();
            $physician_validations = DB::table('PHYSICIAN_VALIDATIONS')
                ->where('physician_list', $request->physician_list)
                ->delete();
            if ($physician_exceptions) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully', $physician_validations, true, 201);
            } else {
                return $this->respondWithToken($this->token(), 'Record Not found', 'false');
            }
        }
    }
}
