<?php

namespace App\Http\Controllers\ValidationLists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosisValidationListController extends Controller
{
    public function search(Request $request)
    {
        $data = DB::table('DIAGNOSIS_EXCEPTIONS as a')
        ->where(DB::raw('UPPER(a.DIAGNOSIS_LIST)'), 'like', '%' .strtoupper($request->search). '%')
        ->orWhere(DB::raw('UPPER(a.EXCEPTION_NAME)'), 'like', '%' .strtoupper($request->search). '%')
        ->get();

    return $this->respondWithToken($this->token(), '', $data);
    }

    public function getDiagnosisLimitations ($diagnosis_list)
        {
            $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                        ->where('DIAGNOSIS_LIST', 'like', '%' . $diagnosis_list . '%')
                        ->get();

            return $this->respondWithToken($this->token(), '', $data);

        }

        public function getDiagnosisCodeList($search=''){
            $data = DB::table('DIAGNOSIS_CODES')
            ->where(DB::raw('UPPER(DIAGNOSIS_ID)'),'like','%'.strtoupper($search).'%')
            ->orWhere(DB::raw('UPPER(DESCRIPTION)'),'like','%'.strtoupper($search).'%')
            ->get();
            return $this->respondWithToken($this->token(), '', $data);
        }

        public function getLimitationsCode($search=''){
            $data = DB::table('LIMITATIONS_LIST')
            ->where(DB::raw('UPPER(LIMITATIONS_LIST)'),'like','%'.strtoupper($search).'%')
            ->where(DB::raw('UPPER(LIMITATIONS_LIST_NAME)'),'like','%'.strtoupper($search).'%')
            ->get();
            return $this->respondWithToken($this->token(),'',$data);
        }

        public function getPriorityDiagnosis($diagnosis_list,$diagnosis_id){

            $data = DB::table('DIAGNOSIS_VALIDATIONS as a')
                        ->join('DIAGNOSIS_EXCEPTIONS as b','b.DIAGNOSIS_LIST','=','a.DIAGNOSIS_LIST')
                        ->where('a.DIAGNOSIS_LIST', 'like', '%' . $diagnosis_list . '%')
                        ->where('a.DIAGNOSIS_ID', 'like', '%' . $diagnosis_id . '%')
                        ->first();
                return $this->respondWithToken($this->token(),'',$data);
        }








}