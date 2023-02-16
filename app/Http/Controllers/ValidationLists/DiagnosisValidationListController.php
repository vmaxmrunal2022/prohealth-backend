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
        // ->join('DIAGNOSIS_VALIDATIONS as b','b.DIAGNOSIS_LIST','=','a.DIAGNOSIS_LIST')
        ->where(DB::raw('UPPER(a.DIAGNOSIS_LIST)'), 'like', '%' .strtoupper($request->search). '%')
        ->orWhere(DB::raw('UPPER(a.EXCEPTION_NAME)'), 'like', '%' .strtoupper($request->search). '%')
        // ->groupBy('DIAGNOSIS_LIST')
        ->get();

    return $this->respondWithToken($this->token(), '', $data);
    }

    public function getPriorityDiagnosis ($diagnosis_list)
        {
            $data = DB::table('DIAGNOSIS_VALIDATIONS as a')
                        ->join('DIAGNOSIS_EXCEPTIONS as b','b.DIAGNOSIS_LIST','=','a.DIAGNOSIS_LIST')
                        ->where('a.DIAGNOSIS_LIST', 'like', '%' . $diagnosis_list . '%')
                        ->orderBy('PRIORITY','ASC')
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

        public function getDiagnosisLimitations($diagnosis_list,$diagnosis_id){
            $data = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC as a')
                        ->where('a.DIAGNOSIS_LIST', '=',  $diagnosis_list )
                        ->where('a.DIAGNOSIS_ID', '=', $diagnosis_id)
                        ->get();
                return $this->respondWithToken($this->token(),'',$data);
        }

        public function addDiagnosisValidations(Request $request){
            // dd($request->all());
            if($request->has('new')){


                $getEligibilityData = DB::table('DIAGNOSIS_EXCEPTIONS')
                ->where('diagnosis_list',strtoupper($request->diagnosis_list))
                ->where('exception_name',strtoupper($request->exception_name))
                ->first();

                $getvalidData = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where('diagnosis_list',strtoupper($request->diagnosis_list))
                ->first();

                // dd($getEligibilityData);


            
                if($getEligibilityData){

                    return $this->respondWithToken($this->token(), 'This record already exists in the system..!!!', $getEligibilityData);
    
    
                }

                else{

                    $addData = DB::table('DIAGNOSIS_EXCEPTIONS')
                    ->insert([
                        'DIAGNOSIS_LIST'=>strtoupper($request->diagnosis_list),
                        'EXCEPTION_NAME'=>strtoupper($request->exception_name),
                        'DATE_TIME_CREATED'=>date('d-M-y'),
                        'USER_ID'=>$request->user_name,
                    ]);


                    $addValidate = DB::table('DIAGNOSIS_VALIDATIONS')
                  
                    ->insert([
                        'DIAGNOSIS_LIST'=>strtoupper($request->diagnosis_list),
                        'DIAGNOSIS_ID'=>strtoupper($request->diagnosis_id),
                        'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                        'PRIORITY' => $request->priority,
                        'DATE_TIME_MODIFIED'=>date('d-M-y'),
                        'USER_ID_MODIFIED'=>$request->user_name
                    ]);

                    $addlimitations = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                    ->insert([
                        'DIAGNOSIS_LIST'=>strtoupper($request->diagnosis_list),
                        'DIAGNOSIS_ID'=>strtoupper($request->diagnosis_id),
                        'LIMITATIONS_LIST'=>$request->limitation_list,
                        'EFFECTIVE_DATE'=>date('Ydm',strtotime($request->effective_date)),
                        'TERMINATION_DATE'=>date('Ydm',strtotime($request->termination_date)),
                        'DATE_TIME_CREATED'=>date('d-M-y'),
                        'USER_ID_CREATED'=>$request->user_name

                    ]);
                    if($addlimitations){
                        return $this->respondWithToken($this->token(),'Added Succcessfully Limitation!!!');
                    }
    
                    if($addData){
                        return $this->respondWithToken($this->token(),'Added Successfully!!!',$addData);
                    }

                }




              
            }else{

                if($request->updateForm == 'update'){

                    $updateData = DB::table('DIAGNOSIS_EXCEPTIONS')
                ->where('DIAGNOSIS_LIST',$request->diagnosis_list)
                ->update([
                    'EXCEPTION_NAME'=>$request->exception_name,
                    'DATE_TIME_MODIFIED'=>date('d-M-y'),
                ]);

                if(isset($request->diagnosis_id)){
                    $updateDataValid = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where('DIAGNOSIS_LIST',$request->diagnosis_list)
                ->where('DIAGNOSIS_ID',$request->diagnosis_id)
                ->update([
                    'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                    'PRIORITY' => $request->priority,
                    'DATE_TIME_MODIFIED'=>date('d-M-y'),
                    'USER_ID_MODIFIED'=>$request->user_name
                ]);
                }
                if($updateData){
                    return $this->respondWithToken($this->token(),'Update Successfully!!!', $updateData);
                }
                }else{

                    if(isset($request->diagnosis_id)){
                        $addDataValid = DB::table('DIAGNOSIS_VALIDATIONS')
                        ->insert([
                            'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                            'DIAGNOSIS_ID'=> $request->diagnosis_id['value'],
                            'DIAGNOSIS_STATUS' => $request->diagnosis_status,
                            'PRIORITY' => $request->priority,
                            'DATE_TIME_CREATED'=>date('d-M-y'),
                            'USER_ID'=>$request->user_name,

                        ]);
                    }
                    if($addDataValid){
                        return $this->respondWithToken($this->token(),'Added Successfully Diagnosis ID!!!', $addDataValid);
                    }

                }

            }

        }


        public function DiagnosisLimitationAdd(Request $request){

            if($request->has('new')){
                $addData = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC')
                        ->insert([
                            'DIAGNOSIS_LIST'=>$request->diagnosis_list,
                            'DIAGNOSIS_ID'=>$request->diagnosis_id,
                            'LIMITATIONS_LIST'=>$request->limitation_list,
                            'EFFECTIVE_DATE'=>date('Ydm',strtotime($request->effective_date)),
                            'TERMINATION_DATE'=>date('Ydm',strtotime($request->termination_date)),
                            'DATE_TIME_CREATED'=>date('d-M-y'),
                            'USER_ID_CREATED'=>$request->user_name

                        ]);
                        if($addData){
                            return $this->respondWithToken($this->token(),'Added Succcessfully Limitation!!!');
                        }
            }
            else{

                $update = DB::table('DIAGNOSIS_LIMITATIONS_ASSOC' )
                ->where( 'DIAGNOSIS_LIST', $request->diagnosis_list)
                ->where('DIAGNOSIS_ID',$request->diagnosis_id)
                ->update(
                    [
                        'LIMITATIONS_LIST' =>$request->limitations_list,
                        'EFFECTIVE_DATE'=>$request->effective_date,
                        'TERMINATION_DATE'=>$request->termination_date,
                    ]
                );
    
    
                $benefitcode = DB::table('CLIENT' ) ->where('client_id', 'like', '%' . $request->client_id. '%')->first();
    

            }
        }

        public function getDiagnosisValidations($diagnosis_list){
            $getData = DB::table('DIAGNOSIS_VALIDATIONS')
                ->where('DIAGNOSIS_LIST',$diagnosis_list)
                ->get();
                return $this->respondWithToken($this->token(),'',$getData);
        }

        public function getDiagnosisDetails($diagnosis_list,$diagnosis_id){
            $data = DB::table('DIAGNOSIS_VALIDATIONS as a')
            ->join('DIAGNOSIS_EXCEPTIONS as b','b.DIAGNOSIS_LIST','=','a.DIAGNOSIS_LIST')
            ->where('a.DIAGNOSIS_LIST',$diagnosis_list)
            ->where('a.DIAGNOSIS_ID',$diagnosis_id)
            ->first();
            return $this->respondWithToken($this->token(),'',$data);
        }


        public function updatePriorityDiagnosisValidation(Request $request){
            $data = DB::table('DIAGNOSIS_VALIDATIONS')
            ->where('DIAGNOSIS_LIST',$request->diagnosis_list)
            ->where('DIAGNOSIS_ID',$request->diagnosis_id)
            ->update([
                'PRIORITY'=>$request->priority
            ]);
            if($data){
                return $this->respondWithToken($this->token(),'updatd successfully',$data);
            }
        }


}