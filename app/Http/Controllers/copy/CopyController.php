<?php

namespace App\Http\Controllers\copy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopyController extends Controller
{
    public function viewCopy(Request $request)
    {
        return "hello controller";
    }

    public function getUniqueId(Request $request)
    {
        $table_name = $request[0];
        $uniqueColumns = DB::table('all_ind_columns')
            ->select('column_name')
            ->whereIn('index_name', function ($query) use ($table_name) {
                $query->select('index_name')
                    ->from('all_indexes')
                    ->where('table_name', $table_name)
                    ->where('uniqueness', 'UNIQUE');
            })
            ->where('table_name', $table_name)
            ->orderBy('column_position', 'desc')
            // ->latest()
            ->first();

        // $selectedColumns = [];
        // $selectedColumns[] = DB::raw($column->column_name . ' AS `' . $column->column_name . '`');

        $selectedColumns = DB::raw($uniqueColumns->column_name . ' AS ' . 'source_id' . '');



        $table_data = DB::table($table_name)
            ->select($selectedColumns)
            ->get();
        return $this->respondWithToken($this->token(), '', $table_data);
    }

    public function getAllCloneTable(Request $request)
    {
        $table_list = [
            'CUSTOMER' => 'Customer', //user data
            'CLIENT' => 'Client',
            'CLIENT_GROUP' => 'Client Group',
            'DIAGNOSIS_EXCEPTIONS' => 'Diagnosis Validation List', //validation list
            'NDC_EXCEPTIONS' => 'NDC Validation List',
            'PHYSICIAN_EXCEPTIONS' => 'Prescriber Validation List',
            'PHARMACY_EXCEPTIONS' => 'Provider Validation List',
            'SPECIALTY_EXCEPTIONS' => 'Speciality Validation List',
            'ELIG_VALIDATION_LISTS' => 'Eligibility Validation List',
            'PRICING_STRATEGY_NAMES' => 'Pricing Strategy', //strategies module
            'COPAY_STRATEGY_NAMES' => 'Copay Strategy',
            'ACCUM_BENE_STRATEGY_NAMES' => 'Accum Benefit Startegy',
            'PLAN_LOOKUP_TABLE' => 'Plan Association', //plan design module
            'PLAN_BENEFIT_TABLE' => 'Plan Edit',
            'PLAN_ACCUM_DEDUCT_TABLE' => 'Accumulated Benefits', // accumulated benedfits
            'GPI_EXCLUSION_LISTS' => 'GPI Exclusion List',
            'NDC_EXCLUSION_LISTS' => 'NDC Exclusion List',
            'MM_LIFE_MAX' => 'Major Medical Maximus',
            'PHARMACY_CHAIN' => 'Pharmacy Chain', //Provider module
            'PHARMACY_TABLE' => 'Provider',
            'RX_NETWORK_NAMES' => 'Traditional Network',
            'RX_NETWORK_RULE_NAMES' => 'Flexible Network',
            'SUPER_RX_NETWORK_NAMES' => 'Super Provider Network',
            'PRICE_SCHEDULE' => 'Price Schedule',
            'COPAY_SCHEDULE' => 'Copay Schedule',
            'COPAY_LIST' => 'COPAY_LIST',
            'mac_table' => 'MAC List',
            'tax_schedule' => 'Tax Schedule',
            'procedure_ucr_names' => 'Procedure UCR List',
            'rva_names' => 'RVA List',
            'NDC_EXCEPTIONS' => 'NDC Exception', //exception list
            'GPI_EXCEPTION_LISTS' => 'GPI Exception',
            'TC_EXCEPTIONS' => 'Therapy Class',
            'DRUG_CATGY_EXCEPTION_NAMES' => 'Drug Classification',
            'PROCEDURE_EXCEPTION_NAMES' => 'Procedure Exceptions',
            'REASON_CODE_LIST_NAMES' => 'Reason Code Exception',
            'BENEFIT_LIST_NAMES' => 'Benefit Exception',
            'BENEFIT_DERIVATION_NAMES' => 'Benefit Derivation Exception',
            'PROV_TYPE_PROC_ASSOC_NAMES' => 'Provider Type Procedure Exception',
            'PROVIDER_TYPE_VALIDATION_NAMES' => 'Provider Type Validations Exception',
            'SUPER_BENEFIT_LIST_NAMES' => 'Super Benefit Exception ',
            'ENTITY_NAMES' => 'Procedure Cross Reference',
            'LIMITATIONS_LIST' => 'Limitations Exception'
        ];

        return $this->respondWithToken($this->token(), 'All Clone Table List', $table_list);
    }

    public function submitCopy(Request $request)
    {
        $uniqueColumns = DB::table('all_ind_columns')
            ->select('column_name')
            ->whereIn('index_name', function ($query) use ($request) {
                $query->select('index_name')
                    ->from('all_indexes')
                    ->where('table_name', $request->table_name)
                    ->where('uniqueness', 'UNIQUE');
            })
            ->where('table_name', $request->table_name)
            ->orderBy('column_position', 'desc')
            // ->latest()
            ->first();

        $ifExists = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->destination_id)
            ->get()
            ->count();
        // return $ifExists;
        if (strtoupper($request->source_id) == strtoupper($request->destination_id) || $ifExists >= 1) {
            return $this->respondWithToken($this->token(), 'Record Already Exists', '', false);
        }

        $get_source_record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->source_id)
            ->first();

        //chatgpt  code
        // return $uniqueColumns->column_name;
        $sourceCustomer = $request->source_id;
        $destinationCustomer = $request->destination_id;

        $record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $sourceCustomer)
            ->first();

        $newRecord = (array) $record;
        $newRecord[$uniqueColumns->column_name] = $destinationCustomer;

        $excludedColumns = [$uniqueColumns->column_name]; // Add any other duplicate column names here

        $columns = array_diff(array_keys($newRecord), [strtolower($excludedColumns[0])]);

        // to insert data  into db PARENT
        $copy_source_to_dest = DB::table($request->table_name)
            ->insert(array_intersect_key($newRecord, array_flip($columns)));

        $get_dest_record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->destination_id)
            ->first();

        //To insert data into child table
        $parent_table_name = [
            'DIAGNOSIS_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS', 'PHARMACY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS',
            'PRICING_STRATEGY_NAMES', 'COPAY_STRATEGY_NAMES',
            'ACCUM_BENE_STRATEGY_NAMES', 'GPI_EXCLUSION_LISTS', 'NDC_EXCLUSION_LISTS',
            'PHARMACY_TABLE', 'RX_NETWORK_NAMES', 'RX_NETWORK_RULE_NAMES', 'SUPER_RX_NETWORK_NAMES', 'SUPER_RX_NETWORK_NAMES',
            'COPAY_LIST', 'procedure_ucr_names', 'rva_names',
            'NDC_EXCEPTIONS', 'GPI_EXCEPTIONS', 'TC_EXCEPTIONS', 'DRUG_CATGY_EXCEPTION_NAMES', 'PROCEDURE_EXCEPTION_NAMES',
            'REASON_CODE_LIST_NAMES', 'BENEFIT_LIST_NAMES', 'BENEFIT_DERIVATION_NAMES', 'PROV_TYPE_PROC_ASSOC_NAMES', 'PROVIDER_TYPE_VALIDATION_NAMES',
            'PROCEDURE_EXCEPTION_NAMES', 'SUPER_BENEFIT_LIST_NAMES', 'ENTITY_NAMES',
        ];
        $child_table_names = [
            'DIAGNOSIS_VALIDATIONS', 'PHYSICIAN_VALIDATIONS', 'PHARMACY_VALIDATIONS', 'SPECIALTY_VALIDATIONS',
            'PRICING_STRATEGY', 'COPAY_STRATEGY',
            'ACCUM_BENEFIT_STRATEGY', 'GPI_EXCLUSIONS', 'NDC_EXCLUSIONS',
            'PHARMACY_VALIDATIONS', 'RX_NETWORKS', 'RX_NETWORK_RULES', 'SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS',
            'COPAY_MATRIX', 'PROCEDURE_UCR_LIST', 'rva_list',
            'NDC_EXCEPTION_LISTS', 'GPI_EXCEPTION_LISTS', 'TC_EXCEPTION_LISTS', 'PLAN_DRUG_CATGY_EXCEPTIONS', 'PROCEDURE_EXCEPTION_LISTS',
            'NDC_EXCEPTION_LISTS', 'BENEFIT_LIST', 'BENEFIT_DERIVATION', 'PROV_TYPE_PROC_ASSOC', 'PROVIDER_TYPE_VALIDATIONS',
            'PROCEDURE_EXCEPTION_LISTS', 'SUPER_BENEFIT_LISTS', 'PROCEDURE_XREF',
        ];

        if (in_array($request->table_name, $parent_table_name)) {
            $parent_key =  array_search($request->table_name, $parent_table_name); // to get a key of tbl name
            $child_table_name = $child_table_names[$parent_key]; //to get child table name

            $child_records = DB::table($child_table_name)
                ->where($uniqueColumns->column_name, $sourceCustomer)
                ->get();
            foreach ($child_records as $child) {
                $newRecord = (array)$child;
                $newRecord[$uniqueColumns->column_name] = $destinationCustomer;

                $excludedColumns = [$uniqueColumns->column_name];
                $columns = array_diff(array_keys($newRecord), [strtolower($excludedColumns[0])]);

                // to insert data  into db PARENT
                $copy_source_to_dest = DB::table($child_table_name)
                    ->insert(array_intersect_key($newRecord, array_flip($columns)));
            }
        }

        // To redirect URL to Modify
        $all_table_names = [
            'CUSTOMER', 'CLIENT', 'CLIENT_GROUP',  //user data module
            'DIAGNOSIS_EXCEPTIONS', //validation list module
            'PHYSICIAN_EXCEPTIONS', 'PHARMACY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS', 'ELIG_VALIDATION_LISTS', // validation list module
            'PRICING_STRATEGY_NAMES', 'COPAY_STRATEGY_NAMES', 'ACCUM_BENE_STRATEGY_NAMES', //strategies module
            'PLAN_LOOKUP_TABLE', 'PLAN_BENEFIT_TABLE', //plan design module
            'PLAN_ACCUM_DEDUCT_TABLE', // accumulated benedfits
            'GPI_EXCLUSION_LISTS',
            'NDC_EXCLUSION_LISTS',
            'MM_LIFE_MAX',
            'PHARMACY_CHAIN', //Provider module
            'PHARMACY_TABLE',
            'RX_NETWORK_NAMES',
            'RX_NETWORK_RULE_NAMES',
            'SUPER_RX_NETWORK_NAMES',
            'PRICE_SCHEDULE', // third party pricing
            'COPAY_SCHEDULE',
            'COPAY_LIST',
            'mac_table',
            'tax_schedule',
            'procedure_ucr_names',
            'rva_names',
            'NDC_EXCEPTIONS', // exception list
            'GPI_EXCEPTION_LISTS',
            'TC_EXCEPTIONS',
            'DRUG_CATGY_EXCEPTION_NAMES',
            'PROCEDURE_EXCEPTION_NAMES',
            'REASON_CODE_LIST_NAMES',
            'BENEFIT_LIST_NAMES',
            'BENEFIT_DERIVATION_NAMES',
            'PROV_TYPE_PROC_ASSOC_NAMES',
            'PROVIDER_TYPE_VALIDATION_NAMES',
            'PROCEDURE_EXCEPTION_NAMES',
            'SUPER_BENEFIT_LIST_NAMES',
            'ENTITY_NAMES',
            'LIMITATIONS_LIST',
        ];
        $table_url = [
            '/dashboard/user/customer', //user data module
            '/dashboard/user/client',
            '/dashboard/user/client-group',
            '/dashboard/validation-lists/diagnosis-validation', //validation list module
            '/dashboard/validation-lists/provider',
            '/dashboard/validation-lists/prescriber',
            '/dashboard/validation-lists/speciality',
            '/dashboard/validation-lists/eligibility',
            '/dashboard/strategies/pricing-startegy', //strategies module
            '/dashboard/strategies/copay-strategy',
            '/dashboard/strategies/accumulated-benefits-strategy',
            '/dashboard/plan-design/plan-association', // Plan design module
            '/dashboard/plan-design/plan-edit',
            '/dashboard/accumulated-benefits/all',
            '/dashboard/accumulated-benefits/gpi-exclusion',
            '/dashboard/accumulated-benefits/ndc-exclusion',
            '/dashboard/accumulated-benefits/major-medical-maximums',
            '/dashboard/provider/chains', //Provider module
            '/dashboard/provider/searchprovider',
            '/dashboard/provider/traditionalnetworks',
            '/dashboard/provider/flexiblenetworks',
            '/dashboard/provider/superprovider',
            '/dashboard/provider/prioritize-networks',
            '/dashboard/third-party-pricing/price-schedule', //Third party pricing
            '/dashboard/third-party-pricing/copay-schedule',
            '/dashboard/third-party-pricing/copay-step-schedule',
            '/dashboard/third-party-pricing/MAC-list',
            '/dashboard/third-party-pricing/tax-schedule',
            '/dashboard/third-party-pricing/procedure-UCR-list',
            '/dashboard/third-party-pricing/RAV-list',
            '/dashboard/exception-list/ndc', // exception list module
            '/dashboard/exception-list/gpi',
            '/dashboard/exception-list/therapy-class',
            '/dashboard/exception-list/drug-classification',
            '/dashboard/exception-list/procedure-exception',
            '/dashboard/exception-list/reason-code-exception',
            '/dashboard/exception-list/benefit-list',
            '/dashboard/exception-list/benefit-derivation',
            '/dashboard/exception-list/provider-type-procedure',
            '/dashboard/exception-list/provider-type-validation',
            '/dashboard/exception-list/procedure-code-list',
            '/dashboard/exception-list/super-benefit-list',
            '/dashboard/exception-list/procedure-cross-reference',
            '/dashboard/exception-list/limitations',

        ];
        $current_table =  array_search($request->table_name, $all_table_names);
        $modify_url = $table_url[$current_table];
        return $this->respondWithToken($this->token(), 'Record Cloned Successfully', [$get_dest_record, $modify_url]);
    }
}
