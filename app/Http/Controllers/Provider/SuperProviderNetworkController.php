<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SuperProviderNetworkController extends Controller
{

    public function dropDown(Request $request)
    {

        $ndclist =  DB::table('SUPER_RX_NETWORK_NAMES')
            ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->get();
        return $this->respondWithToken($this->token(), 'Data Fetched Succssfully', $ndclist);
    }

    // public function add_old(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [

    //         "super_rx_network_id" => ['required', 'max:10'],
    //         "super_rx_network_id_name" => ['required', 'max:35'],
    //         "min_rx_qty" => ['max:6'],
    //         "max_rx_qty" => ['max:6'],
    //         "rx_network_id" => ['required'],
    //         "rx_network_type" => ['max:6'],
    //         "min_rx_days" => ['max:1'],
    //         "max_rx_days" => ['max:1'],
    //         "effective_date" => ['max:6'],
    //         "max_retail_fills" => ['max:6'],
    //         "max_fills_opt" => ['max:1'],
    //         "starter_dose_days" => ['max:3'],
    //         "price_schedule_ovrd" => ['max:6'],
    //         "starter_dose_bypass_days" => ['max:3'],
    //         "starter_dose_maint_bypass_days" => ['max:3'],
    //         "termination_date" => ['max:6'],
    //         "comm_charge_paid" => ['max:10'],
    //         "comm_charge_reject" => ['max:10'],
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
    //     }


    //     $createddate = date('y-m-d');

    //     $recordcheck = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', $request->super_rx_network_id)->first();



    //     if ($request->add_new == 1) {

    //         if ($recordcheck) {
    //             return $this->respondWithToken($this->token(), 'Super Network  Id is  already Exists ', $recordcheck);
    //         } else {

    //             $accum_benfit_stat_names = DB::table('SUPER_RX_NETWORK_NAMES')->insert(
    //                 [
    //                     'super_rx_network_id' => $request->super_rx_network_id,
    //                     'super_rx_network_id_name' => $request->super_rx_network_id_name,

    //                 ]
    //             );


    //             $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS')->insert(
    //                 [
    //                     'super_rx_network_id' => $request->super_rx_network_id,
    //                     'rx_network_id' => $request->rx_network_id,
    //                     'effective_date' => $request->effective_date,
    //                     'comm_charge_paid' => $request->comm_charge_paid,
    //                     'comm_charge_reject' => $request->comm_charge_reject,
    //                     'days_supply_opt' => $request->days_supply_opt,
    //                     'effective_date' => $request->effective_date,
    //                     'max_fills_opt' => $request->max_fills_opt,
    //                     'max_retail_fills' => $request->max_retail_fills,
    //                     'max_rx_qty' => $request->max_rx_qty,
    //                     'min_rx_qty' => $request->min_rx_qty,
    //                     'price_schedule_ovrd' => $request->price_schedule_ovrd,
    //                     'rx_network_id' => $request->rx_network_id,
    //                     'rx_network_type' => $request->rx_network_type,
    //                     'starter_dose_bypass_days' => $request->starter_dose_bypass_days,
    //                     'starter_dose_days' => $request->starter_dose_days,
    //                     'starter_dose_maint_bypass_days' => $request->starter_dose_maint_bypass_days,
    //                     'super_rx_network_priority' => $request->super_rx_network_priority,
    //                     'termination_date' => $request->termination_date,
    //                     'min_rx_days' => $request->min_rx_days,
    //                     'max_rx_days' => $request->max_rx_days,



    //                 ]
    //             );
    //         }

    //         if ($accum_benfit_stat) {
    //             return $this->respondWithToken($this->token(), 'Recored Added Successfully', $accum_benfit_stat);
    //         }


    //         $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS')->insert(
    //             [
    //                 'super_rx_network_id' => $request->super_rx_network_id,
    //                 'rx_network_id' => $request->rx_network_id,
    //                 'effective_date' => $request->effective_date,
    //             ]
    //         );

    //         $benefitcode = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%' . $request->super_rx_network_id . '%')->first();
    //     } else if ($request->add_new == 0) {



    //         $benefitcode = DB::table('SUPER_RX_NETWORK_NAMES')
    //             ->where('super_rx_network_id', $request->super_rx_network_id)


    //             ->update(
    //                 [
    //                     'super_rx_network_id' => $request->super_rx_network_id,
    //                     'super_rx_network_id_name' => $request->super_rx_network_id_name

    //                 ]
    //             );

    //         $accum_benfit_stat = DB::table('SUPER_RX_NETWORKS')

    //             ->where('super_rx_network_id', $request->super_rx_network_id)
    //             ->update(
    //                 [
    //                     'rx_network_id' => $request->rx_network_id,
    //                     'effective_date' => $request->effective_date,
    //                     'comm_charge_paid' => $request->comm_charge_paid,
    //                     'comm_charge_reject' => $request->comm_charge_reject,
    //                     'days_supply_opt' => $request->days_supply_opt,
    //                     'max_fills_opt' => $request->max_fills_opt,
    //                     'max_retail_fills' => $request->max_retail_fills,
    //                     'max_rx_qty' => $request->max_rx_qty,
    //                     'min_rx_qty' => $request->min_rx_qty,
    //                     'price_schedule_ovrd' => $request->price_schedule_ovrd,
    //                     'rx_network_type' => $request->rx_network_type,
    //                     'starter_dose_bypass_days' => $request->starter_dose_bypass_days,
    //                     'starter_dose_days' => $request->starter_dose_days,
    //                     'starter_dose_maint_bypass_days' => $request->starter_dose_maint_bypass_days,
    //                     'super_rx_network_priority' => $request->super_rx_network_priority,
    //                     'termination_date' => $request->termination_date,
    //                     'min_rx_days' => $request->min_rx_days,
    //                     'max_rx_days' => $request->max_rx_days,



    //                 ]
    //             );


    //         $benefitcode = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%' . $request->super_rx_network_id . '%')->first();
    //         return $this->respondWithToken($this->token(), 'Record Updated Successfully', $benefitcode);
    //     }
    // }

    public function add(Request $request)
    {




        $createddate = date('y-m-d');

        $recordcheck = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', $request->super_rx_network_id)->first();



        if ($request->add_new) {

            $validator = Validator::make($request->all(), [
                // "super_rx_network_id" => ['required', 'max:10'],
                // "super_rx_network_id_name" => ['required', 'max:35'],
                // "min_rx_qty" => ['max:6'],
                // "max_rx_qty" => ['max:6'],
                // "rx_network_id" => ['required'],
                // "rx_network_type" => ['max:6'],
                // "min_rx_days" => ['max:1'],
                // "max_rx_days" => ['max:1'],
                // "effective_date" => ['max:10'],
                // "max_retail_fills" => ['max:6'],
                // "max_fills_opt" => ['max:1'],
                // "starter_dose_days" => ['max:3'],
                // "price_schedule_ovrd" => ['max:6'],
                // "starter_dose_bypass_days" => ['max:3'],
                // "starter_dose_maint_bypass_days" => ['max:3'],
                // "termination_date" => ['max:10'],
                // "comm_charge_paid" => ['max:10'],
                // "comm_charge_reject" => ['max:10'],
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), false);
            } else {

                if (!$request->updateForm) {

                    $ifExist = DB::table('SUPER_RX_NETWORK_NAMES')
                        ->where(DB::raw('UPPER(super_rx_network_id)'), strtoupper($request->super_rx_network_id))
                        ->get();
                    if (count($ifExist) >= 1) {
                        return $this->respondWithToken($this->token(), [["Super Network ID already exists"]], '', false);
                    }
                } else {
                }

                if ($request->super_rx_network_id && $request->rx_network_id) {
                    $count = DB::table('SUPER_RX_NETWORK_NAMES')
                        ->where(DB::raw('UPPER(super_rx_network_id)'), strtoupper($request->super_rx_network_id))
                        ->get()
                        ->count();
                    if ($count <= 0) {
                        // return date('d-M-y');
                        $add_names = DB::table('SUPER_RX_NETWORK_NAMES')->insert(
                            [
                                'super_rx_network_id' => $request->super_rx_network_id,
                                'super_rx_network_id_name' => $request->super_rx_network_id_name,
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'form_id' => ''
                            ]
                        );
                        $add = DB::table('SUPER_RX_NETWORKS')
                            ->insert([
                                'DATE_TIME_CREATED' => date('d-M-y'),
                                'user_id' => Cache::get('userId'),
                                'DATE_TIME_MODIFIED' => date('d-M-y'),
                                'form_id' => '',
                                'super_rx_network_id' => $request->super_rx_network_id,
                                'rx_network_id' => $request->rx_network_id,
                                'effective_date' => $request->effective_date,
                                'comm_charge_paid' => $request->comm_charge_paid,
                                'comm_charge_reject' => $request->comm_charge_reject,
                                'days_supply_opt' => $request->days_supply_opt,
                                'effective_date' => $request->effective_date,
                                'max_fills_opt' => $request->max_fills_opt,
                                'max_retail_fills' => $request->max_retail_fills,
                                'max_rx_qty' => $request->max_rx_qty,
                                'min_rx_qty' => $request->min_rx_qty,
                                'price_schedule_ovrd' => $request->price_schedule_ovrd,
                                'rx_network_id' => $request->rx_network_id,
                                'rx_network_type' => $request->rx_network_type,
                                'starter_dose_bypass_days' => $request->starter_dose_bypass_days,
                                'starter_dose_days' => $request->starter_dose_days,
                                'starter_dose_maint_bypass_days' => $request->starter_dose_maint_bypass_days,
                                'super_rx_network_priority' => $request->super_rx_network_priority == null ? "1" : $request->super_rx_network_priority,
                                'termination_date' => $request->termination_date,
                                'min_rx_days' => $request->min_rx_days,
                                'max_rx_days' => $request->max_rx_days,
                            ]);

                        $add = DB::table('SUPER_RX_NETWORKS')->where('super_rx_network_id', 'like', '%' . $request->super_rx_network_id . '%')->first();
                        return $this->respondWithToken($this->token(), 'Record Added Successfully', $add);
                    } else {
                        $updateProviderExceptionData = DB::table('SUPER_RX_NETWORK_NAMES')
                            ->where('super_rx_network_id', $request->super_rx_network_id)
                            ->update([
                                'super_rx_network_id_name' => $request->super_rx_network_id_name,
                                'user_id' => Cache::get('userId'),
                                'date_time_modified' => date('d-M-y'),
                                // 'specialty_status' => $request->specialty_status,
                                'form_id' => ''
                            ]);
                        $countValidation = DB::table('SUPER_RX_NETWORKS')
                            ->where(DB::raw('UPPER(super_rx_network_id)'), strtoupper($request->super_rx_network_id))
                            ->where(DB::raw('UPPER(rx_network_id)'), strtoupper($request->rx_network_id))
                            ->get();

                        if (count($countValidation) >= 1) {
                            return $this->respondWithToken(
                                $this->token(),
                                [['Provider Network ID already exists']],
                                [['Provider Network ID already exists']],
                                false
                            );
                        } else {
                            $addProviderValidationData = DB::table('SUPER_RX_NETWORKS')
                                ->insert([
                                    'DATE_TIME_CREATED' => date('d-M-y'),
                                    'user_id' => Cache::get('userId'),
                                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                                    'form_id' => '',
                                    'super_rx_network_id' => $request->super_rx_network_id,
                                    'rx_network_id' => $request->rx_network_id,
                                    'effective_date' => $request->effective_date,
                                    'comm_charge_paid' => $request->comm_charge_paid,
                                    'comm_charge_reject' => $request->comm_charge_reject,
                                    'days_supply_opt' => $request->days_supply_opt,
                                    'effective_date' => $request->effective_date,
                                    'max_fills_opt' => $request->max_fills_opt,
                                    'max_retail_fills' => $request->max_retail_fills,
                                    'max_rx_qty' => $request->max_rx_qty,
                                    'min_rx_qty' => $request->min_rx_qty,
                                    'price_schedule_ovrd' => $request->price_schedule_ovrd,
                                    'rx_network_id' => $request->rx_network_id,
                                    'rx_network_type' => $request->rx_network_type,
                                    'starter_dose_bypass_days' => $request->starter_dose_bypass_days,
                                    'starter_dose_days' => $request->starter_dose_days,
                                    'starter_dose_maint_bypass_days' => $request->starter_dose_maint_bypass_days,
                                    'super_rx_network_priority' => $request->super_rx_network_priority == null ? "1" : $request->super_rx_network_priority,
                                    'termination_date' => $request->termination_date,
                                    'min_rx_days' => $request->min_rx_days,
                                    'max_rx_days' => $request->max_rx_days,
                                ]);
                            $reecord = DB::table('SUPER_RX_NETWORKS')
                                ->join('SUPER_RX_NETWORK_NAMES', 'SUPER_RX_NETWORK_NAMES.super_rx_network_id', '=', 'SUPER_RX_NETWORKS.super_rx_network_id')
                                ->where('SUPER_RX_NETWORKS.super_rx_network_id', $request->super_rx_network_id)
                                ->where('SUPER_RX_NETWORKS.rx_network_id', $request->rx_network_id)
                                ->first();
                            return $this->respondWithToken(
                                $this->token(),
                                'Record Added successfully',
                                $reecord,
                            );
                        }
                    }
                }
            }
        } else {
            $updateProviderExceptionData = DB::table('SUPER_RX_NETWORK_NAMES')
                ->where(DB::raw('UPPER(super_rx_network_id)'), strtoupper($request->super_rx_network_id))
                ->update([
                    'super_rx_network_id_name' => $request->super_rx_network_id_name,
                    'user_id' => Cache::get('userId'),
                    'date_time_modified' => date('d-M-y'),
                    'form_id' => ''
                ]);

            $countValidation = DB::table('SUPER_RX_NETWORKS')
                ->where(DB::raw('UPPER(super_rx_network_id)'), strtoupper($request->super_rx_network_id))
                ->where(DB::raw('UPPER(rx_network_id)'), strtoupper($request->rx_network_id))
                // ->where('pharmacy_status', $request->pharmacy_status)
                ->update([
                    'user_id' => Cache::get('userId'),
                    'DATE_TIME_MODIFIED' => date('d-M-y'),
                    'form_id' => '',
                    'effective_date' => $request->effective_date,
                    'comm_charge_paid' => $request->comm_charge_paid,
                    'comm_charge_reject' => $request->comm_charge_reject,
                    'days_supply_opt' => $request->days_supply_opt,
                    'effective_date' => $request->effective_date,
                    'max_fills_opt' => $request->max_fills_opt,
                    'max_retail_fills' => $request->max_retail_fills,
                    'max_rx_qty' => $request->max_rx_qty,
                    'min_rx_qty' => $request->min_rx_qty,
                    'price_schedule_ovrd' => $request->price_schedule_ovrd,
                    'rx_network_id' => $request->rx_network_id,
                    'rx_network_type' => $request->rx_network_type,
                    'starter_dose_bypass_days' => $request->starter_dose_bypass_days,
                    'starter_dose_days' => $request->starter_dose_days,
                    'starter_dose_maint_bypass_days' => $request->starter_dose_maint_bypass_days,
                    'super_rx_network_priority' => $request->super_rx_network_priority == null ? "1" : $request->super_rx_network_priority,
                    'termination_date' => $request->termination_date,
                    'min_rx_days' => $request->min_rx_days,
                    'max_rx_days' => $request->max_rx_days,
                ]);

            return $this->respondWithToken(
                $this->token(),
                'Record Updated successfully',
                $countValidation,
            );
        }
    }

    public function search(Request $request)
    {
        // $ndc = DB::table('SUPER_RX_NETWORK_NAMES')
        //     ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID')
        //     ->select('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_PRIORITY', 'SUPER_RX_NETWORKS.EFFECTIVE_DATE', 'SUPER_RX_NETWORKS.RX_NETWORK_TYPE', 'SUPER_RX_NETWORKS.PRICE_SCHEDULE_OVRD')
        //     ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . $request->search . '%')
        //     ->orWhere('SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID_NAME', 'like', '%' . $request->search . '%')
        //     ->distinct()
        //     ->get();

        $ndc = DB::table('SUPER_RX_NETWORK_NAMES')
            // ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->where(DB::raw('UPPER(SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID)'), 'like', '%' . strtoupper($request->search) . '%')
            ->distinct()
            ->get();

        return $this->respondWithToken($this->token(), '', $ndc);
    }

    public function networkList($ndcid)
    {
        $ndclist =  DB::table('SUPER_RX_NETWORK_NAMES')
            ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
            ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . $ndcid . '%')
            ->orderBy('SUPER_RX_NETWORK_PRIORITY', 'asc')
            ->get();
        // $ndclist =  DB::table('SUPER_RX_NETWORKS')
        //     ->where(DB::raw('UPPER(SUPER_RX_NETWORK_ID)'), strtoupper($ndcid))
        //     ->orderBy('SUPER_RX_NETWORK_PRIORITY', 'asc')
        //     ->get();

        return $this->respondWithToken($this->token(), '', $ndclist);
    }



    public function getDetails($ndcid, $rx_network_id)
    {

        // $ndc =  DB::table('SUPER_RX_NETWORK_NAMES')
        //     ->join('SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID')
        //     ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', 'like', '%' . $ndcid . '%')
        //     ->where('SUPER_RX_NETWORKS.rx_newtwork_id', $rx_network_id)
        //     ->first();

        $ndc =  DB::table('SUPER_RX_NETWORKS')
            ->join('SUPER_RX_NETWORK_NAMES', 'SUPER_RX_NETWORK_NAMES.SUPER_RX_NETWORK_ID', '=', 'SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID')
            ->where('SUPER_RX_NETWORKS.SUPER_RX_NETWORK_ID', $ndcid)
            ->where('SUPER_RX_NETWORKS.rx_network_id', $rx_network_id)
            ->first();

        return $this->respondWithToken($this->token(), '', $ndc);
    }
}
