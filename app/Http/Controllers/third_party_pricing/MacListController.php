<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
class MacListController extends Controller
{
    public function get(Request $request)
    {
        $macList = DB::table('MAC_LIST')
            ->where('MAC_LIST', 'like', '%' . $request->search. '%')
            ->orWhere('MAC_DESC', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $macList);
    }

    public function getMacList(Request $request)
    {
        $data = DB::table('MAC_LIST')
            ->join('MAC_TABLE', 'mac_list.mac_list', '=', 'mac_table.mac_list')
            ->where('mac_table.mac_list', $request->search)
            ->get();
        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getPriceSource(Request $request)
    {
        $priceSource = [
            ['price_id' => 'CALC', 'price_label' => 'CALC Predefined Calculation'],
            ['price_id' => 'FDB', 'price_label' => 'FDB First Data Bank'],
            ['price_id' => 'MDS', 'price_label' => 'MDS Medi-Span'],
            ['price_id' => 'PLAN', 'price_label' => 'PLAN sSet by Plan'],
            ['price_id' => 'TRX', 'price_label' => 'TRX Inbound from the Provider'],
            ['price_id' => 'USR', 'price_label' => 'USR User Defined'],
        ];

        return $this->respondWithToken($this->token(), '', $priceSource);
    }

    public function getPriceType(Request $request)
    {
        $priceType = [
            ['price_type_id' => 'USC', 'price_type_label' => 'Usual and customary charge'],
        ];

        return $this->respondWithToken($this->token(), '', $priceType);
    }

    public function submitcopy(Request $request)
    {
        $effective_date = date('Ymd', strtotime($request->effective_date));
        $termination_date = date('Ymd', strtotime($request->termination_date));

        // print($effective_date);
        // print($termination_date);
        // dd($request->all());
        $validation = DB::table('mac_list')->where('mac_list', $request->mac_list)->get();



        if ($request->add_new == 1) {
            if ($validation->count() > 0) {
                return $this->respondWithToken($this->token(), 'MAC List ID is Already Exists', $validation, true, 200, 1);
            }
            $add_mac_list = DB::table('mac_list')
                ->insert([
                    'mac_list' => $request->mac_list,
                    'mac_desc' => $request->mac_desc,
                ]);

            $add = DB::table('mac_table')
                ->insert([
                    'mac_list' => $request->mac_list,
                    'gpi' => $request->gpi,
                    'effective_date' => $effective_date,
                    'termination_date' => $termination_date,
                    'price_source' => $request->price_source,
                    'price_type' => $request->price_type,
                    'mac_amount' => $request->mac_amount,
                    'allow_fee' => $request->allow_fee,
                ]);

            $add = DB::table('mac_table')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
            return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
        } else if ($request->add_new == 0) {
            if ($validation->count() < 1) {
                return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
            }
            $update_mac_list = DB::table('mac_list')
                ->where('mac_list', $request->mac_list)
                ->update([
                    'mac_desc' => $request->mac_desc,
                ]);

            $checkGPI = DB::table('mac_table')
                ->where('gpi', $request->gpi)
                ->get()
                ->count();


            if ($checkGPI <= "0") {
                $update = DB::table('mac_table')
                    ->insert([
                        'mac_list' => $request->mac_list,
                        'gpi' => $request->gpi,
                        'effective_date' => $effective_date,
                        'termination_date' => $termination_date,
                        'price_source' => $request->price_source,
                        'price_type' => $request->price_type,
                        'mac_amount' => $request->mac_amount,
                        'allow_fee' => $request->allow_fee
                    ]);
            } else {
                $update = DB::table('mac_table')
                    ->where('mac_list', $request->mac_list)
                    ->where('gpi', $request->gpi)
                    ->update([
                        // 'gpi' => $request->gpi,
                        'effective_date' => $effective_date,
                        'termination_date' => $termination_date,
                        'price_source' => $request->price_source,
                        'price_type' => $request->price_type,
                        'mac_amount' => $request->mac_amount,
                        'allow_fee' => $request->allow_fee
                    ]);
            }

            $update = DB::table('mac_table')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
            return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);
        }
    }

    

    public function submit(Request $request)
    {
        $createddate = date( 'y-m-d' );

        $validation = DB::table('mac_list')
        ->where('mac_list',$request->mac_list)
        ->get();

        if ($request->add_new == 1) {

            $validator = Validator::make($request->all(), [
                'mac_list' => ['required', 'max:10', Rule::unique('mac_list')->where(function ($q) {
                    $q->whereNotNull('mac_list');
                })],
                // 'ndc' => ['required', 'max:11', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('NDC');
                // })],

                // 'effective_date' => ['required', 'max:10', Rule::unique('NDC_EXCEPTION_LISTS')->where(function ($q) {
                //     $q->whereNotNull('effective_date');
                // })],

                // 'ndc_exception_list' => ['required', 'max:10', Rule::unique('mac_list')->where(function ($q) {
                //     $q->whereNotNull('ndc_exception_list');
                // })],

                "ma_desc" => ['max:36'],
              



            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{
                if ($validation->count() > 0) {
                    return $this->respondWithToken($this->token(), 'NDC Exception Already Exists', $validation, true, 200, 1);
                }
                $add_names = DB::table('mac_list')->insert(
                    [
                        'mac_list' => $request->mac_list,
                        'mac_desc'=>$request->mac_desc,
                        
                    ]
                );
    
                $add = DB::table('MAC_TABLE')
                    ->insert([
    
                        
                            'MAC_LIST' =>$request->mac_list,
                            'GPI'=>$request->gpi,
                            'MAC_AMOUNT'=>$request->mac_amount,
                            'ALLOW_FEE'=>$request->allow_fee,
                            'EFFECTIVE_DATE'=>$request->effective_date,
                            'TERMINATION_DATE'=>$request->termination_date,
                            'PRICE_SOURCE'=>$request->price_source,
                            'PRICE_TYPE'=>$request->price_type,
                           
                        
                        
                    ]);
    
                $add = DB::table('MAC_TABLE')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
                return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);

            }


           
        } else if ($request->add_new == 0) {

            $validator = Validator::make($request->all(), [

                'mac_list' => ['required', 'max:10'],
                


            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            }

            else{

                // if ($validation->count() < 1) {
                //     return $this->respondWithToken($this->token(), 'Record Not Found', $validation, false, 404, 0);
                // }
    
                $mac_list = DB::table('mac_list')
                ->where('mac_list', $request->mac_list )
                ->first();
                    
    
                $checkGPI = DB::table('MAC_TABLE')
                    ->where('MAC_LIST', $request->mac_list)
                    ->where('gpi',$request->gpi)
                    ->get()
                    ->count();

                    // dd($checkGPI);


                $effect_date_check = DB::table('MAC_TABLE')
                    ->where('MAC_LIST', $request->mac_list)
                    ->where('gpi',$request->gpi)
                    ->where('effective_date',$request->effective_date)
                    ->get()
                    ->count();
                    // dd($effective_date);
                // if result >=1 then update NDC_EXCEPTION_LISTS table record
                //if result 0 then add NDC_EXCEPTION_LISTS record


                if($effect_date_check == 1){

                    $add_names = DB::table('mac_list')
                    ->where('mac_list',$request->mac_list)
                    ->update(
                        [
                            'mac_desc'=>$request->mac_desc,
                            
                        ]
                    );


                    $update = DB::table('MAC_TABLE' )
                    ->where('MAC_LIST', $request->mac_list)
                    ->where('gpi',$request->gpi)
                    ->where('effective_date',$request->effective_date) 
                    ->where('termination_date',$request->termination_date)      
     
                        ->update(
                            [
                                'MAC_AMOUNT'=>$request->mac_amount,
                                'ALLOW_FEE'=>$request->allow_fee,
                                'TERMINATION_DATE'=>$request->termination_date,
                                'PRICE_SOURCE'=>$request->price_source,
                                'PRICE_TYPE'=>$request->price_type,
                                
                            ]
                        );
                        $update = DB::table('MAC_TABLE')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Updated Successfully', $update);

                   


                }else if($checkGPI == 1)
                {

                    return $this->respondWithToken($this->token(), 'Record already  exists',$checkGPI);


                }
                else{
                    if ($checkGPI <= "0") {
                        $update = DB::table('MAC_TABLE')
                        ->insert([
    
                        
                            'MAC_LIST' =>$request->mac_list,
                            'GPI'=>$request->gpi,
                            'MAC_AMOUNT'=>$request->mac_amount,
                            'ALLOW_FEE'=>$request->allow_fee,
                            'EFFECTIVE_DATE'=>$request->effective_date,
                            'TERMINATION_DATE'=>$request->termination_date,
                            'PRICE_SOURCE'=>$request->price_source,
                            'PRICE_TYPE'=>$request->price_type,
                           
                        
                        
                    ]);
                       
                    $add_names = DB::table('mac_list')
                    ->where('mac_list',$request->mac_list)
                    ->update(
                        [
                            'mac_desc'=>$request->mac_desc,
                            
                        ]
                    );
    
                    $update = DB::table('mac_list')->where('mac_list', 'like', '%' . $request->mac_list . '%')->first();
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $update);
    
                    } 

                }
               
                

    
            
            }

           
        }
    }
}
