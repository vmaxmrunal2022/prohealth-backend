<?php

namespace App\Http\Controllers\drug_information;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugDatabaseController extends Controller
{


    public function add(Request $request)
    {

        
        $getData = DB::table('DRUG_MASTER')
            ->where('NDC', strtoupper($request->ndc))
                // ->Where('LABEL_NAME',strtoupper($request->label_name))
                // ->Where('GENERIC_NAME',strtoupper($request->generic_name))
                // ->Where('PACKAGE_SIZE',strtoupper($request->package_size))
            ->first();

        if ($request->has('new')) {

            if ($getData) {

                return $this->respondWithToken($this->token(), 'NDC ID Already Exists', $getData);


            } else {



                $addData = DB::table('DRUG_MASTER')
                    ->insert([
                        'NDC' => strtoupper($request->ndc),
                        'LABEL_NAME' => strtoupper($request->label_name),
                        'GENERIC_PRODUCT_ID' => strtoupper($request->generic_product_id),
                        'GENERIC_NAME' => strtoupper($request->generic_name),
                        'MANUFACTURER_NAME' => strtoupper($request->manufacturer_name),
                        'MANUFACTURER_NAME_ABBR' => strtoupper($request->manufacturer_name_abbr),
                        'GENERIC_CODE' => strtoupper($request->generic_code),
                        'DEA_CLASS_CODE' => strtoupper($request->dea_class_code),
                        'THERAPEUTIC_CLASS_CODE' => strtoupper($request->therapeutic_class_code),
                        'THERAPEUTIC_EQUIV_CODE' => strtoupper($request->therapeutic_equiv_code),
                        'RX_OTC_INDICATOR' => strtoupper($request->rx_otc_indicator),
                        'GPPC_CODE' => strtoupper($request->gppc_code),
                        'METRIC_STRENGTH' => strtoupper($request->metric_strength),
                        'STRENGTH_UOM' => strtoupper($request->strength_vom),
                        'DOSAGE_FORM' => strtoupper($request->dosage_form),
                        'PACKAGE_SIZE' => strtoupper($request->package_size),
                        'PACKAGE_UOM' => strtoupper($request->package_uom),
                        'PACKAGE_QTY' => strtoupper($request->package_qty),
                        'TOTAL_PACKAGE_QTY' => strtoupper($request->total_package_qty),
                        'LEGEND_CHANGE_DATE' => $request->legend_change_date,
                        'NEXT_SMLR_SUFFIX' => strtoupper($request->next_smlr_suffix),
                        'NEXT_LRGR_SUFFIX' => strtoupper($request->next_lrgr_suffix),
                        'DESI_CODE' => $request->desi_code,
                        'MAINTENANCE_DRUG_CODE' => $request->maintenance_drug_code,
                        'DISPENSING_UNIT_CODE' => $request->dispensing_unit_code,
                        'UNIT_DOSE_CODE' => $request->unit_dose_code,
                        'ROUTE_ADMIN_CODE' => $request->route_admin_code,
                        'DOLLAR_RANK_CODE' => $request->dollar_rank_code,
                        'RX_RANK_CODE' => $request->rx_rank_code,
                        'SINGLE_COMB_CODE' => $request->single_comb_code,
                        'REPACKAGER_IND' => $request->repackager_ind,
                        'MANUFACTURER_ID' => $request->manufacturer_id,
                        'SUPERCEDED_NDC' => $request->superceded_ndc,
                        'PRECEDED_NDC' => $request->preceded_ndc,
                        'LAST_CHANGE_DATE' => $request->last_change_date,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'DRUG_STATUS' => $request->drug_status,
                        'INT_EXT_CODE' => $request->int_ext_code,
                        'PKG_DESCRIPTION' => $request->pkg_description,

                        'USER_ID' => $request->user_id,

                        'OTC_EQUIV_IND' => $request->otc_equiv_ind,
                        'BRAND_NAME_CODE' => $request->brand_name_code,



                    ]);


                    


                    $pricing_list_obj = json_decode(json_encode($request->pricing_form, true));


                    if(!empty($request->pricing_form)){

                        $pricing_list = $pricing_list_obj[0];
                    // $effective_date   = $limitation_list->effective_date;
                    // $termination_date = $limitation_list->termination_date;
                    // $limitations_list = $limitation_list->limitations_list;
                    foreach ($pricing_list_obj as $key => $pricing_list) {
                       



                    $drug_price = DB::table('DRUG_PRICE')
                    ->insert([
                        'NDC' => strtoupper($request->ndc),
                        'PRICE_SOURCE' => strtoupper($pricing_list->price_source),
                        'PRICE_TYPE' => strtoupper($pricing_list->price_type),
                        'PRICE_EFF_DATE_1' => strtoupper($pricing_list->price_eff_date_1),
                        'PRICE_AMT_1' => strtoupper($pricing_list->price_amt_1),
                        'PRICE_EFF_DATE_2' => strtoupper($pricing_list->price_eff_date_2),
                        'PRICE_AMT_2' => strtoupper($pricing_list->price_amt_2),
                        'PRICE_EFF_DATE_3' => strtoupper($pricing_list->price_eff_date_3),
                        'PRICE_AMT_3' => strtoupper($pricing_list->price_amt_3),



                    ]);



                    }
                }






                if ($addData) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully', $addData);
                }




            }

        } else { {
                $updateData = DB::table('DRUG_MASTER')
                    ->where('NDC', $request->ndc)
                    ->update([
                        'LABEL_NAME' => strtoupper($request->label_name),
                        'GENERIC_PRODUCT_ID' => strtoupper($request->generic_product_id),
                        'GENERIC_NAME' => strtoupper($request->generic_name),
                        'MANUFACTURER_NAME' => strtoupper($request->manufacturer_name),
                        'MANUFACTURER_NAME_ABBR' => strtoupper($request->manufacturer_name_abbr),
                        'GENERIC_CODE' => strtoupper($request->generic_code),
                        'DEA_CLASS_CODE' => strtoupper($request->dea_class_code),
                        'THERAPEUTIC_CLASS_CODE' => strtoupper($request->therapeutic_class_code),
                        'THERAPEUTIC_EQUIV_CODE' => strtoupper($request->therapeutic_equiv_code),
                        'RX_OTC_INDICATOR' => strtoupper($request->rx_otc_indicator),
                        'GPPC_CODE' => strtoupper($request->gppc_code),
                        'METRIC_STRENGTH' => strtoupper($request->metric_strength),
                        'STRENGTH_UOM' => strtoupper($request->strength_vom),
                        'DOSAGE_FORM' => strtoupper($request->dosage_form),
                        'PACKAGE_SIZE' => strtoupper($request->package_size),
                        'PACKAGE_UOM' => strtoupper($request->package_uom),
                        'PACKAGE_QTY' => strtoupper($request->package_qty),
                        'TOTAL_PACKAGE_QTY' => strtoupper($request->total_package_qty),
                        'LEGEND_CHANGE_DATE' => $request->legend_change_date,
                        'NEXT_SMLR_SUFFIX' => strtoupper($request->next_smlr_suffix),
                        'NEXT_LRGR_SUFFIX' => strtoupper($request->next_lrgr_suffix),
                        'DESI_CODE' => $request->desi_code,
                        'MAINTENANCE_DRUG_CODE' => $request->maintenance_drug_code,
                        'DISPENSING_UNIT_CODE' => $request->dispensing_unit_code,
                        'UNIT_DOSE_CODE' => $request->unit_dose_code,
                        'ROUTE_ADMIN_CODE' => $request->route_admin_code,
                        'DOLLAR_RANK_CODE' => $request->dollar_rank_code,
                        'RX_RANK_CODE' => $request->rx_rank_code,
                        'SINGLE_COMB_CODE' => $request->single_comb_code,
                        'REPACKAGER_IND' => $request->repackager_ind,
                        'MANUFACTURER_ID' => $request->manufacturer_id,
                        'SUPERCEDED_NDC' => $request->superceded_ndc,
                        'PRECEDED_NDC' => $request->preceded_ndc,
                        'LAST_CHANGE_DATE' => $request->last_change_date,
                        'EFFECTIVE_DATE' => $request->effective_date,
                        'DRUG_STATUS' => $request->drug_status,
                        'INT_EXT_CODE' => $request->int_ext_code,
                        'PKG_DESCRIPTION' => $request->pkg_description,

                        'USER_ID' => $request->user_id,

                        'OTC_EQUIV_IND' => $request->otc_equiv_ind,
                        'BRAND_NAME_CODE' => $request->brand_name_code,
                    ]);


                
                    $data=DB::table('DRUG_PRICE')->where('NDC',strtoupper($request->ndc))->delete(); 

                    $pricing_list_obj = json_decode(json_encode($request->pricing_form, true));


                    if(!empty($request->pricing_form)){

                        $pricing_list = $pricing_list_obj[0];
                    // $effective_date   = $limitation_list->effective_date;
                    // $termination_date = $limitation_list->termination_date;
                    // $limitations_list = $limitation_list->limitations_list;
                    foreach ($pricing_list_obj as $key => $pricing_list) {
                       



                    $drug_price = DB::table('DRUG_PRICE')
                    ->insert([
                        'NDC' => strtoupper($request->ndc),
                        'PRICE_SOURCE' => strtoupper($pricing_list->price_source),
                        'PRICE_TYPE' => strtoupper($pricing_list->price_type),
                        'PRICE_EFF_DATE_1' => strtoupper($pricing_list->price_eff_date_1),
                        'PRICE_AMT_1' => strtoupper($pricing_list->price_amt_1),
                        'PRICE_EFF_DATE_2' => strtoupper($pricing_list->price_eff_date_2),
                        'PRICE_AMT_2' => strtoupper($pricing_list->price_amt_2),
                        'PRICE_EFF_DATE_3' => strtoupper($pricing_list->price_eff_date_3),
                        'PRICE_AMT_3' => strtoupper($pricing_list->price_amt_3),



                    ]);



                    }
                }






                if ($updateData) {
                    return $this->respondWithToken($this->token(), 'Record Updated Successfully', $updateData);
                }
            }
        }



    }


    public function get(Request $request)
    {
        $data = DB::table('DRUG_MASTER')
            ->where('NDC', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('LABEL_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('GENERIC_NAME', 'like', '%' . strtoupper($request->search) . '%')
            ->orWhere('PACKAGE_SIZE', 'like', '%' . strtoupper($request->search) . '%')
            ->get();

        return $this->respondWithToken($this->token(), '', $data);
    }

    public function getDrugPrices(Request $request)
    {
        $drugPrices = DB::table('drug_price')
            ->where('ndc', $request->ndc)
            ->get();
        return $this->respondWithToken($this->token(), '', $drugPrices);
    }

    public function addDrugPrice(Request $request)
    {

        $getrecord = DB::table('DRUG_PRICE')
            ->where('price_source', strtoupper($request->price_source))
            ->where('NDC', $request->ndc)

            ->first();

        if ($request->add_drug_price== 0 ||$request->add_drug_price== 1 ) {

            if ($getrecord) {

                $updateUser = DB::table('DRUG_PRICE')
                ->where('NDC', $request->ndc)
                ->where('PRICE_SOURCE',$request->price_source)
                ->update([
                    'PRICE_TYPE' => strtoupper($request->price_type),
                    'PRICE_EFF_DATE_1' => strtoupper($request->price_eff_date_1),
                    'PRICE_AMT_1' => strtoupper($request->price_amt_1),
                    'PRICE_EFF_DATE_2' => strtoupper($request->price_eff_date_2),
                    'PRICE_AMT_2' => strtoupper($request->price_amt_2),
                    'PRICE_EFF_DATE_3' => strtoupper($request->price_eff_date_3),
                    'PRICE_AMT_3' => strtoupper($request->price_amt_3),
                ]);

            if ($updateUser) {
                return $this->respondWithToken($this->token(), 'Record Updated Successfully !!!', $updateUser);
            }

            } else if($request->add_price ==0  || $request->add_drug_price== 1 ) {


                $addData = DB::table('DRUG_PRICE')
                    ->insert([
                        'NDC' => strtoupper($request->ndc),
                        'PRICE_SOURCE' => strtoupper($request->price_source),
                        'PRICE_TYPE' => strtoupper($request->price_type),
                        'PRICE_EFF_DATE_1' => strtoupper($request->price_eff_date_1),
                        'PRICE_AMT_1' => strtoupper($request->price_amt_1),
                        'PRICE_EFF_DATE_2' => strtoupper($request->price_eff_date_2),
                        'PRICE_AMT_2' => strtoupper($request->price_amt_2),
                        'PRICE_EFF_DATE_3' => strtoupper($request->price_eff_date_3),
                        'PRICE_AMT_3' => strtoupper($request->price_amt_3),



                    ]);


                if ($addData) {
                    return $this->respondWithToken($this->token(), 'Record Added Successfully!!!', $addData);
                }


            }

    }

}


}