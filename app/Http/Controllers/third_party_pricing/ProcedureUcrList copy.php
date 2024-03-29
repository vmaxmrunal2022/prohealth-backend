<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeUnit\FunctionUnit;

class ProcedureUcrList extends Controller
{
    public function get(Request $request)
    {
        $ucrName = DB::table('procedure_ucr_names')
            ->whereRaw('UPPER(PROCEDURE_UCR_ID) LIKE ?', ['%' . strtoupper($request->search) . '%'])
            ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ucrName);
    }

    public function getProcedureListData(Request $request)
    {
        $ucrList = DB::table('procedure_ucr_names')
            ->join('procedure_ucr_list', 'procedure_ucr_names.procedure_ucr_id', '=', 'procedure_ucr_list.procedure_ucr_id')
            ->where('procedure_ucr_list.procedure_ucr_id', $request->search)
            ->get();
        return $this->respondWithToken($this->token(), '', $ucrList);
    }

    public function submitProcedureList(Request $request)
    {
        $validate = DB::table('procedure_ucr_names')->where('procedure_ucr_id', $request->procedure_ucr_id)->get()->count();
        if ($validate <= "0") {
            if ($request->add_new) {
                $add_procedure_names = DB::table('procedure_ucr_names')
                    ->insert([
                        'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                        'DESCRIPTION' => $request->description
                    ]);

                $add_procedure_list = DB::table('PROCEDURE_UCR_LIST')
                    ->insert([
                        'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                        'procedure_code' => $request->procedure_code,
                        'effective_date' => $request->effective_date,
                        'termination_date' => $request->termination_date,
                        'unit_value' => $request->unit_value,
                        'UCR_CURRENCY' => $request->ucr_currency,
                    ]);
                return $this->respondWithToken($this->token(), 'Added Successfully!', $add_procedure_list);
            }
        } else {
            $update_procedure_names = DB::table('PROCEDURE_UCR_NAMES')
                ->where('procedure_ucr_id', $request->procedure_ucr_id)
                ->update([
                    // 'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    'DESCRIPTION' => $request->description
                ]);

            $update_procedure_list = DB::table('PROCEDURE_UCR_LIST')
                ->where('PROCEDURE_UCR_ID', $request->procedure_ucr_id)
                ->where('procedure_code', $request->procedure_code)
                ->update([
                    // 'PROCEDURE_UCR_ID' => $request->procedure_ucr_id,
                    // 'procedure_code' => $request->procedure_code,
                    'effective_date' => $request->effective_date,
                    'termination_date' => $request->termination_date,
                    'unit_value' => $request->unit_value,
                    'UCR_CURRENCY' => $request->ucr_currency,
                ]);
            // dd($update_procedure_list);
            return $this->respondWithToken($this->token(), 'Updated Successfully!', $update_procedure_list);
        }
    }

    public function getProcedureCode(Request $request)
    {
        $procedure_codes = DB::table('procedure_codes')
            ->get();
        return $this->respondWithToken($this->token(), '', $procedure_codes);
    }
}
