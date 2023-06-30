<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DiagnosisController extends Controller
{

    use AuditTrait;
    public function all(Request $request)
    {

        $benefitcodes = DB::table('DIAGNOSIS_CODES')
                            ->select('diagnosis_id','description')
                            ->paginate(100);
        return $this->respondWithToken($this->token(), '', $benefitcodes);
    }

    public function allNew(Request $request)
    {
        $searchQuery = $request->search;
        $benefitcodes = DB::table('DIAGNOSIS_CODES')
                            ->select('diagnosis_id','description')
                            ->when($searchQuery, function ($query) use ($searchQuery) {
                                $query->where(DB::raw('UPPER(DIAGNOSIS_ID)'), 'like', '%' . strtoupper($searchQuery) . '%');
                                $query->orWhere(DB::raw('UPPER(DESCRIPTION)'), 'like', '%' . strtoupper($searchQuery) . '%');
                             })->paginate(100);
        return $this->respondWithToken($this->token(), '', $benefitcodes);
    }

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'search' => ['required']
        ]);
        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $benefitcodes = DB::table('DIAGNOSIS_CODES')
                // ->where(DB::raw('UPPER(diagnosis_id)'), 'like', '%' . strtoupper($request->search) . '%')

                ->whereRaw('LOWER(DIAGNOSIS_ID) LIKE ?', ['%' . strtolower($request->search) . '%'])                // ->orWhere(DB::raw('UPPER(description)'), 'like', '%' . $request->search . '%')
                // ->where(DB::raw('UPPER(diagnosis_id)'), 'like', '%' . strtoupper($request->search) . '%')

                ->get();

            return $this->respondWithToken($this->token(),'', $benefitcodes);
        }
    }







    public function getLimitations(Request $request)
    {
        $benefitcodes = DB::table('LIMITATIONS_LIST')
            ->where('LIMITATIONS_LIST', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('LIMITATIONS_LIST_NAME', 'like', '%' . $request->search . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $benefitcodes);
    }

    public function add(Request $request)
    {
        if ($request->new) {
            // $validator = Validator::make($request->all(), [
            //     'diagnosis_id' => ['required', 'max:8', Rule::unique('DIAGNOSIS_CODES')->where(function ($q) {
            //         $q->whereNotNull('diagnosis_id');
            //     })],

                $validator = Validator::make($request->all(), [
                    'diagnosis_id' => ['required', 'max:8', Rule::unique('DIAGNOSIS_CODES')->where(function ($q) {
                        $q->whereNotNull('diagnosis_id');
                    })],
                "description" => ['max:35'],
                // 'complete_code_ind' => ['required'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $benefitcode = DB::table('DIAGNOSIS_CODES')->insert(
                    [
                        'DIAGNOSIS_ID' => strtoupper($request->diagnosis_id),
                        'DESCRIPTION' => $request->description,
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'USER_ID_CREATED' => '',
                        'FORM_ID' => '',
                        'COMPLETE_CODE_IND' => $request->complete_code_ind,
                    ]
                );
                $code = DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))->where('DESCRIPTION', strtoupper($request->description))->first();
                return  $this->respondWithToken($this->token(), 'Record Added Successfully', $code);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'diagnosis_id' => ['required', 'max:8'],
                "description" => ['max:35'],
                // 'complete_code_ind' => ['required'],
            ]);
            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $benefitcode = DB::table('DIAGNOSIS_CODES')
                    // ->where(DB::raw('UPPER(DIAGNOSIS_ID)'), strtoupper($request->diagnosis_code))
                    ->where('DIAGNOSIS_ID', $request->diagnosis_id)
                    ->update(
                        [
                            'DESCRIPTION' => $request->description,
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'USER_ID_CREATED' => '',
                            'FORM_ID' => '',
                            'COMPLETE_CODE_IND' => $request->complete_code_ind,
                        ]
                    );
                // $code = DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))->where('DESCRIPTION', strtoupper($request->description))->first();
                return  $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
            }
        }
        // $code = DB::table('DIAGNOSIS_CODES')->where('DIAGNOSIS_ID', strtoupper($request->diagnosis_id))->where('DESCRIPTION', strtoupper($request->description))->first();

    }

    public function delete(Request $request)
    {
        if (isset($request->diagnosis_id)) {
            $delete_diagnosis_code =  DB::table('DIAGNOSIS_CODES')
                ->where('DIAGNOSIS_ID', $request->diagnosis_id)
                ->delete();
            if ($delete_diagnosis_code) {
                return $this->respondWithToken($this->token(), 'Record Deleted Successfully');
            } else {
                return $this->respondWithToken($this->token(), 'Record Not Found');
            }
        } else {
            return $this->respondWithToken($this->token(), 'Record Not Found');
        }
    }

    public function checkDiagnosisCodeExist(Request $request)
    {
        $check_diagnosis_exist = DB::table('DIAGNOSIS_CODES')
            // ->where(DB::raw('UPPER(diagnosis_id)'), strtoupper($request->diagnosis_id))
            ->where('DIAGNOSIS_ID', $request->search)
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $check_diagnosis_exist);
    }
}
