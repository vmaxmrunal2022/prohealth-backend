<?php

namespace App\Http\Controllers\administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyDrugVCoverage extends Controller
{
    public function getPharmacyVarInd(Request $request)
    {
        $pharmacy_var_ind = [
            ['pharmacy_var_ind_id' => 'R', 'pharmacy_var_ind_title' => 'Retail'],
            ['pharmacy_var_ind_id' => 'M', 'pharmacy_var_ind_title' => 'Mail Service'],
            ['pharmacy_var_ind_id' => '*', 'pharmacy_var_ind_title' => 'WildCard - No Variation'],
        ];
        return $this->respondWithToken($this->token(), '', $pharmacy_var_ind);
    }

    public function getNetworkVarInd(Request $request)
    {
        $network_var_ind = [
            ['network_var_ind_id' => 'I', 'network_var_ind_title' => 'In Network'],
            ['network_var_ind_id' => 'O', 'network_var_ind_title' => 'Out of Network'],
            ['network_var_ind_id' => '*', 'network_var_ind_title' => 'Wildcard - No Variation'],
        ];
        return $this->respondWithToken($this->token(), '', $network_var_ind);
    }

    public function getClaimVarInd(Request $request)
    {
        $claim_var_ind = [
            ['claim_var_ind_id' => 'P', 'claim_var_ind_title' => 'POS'],
            ['claim_var_ind_id' => 'D', 'claim_var_ind_title' => 'DMR'],
            ['claim_var_ind_id' => 'U', 'claim_var_ind_title' => 'UCF'],
            ['claim_var_ind_id' => '*', 'claim_var_ind_title' => 'WildCard - No Variation'],
        ];
        return $this->respondWithToken($this->token(), '', $claim_var_ind);
    }

    public function submitVerifyDrugCoverage(Request $request)
    {
        //TODO -> functionality not clear
        if ($request->add_new) {
            $add_newVerify_drug = [
                'customer_id' => $request->customer_id,
                'client_id' => $request->client_id,
                'client_group_id' => $request->client_group_id,
                'member_id' => $request->member_id,
                'person_code' => $request->person_code,
                'relationship' => $request->relationship,
                'ndc' => $request->ndc,
                'date_of_service' => $request->date_of_service,
                'pharmacy_type_var_ind' => $request->pharmacy_type_var_ind,
                'network_part_var_ind' => $request->network_part_var_ind,
                'claim_type_var_ind' => $request->claim_type_var_ind,
            ];
        }
    }
}
