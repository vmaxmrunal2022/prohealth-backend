<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperBenefitControler extends Controller
{      



    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        $effective_date = date('Ymd', strtotime($request->effective_date));
        $terminate_date = date('Ymd', strtotime($request->termination_date));


        $recordcheck = DB::table('SUPER_BENEFIT_LIST_NAMES')
        ->where('super_benefit_list_id', strtoupper($request->super_benefit_list_id))
        ->first();


        if ( $request->has( 'new' ) ) {

            if($recordcheck){
                return $this->respondWithToken($this->token(), 'Super Benefit List Id  Already Exists', $recordcheck);

            }

            else{


                $accum_benfit_stat_names = DB::table('SUPER_BENEFIT_LIST_NAMES')->insert(
                    [
                        'super_benefit_list_id' => strtoupper( $request->super_benefit_list_id ),
                        'description'=>$request->description
                        
    
                    ]
                );
    
    
                $accum_benfit_stat = DB::table('SUPER_BENEFIT_LISTS' )->insert(
                    [
                        'super_benefit_list_id'=>strtoupper( $request->super_benefit_list_id ),
    
                        'benefit_list_id'=>$request->benefit_list_id,
                        'accum_benefit_strategy_id'=>$request->accum_benefit_strategy_id,
                        'effective_date'=>$effective_date,
                        'termination_date'=>$terminate_date,
    
    
                    ]
                );
    
                if ($accum_benfit_stat) {
                    return $this->respondWithToken($this->token(), 'Recored Added Successfully', $accum_benfit_stat);
                }

            }




         
        } else {


            $benefitcode = DB::table('SUPER_BENEFIT_LIST_NAMES' )
            ->where('super_benefit_list_id', $request->super_benefit_list_id )


            ->update(
                [
                    'description'=>$request->description,

                ]
            );

            $accum_benfit_stat = DB::table('SUPER_BENEFIT_LISTS' )
            ->where('super_benefit_list_id', $request->super_benefit_list_id )
            ->where('benefit_list_id',strtoupper($request->benefit_list_id))
            ->update(
                [
                    'accum_benefit_strategy_id'=>$request->accum_benefit_strategy_id,
                    'effective_date'=>$effective_date,
                    'termination_date'=>$terminate_date,
                  

                ]
            );

            if ($accum_benfit_stat) {
                return $this->respondWithToken($this->token(), 'Record  Updated Successfully', $accum_benfit_stat);
            }
        }


    }


    public function getNDCItemDetails($id)
    {
      
         $benefitLists = DB::table('SUPER_BENEFIT_LISTS')
         ->join('SUPER_BENEFIT_LIST_NAMES', 'SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID', '=', 'SUPER_BENEFIT_LIST_NAMES.SUPER_BENEFIT_LIST_ID')
         ->where('SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID',$id)
         ->get();

        return $this->respondWithToken($this->token(), '', $benefitLists);

    }



    public function get(Request $request)
    {
        $superBenefitNames = DB::table('SUPER_BENEFIT_LIST_NAMES')
                             ->where('SUPER_BENEFIT_LIST_ID','like','%'.strtoupper($request->search).'%')
                             ->orWhere('DESCRIPTION','like','%'.strtoupper($request->search).'%')
                             ->get();

        return $this->respondWithToken($this->token(),'',$superBenefitNames);
    }

    public function getBenefitCode(Request $request)
    {
        $benefitLists = DB::table('SUPER_BENEFIT_LISTS')
                        ->join('SUPER_BENEFIT_LIST_NAMES', 'SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID', '=', 'SUPER_BENEFIT_LIST_NAMES.SUPER_BENEFIT_LIST_ID')
                        ->where('SUPER_BENEFIT_LISTS.SUPER_BENEFIT_LIST_ID','like','%'.strtoupper($request->search).'%')
                        ->get();

        return $this->respondWithToken($this->token(),'',$benefitLists);
    }
}


