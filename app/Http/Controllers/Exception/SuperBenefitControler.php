<?php

namespace App\Http\Controllers\exception_list;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperBenefitControler extends Controller
{      



    public function add( Request $request ) {
        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {



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
                    // 'effective_date'=>$request->effective_date,

                ]
            );

            $benefitcode = DB::table('SUPER_BENEFIT_LISTS')->where('benefit_list_id', 'like', '%'.$request->benefit_list_id .'%')->first();

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
            ->where('benefit_list_id',$request->benefit_list_id)
            ->update(
                [
                    'accum_benefit_strategy_id'=>$request->accum_benefit_strategy_id,
                    'effective_date'=>$request->effective_date,
                  

                ]
            );

            $benefitcode = DB::table('SUPER_BENEFIT_LISTS')->where('super_benefit_list_id', 'like', $request->super_benefit_list_id )->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
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


