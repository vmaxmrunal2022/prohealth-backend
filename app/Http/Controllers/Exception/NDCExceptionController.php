<?php

namespace App\Http\Controllers\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NDCExceptionController extends Controller
{

    public function add( Request $request ) {

        $createddate = date( 'y-m-d' );

        if ( $request->has( 'new' ) ) {


            $accum_benfit_stat_names = DB::table('NDC_EXCEPTIONS')->insert(
                [
                    'ndc_exception_list' => strtoupper( $request->ndc_exception_list ),
                    'exception_name'=>$request->exception_name,
                    

                ]
            );

            $accum_benfit_stat = DB::table('NDC_EXCEPTION_LISTS' )->insert(
                [
                    'ndc_exception_list' => strtoupper( $request->ndc_exception_list ),
                 
                ]
            );
            $benefitcode = DB::table('NDC_EXCEPTION_LISTS')->where('ndc_exception_list', 'like', '%'.$request->ndc_exception_list .'%')->first();


        } else {


            // dd($request->all())

            $benefitcode = DB::table('NDC_EXCEPTION_LISTS' )
            ->where('ndc', $request->ndc )
            ->update(
                [
                    'ndc' => strtoupper($request->ndc),
                    'min_rx_qty'=>$request->min_rx_qty,
                 

                ]
            );

            $benefitcode = DB::table('NDC_EXCEPTION_LISTS')->where('ndc', 'like', '%'.$request->ndc .'%')->first();


            $accum_benfit_stat = DB::table('NDC_EXCEPTIONS' )
            ->where('ndc_exception_list', $request->ndc_exception_list )
            ->update(
                [
                    'ndc_exception_list' => $request->ndc_exception_list,
                    'exception_name'=>$request->exception_name,
                   
                  

                ]
            );

            $benefitcode = DB::table('NDC_EXCEPTIONS')->where('ndc_exception_list', 'like', '%'.$request->ndc_exception_list .'%')->first();

        }


        return $this->respondWithToken( $this->token(), 'Successfully added',$benefitcode);
    }




    public function search(Request $request)
    {
        $ndc = DB::table('NDC_EXCEPTIONS')
                ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

    return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function getNDCList($ndcid)
    {
        $ndclist = DB::table('NDC_EXCEPTION_LISTS')
                // ->select('NDC_EXCEPTION_LIST', 'EXCEPTION_NAME')
                ->where('NDC_EXCEPTION_LIST', 'like', '%' . strtoupper($ndcid) . '%')
                // ->orWhere('EXCEPTION_NAME', 'like', '%' . strtoupper($ndcid) . '%')
                ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }

    public function getNDCItemDetails($ndcid)
    {
        $ndc = DB::table('NDC_EXCEPTION_LISTS')
                    ->select('NDC_EXCEPTION_LISTS.*', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST as exception_list', 'NDC_EXCEPTIONS.EXCEPTION_NAME as exception_name')
                    ->leftjoin('NDC_EXCEPTIONS', 'NDC_EXCEPTIONS.NDC_EXCEPTION_LIST', '=', 'NDC_EXCEPTION_LISTS.NDC_EXCEPTION_LIST')
                    ->where('NDC_EXCEPTION_LISTS.NDC', 'like', '%' . strtoupper($ndcid) . '%')  
                    ->first();

        return $this->respondWithToken($this->token(), '', $ndc);

    }
}
