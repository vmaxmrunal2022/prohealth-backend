<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugClassController extends Controller
{
    public function search(Request $request)
    {
        $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
                ->where('DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('DRUG_CATGY_EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }


    public function DrugCategoryList(Request $request)
    {
        $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getTCList($ndcid)
    {
        $ndclist = DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getTCItemDetails($ndcid)
    {
        $ndc = DB::table('TC_EXCEPTION_LISTS')
                    ->select('TC_EXCEPTION_LISTS.*', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST as exception_list', 'TC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->join('TC_EXCEPTIONS', 'TC_EXCEPTIONS.THER_CLASS_EXCEPTION_LIST', '=', 'TC_EXCEPTION_LISTS.THER_CLASS_EXCEPTION_LIST')
                    ->where('TC_EXCEPTION_LISTS.therapy_class', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }

    public function getNDCItemDetails($ndcid){

        $ndc = DB::table('DRUG_CATGY_EXCEPTION_NAMES')
        // ->select('DRUG_CATGY_EXCEPTION_NAMES.*', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_NAME')
        ->join('PLAN_DRUG_CATGY_EXCEPTIONS', 'PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', '=', 'DRUG_CATGY_EXCEPTION_NAMES.DRUG_CATGY_EXCEPTION_LIST')
        ->where('PLAN_DRUG_CATGY_EXCEPTIONS.DRUG_CATGY_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')  ->get();
        return $this->respondWithToken($this->token(), '', $ndc);


    }

    public function add(Request $request)
    {
        
        $createddate = date('y-m-d');
        if ($request->new == 1)  {
            $drugcatgy = DB::table('DRUG_CATGY_EXCEPTION_NAMES')->insert(
                [
                    'drug_catgy_exception_list' => strtoupper($request->drug_catgy_exception_list),
                    'drug_catgy_exception_name' => $request->drug_catgy_exception_name,
                    'DATE_TIME_CREATED' => $createddate,
                    'USER_ID' => '', // TODO add user id
                    'DATE_TIME_MODIFIED' => '',
                    'USER_ID_CREATED' => '',
                    'FORM_ID' => ''
                ]
            );


            $plan=DB::table('PLAN_DRUG_CATGY_EXCEPTIONS')->insert(
                [
                    'PLAN_ID'=>$request->plan_id,
                    'SCATEGORY'=>$request->scategory,
                    'STYPE'=>$request->stype,
                    'NEW_DRUG_STATUS'=>$request->new_drug_status,
                    'PROCESS_RULE'=>$request->process_rule,
                    'MAXIMUM_ALLOWABLE_COST'=>$request->maximum_allowable_cost,
                    'PHYSICIAN_LIST'=>$request->physician_list,
                    'PHYSICIAN_SPECIALTY_LIST'=>$request->physician_speciality_list,
                    'PHARMACY_LIST'=>$request->pharmacy_list,
                    'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                    'PREFERRED_PRODUCT_NDC'=>$request->prefered_product_ndc,
                    'CONVERSION_PRODUCT_NDC'=>$request->conversion_product_ndc,
                    'ALTERNATE_PRICE_SCHEDULE'=>$request->alternate_price_schedule,
                    'ALTERNATE_COPAY_SCHED'=>$request->alternate_copay_sched,
                    'MESSAGE'=>$request->message,
                    'MESSAGE_STOP_DATE'=>$request->message_stop_date,
                    'MIN_RX_QTY'=>$request->min_rx_qty,
                    'MAX_RX_QTY'=>$request->max_rx_qty,
                    'MIN_RX_DAYS'=>$request->min_rx_days,
                    

                ]
            );


        

	return $this->respondWithToken($this->token(), 'Record added Successfully!', $drugcatgy);


        } else  if($request->new == 0){
            $benefitcode = DB::table('benefit_codes')
                                ->where('benefit_code', $request->benefit_code)
                                ->update(
                                    [
                                        'benefit_code' => strtoupper($request->benefit_code),
                                        'description' => $request->description,
                                        'DATE_TIME_CREATED' => $createddate,
                                        'USER_ID' => '', // TODO add user id
                                        'DATE_TIME_MODIFIED' => '',
                                        'USER_ID_CREATED' => '',
                                        'FORM_ID' => ''
                                    ]
                            );

           

            $benefitcode = DB::table('benefit_codes' ) ->where('benefit_code', 'like', '%' . $request->benefit_code. '%')->first();


        
        }


        return $this->respondWithToken($this->token(), 'Record updated Successfully! ', $benefitcode);
    }
}
