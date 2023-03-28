<?php

namespace App\Http\Controllers\third_party_pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function submit(Request $request)
    {
        $effective_date = date('Ymd', strtotime($request->effective_date));
        $termination_date = date('Ymd', strtotime($request->termination_date));

        // print($effective_date);
        // print($termination_date);
        // dd($request->all());
        $validation = DB::table('mac_list')->where('mac_list', $request->mac_list)->get();



        if ($request->add_new == 1) {
            if ($validation->count() > 0) {
                return $this->respondWithToken($this->token(), 'MAC List ID is already existed', $validation, true, 200, 1);
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
            return $this->respondWithToken($this->token(), 'Added Successfully!', $add);
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
            return $this->respondWithToken($this->token(), 'Updated Successfully!', $update);
        }
    }
}
