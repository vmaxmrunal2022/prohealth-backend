<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

class ProcedureCrossReferenceController extends Controller
{
    public function ProcedureCodes(Request $request)
    {

        $codes = DB::table('PROCEDURE_CODES')
            ->select('PROCEDURE_CODES.PROCEDURE_CODE', 'PROCEDURE_CODES.DESCRIPTION')
            ->get();

        return $this->respondWithToken($this->token(), '', $codes);
    }

    public function search(Request $request)
    {
        return "success";
        $ndc = DB::table('PROCEDURE_XREF')
            ->where('procedure_xref_id', 'like', '%' . strtoupper($request->search) . '%')
            // ->orWhere('LIMITATIONS_LIST_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function details($id)
    {

        $limitationDetails = DB::table('PROCEDURE_XREF')
            ->where('procedure_xref_id', $id)
            ->first();
        return $this->respondWithToken($this->token(), '', $limitationDetails);
    }

    public function add(Request $request)
    {

        $createddate = date('y-m-d');

        $recordcheck = DB::table('PROCEDURE_XREF')
            ->where('PROCEDURE_XREF_ID', strtoupper($request->procedure_xref_id))
            ->first();


        if ($request->has('new')) {


            if ($recordcheck) {
                return $this->respondWithToken($this->token(), 'Limitation List ID Already Exists', $recordcheck);
            } else {


                $insert = DB::table('PROCEDURE_XREF')->insert(
                    [
                        "PROCEDURE_XREF_ID" => $request->procedure_xref_id,
                        "SUB_PROCEDURE_CODE" => $request->sub_procedure_code,
                        "HIST_PROCEDURE_CODE" => $request->hist_procedure_code,
                        "EFFECTIVE_DATE" => $request->effective_date,
                        "TERMINATION_DATE" => $request->termination_date,
                        "TOOTH_OPT" => $request->tooth_opt,
                        "SURFACE_OPT" => $request->surface_opt,
                        "QUADRANT_OPT" => $request->quadrant_opt,
                        "NEW_DRUG_STATUS" => $request->new_drug_status,
                        "MESSAGE" => $request->message,
                        "MESSAGE_STOP_DATE" => $request->message_stop_date,
                        "USER_ID_CREATED" => $request->user_id_created,
                        "DATE_TIME_CREATED" => $request->date_time_created,
                        "USER_ID_MODIFIED" => $request->user_id_modified,
                        "DATE_TIME_MODIFIED" => $request->date_time_modified,
                        "FORM_ID" => $request->form_id,


                    ]
                );
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $insert);
            }
        } else {

            $update = DB::table('PROCEDURE_XREF')
                ->where('procedure_xref_id', strtoupper($request->procedure_xref_id))
                ->update(
                    [
                        "SUB_PROCEDURE_CODE" => $request->sub_procedure_code,
                        "HIST_PROCEDURE_CODE" => $request->hist_procedure_code,
                        "EFFECTIVE_DATE" => $request->effective_date,
                        "TERMINATION_DATE" => $request->termination_date,
                        "TOOTH_OPT" => $request->tooth_opt,
                        "SURFACE_OPT" => $request->surface_opt,
                        "QUADRANT_OPT" => $request->quadrant_opt,
                        "NEW_DRUG_STATUS" => $request->new_drug_status,
                        "MESSAGE" => $request->message,
                        "MESSAGE_STOP_DATE" => $request->message_stop_date,
                        "USER_ID_CREATED" => $request->user_id_created,
                        "DATE_TIME_CREATED" => $request->date_time_created,
                        "USER_ID_MODIFIED" => $request->user_id_modified,
                        "DATE_TIME_MODIFIED" => $request->date_time_modified,
                        "FORM_ID" => $request->form_id,

                    ]
                );


            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
        }
    }
}
