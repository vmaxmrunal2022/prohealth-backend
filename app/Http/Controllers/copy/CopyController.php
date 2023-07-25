<?php

namespace App\Http\Controllers\copy;

use App\Http\Controllers\Controller;
use App\Traits\AuditTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class CopyController extends Controller
{
    use AuditTrait;
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
                    // ->where('table_name', $table_name)
                    ->where(DB::raw('UPPER(table_name)'), strtoupper($table_name))
                    ->where('uniqueness', 'UNIQUE');
            })
            // ->where('table_name', $table_name)
            ->where(DB::raw('UPPER(table_name)'), strtoupper($table_name))
            ->orderBy('column_position', 'desc')
            // ->latest()
            ->first();
        // return $unsiqueColumns;
        if (empty($uniqueColumns)) {
            return $this->respondWithToken($this->token(), 'Unique Source ID Not Found', '', false);
        }
        // return $uniqueColumns;
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
            //user data
            'CUSTOMER' => 'Customer',
            'CLIENT' => 'Client',
            'CLIENT_GROUP' => 'Client Group',
            //validation list
            'DIAGNOSIS_EXCEPTIONS' => 'Diagnosis Validation List',
            'NDC_EXCEPTIONS' => 'NDC Validation List',
            'PHYSICIAN_EXCEPTIONS' => 'Prescriber Validation List',
            'PHARMACY_EXCEPTIONS' => 'Provider Validation List',
            'SPECIALTY_EXCEPTIONS' => 'Speciality Validation List',
            'ELIG_VALIDATION_LISTS' => 'Eligibility Validation List',
            // 'DIAGNOSIS_VALIDATIONS' => 'Diagnosis Prioritization',
            //strategies module
            'PRICING_STRATEGY_NAMES' => 'Pricing Strategy',
            'COPAY_STRATEGY_NAMES' => 'Copay Strategy',
            'ACCUM_BENE_STRATEGY_NAMES' => 'Accum Benefit Startegy',
            //plan design module
            // 'PLAN_LOOKUP_TABLE' => 'Plan Association', 
            'PLAN_BENEFIT_TABLE' => 'Plan Edit',
            // accumulated benedfits
            'PLAN_ACCUM_DEDUCT_TABLE' => 'Accumulated Benefits',
            'GPI_EXCLUSIONS' => 'GPI Exclusion List',
            'NDC_EXCLUSIONS' => 'NDC Exclusion List',
            // 'MM_LIFE_MAX' => 'Major Medical Maximus',
            //Provider module
            // 'PHARMACY_CHAIN' => 'Pharmacy Chain', 
            // 'PHARMACY_TABLE' => 'Provider',
            'RX_NETWORK_NAMES' => 'Traditional Network',
            'RX_NETWORK_RULE_NAMES' => 'Flexible Network',
            'SUPER_RX_NETWORK_NAMES' => 'Super Provider Network',
            //Third party pricing
            'PRICE_SCHEDULE' => 'Price Schedule',
            'COPAY_SCHEDULE' => 'Copay Schedule',
            'COPAY_LIST' => 'Copay Step Schedule',
            'MAC_LIST' => 'MAC List',
            'tax_schedule' => 'Tax Schedule',
            'procedure_ucr_names' => 'Procedure UCR List',
            'rva_names' => 'RVA List',
            //Exception 
            'NDC_EXCEPTIONS' => 'NDC Exception',
            'GPI_EXCEPTIONS' => 'GPI Exception',
            'TC_EXCEPTIONS' => 'Therapy Class',
            'DRUG_CATGY_EXCEPTION_NAMES' => 'Drug Classification Exception',
            'PROC_CODE_LIST_NAMES' => 'Procedure Code List Exception',
            'PROCEDURE_EXCEPTION_NAMES' => 'Procedure Exceptions',
            'REASON_CODE_LIST_NAMES' => 'Reason Code Exception',
            'BENEFIT_LIST_NAMES' => 'Benefit Exception',
            'BENEFIT_DERIVATION_NAMES' => 'Benefit Derivation Exception',
            'PROV_TYPE_PROC_ASSOC_NAMES' => 'Provider Type Procedure Exception',
            'PROVIDER_TYPE_VALIDATION_NAMES' => 'Provider Type Validations Exception',
            'PROCEDURE_EXCEPTION_LISTS' => 'Procedure Code Exception',
            'SUPER_BENEFIT_LIST_NAMES' => 'Super Benefit Exception ',
            'ENTITY_NAMES' => 'Procedure Cross Reference',
            'LIMITATIONS_LIST' => 'Limitations Exception'
        ];

        return $this->respondWithToken($this->token(), 'All Clone Table List', [$table_list]);
    }

    public function submitCopy(Request $request)
    {
        $all_table_names = [
            //exception list
            'NDC_EXCEPTION_LISTS',
            'GPI_EXCEPTIONS',
            'TC_EXCEPTIONS',
            'DRUG_CATGY_EXCEPTION_NAMES',
            'PROCEDURE_EXCEPTION_NAMES',
            'REASON_CODE_LIST_NAMES',
            'BENEFIT_LIST_NAMES',
            'BENEFIT_DERIVATION_NAMES',
            'PROV_TYPE_PROC_ASSOC_NAMES',
            'PROVIDER_TYPE_VALIDATION_NAMES',
            'PROC_CODE_LIST_NAMES',
            'SUPER_BENEFIT_LIST_NAMES',
            'ENTITY_NAMES',
            'LIMITATIONS_LIST',
            //Validation list
            'DIAGNOSIS_EXCEPTIONS',
            'SPECIALTY_EXCEPTIONS',
            'ELIG_VALIDATION_LISTS',
            'PHARMACY_EXCEPTIONS',
            'PHYSICIAN_EXCEPTIONS',
            'DIAGNOSIS_VALIDATIONS',
            //Provider
            'PHARMACY_CHAIN',
            'PHARMACY_TABLE',
            'RX_NETWORK_NAMES',
            'RX_NETWORK_RULE_NAMES',
            'SUPER_RX_NETWORK_NAMES',
            //Accumulated Benefit
            'PLAN_ACCUM_DEDUCT_TABLE',
            'GPI_EXCLUSIONS',
            'NDC_EXCLUSIONS',
            'MM_LIFE_MAX',
            //Plan Association
            'PLAN_LOOKUP_TABLE',
            'PLAN_BENEFIT_TABLE',
            //User Data
            'CUSTOMER',
            'CLIENT',
            'CLIENT_GROUP',
            //Third Party Pricing
            'PRICE_SCHEDULE',
            'COPAY_SCHEDULE',
            'COPAY_LIST',
            'MAC_LIST',
            'TAX_SCHEDULE',
            'PROCEDURE_UCR_NAMES',
            'RVA_NAMES',
            //Strategies
            'PRICING_STRATEGY_NAMES',
            'COPAY_STRATEGY_NAMES',
            'ACCUM_BENE_STRATEGY_NAMES',
        ];
        $table_url = [
            //exception list
            '/dashboard/exception-list/ndc',
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
            //Validation list
            '/dashboard/validation-lists/diagnosis-validation',
            '/dashboard/validation-lists/speciality',
            '/dashboard/validation-lists/eligibility',
            '/dashboard/validation-lists/provider',
            '/dashboard/validation-lists/prescriber',
            '/dashboard/validation-lists/diagnosis-prioritization',
            //Provider
            '/dashboard/provider/chains',
            '/dashboard/provider/searchprovider',
            '/dashboard/provider/traditionalnetworks',
            '/dashboard/provider/flexiblenetworks',
            '/dashboard/provider/superprovider',
            //Accumulated Benefit
            '/dashboard/accumulated-benefits/all',
            '/dashboard/accumulated-benefits/gpi-exclusion',
            '/dashboard/accumulated-benefits/ndc-exclusion',
            '/dashboard/accumulated-benefits/major-medical-maximums',
            //Plan Association
            '/dashboard/plan-design/plan-association',
            '/dashboard/plan-design/plan-edit',
            //User Data
            '/dashboard/user/customer',
            '/dashboard/user/client',
            '/dashboard/user/client-group',
            //Third Party Pricing
            '/dashboard/third-party-pricing/price-schedule',
            '/dashboard/third-party-pricing/copay-schedule',
            '/dashboard/third-party-pricing/copay-step-schedule',
            '/dashboard/third-party-pricing/MAC-list',
            '/dashboard/third-party-pricing/tax-schedule',
            '/dashboard/third-party-pricing/procedure-UCR-list',
            '/dashboard/third-party-pricing/RAV-list',
            //Strategies
            '/dashboard/strategies/pricing-startegy',
            '/dashboard/strategies/copay-strategy',
            '/dashboard/strategies/accumulated-benefits-strategy',

        ];

        $parent_table_name = [
            'DIAGNOSIS_EXCEPTIONS', 'PHYSICIAN_EXCEPTIONS', 'PHARMACY_EXCEPTIONS', 'SPECIALTY_EXCEPTIONS',
            'PRICING_STRATEGY_NAMES', 'COPAY_STRATEGY_NAMES',
            'ACCUM_BENE_STRATEGY_NAMES', 'GPI_EXCLUSIONS', 'NDC_EXCLUSIONS',
            'PHARMACY_TABLE', 'RX_NETWORK_NAMES', 'RX_NETWORK_RULE_NAMES', 'SUPER_RX_NETWORK_NAMES', 'SUPER_RX_NETWORK_NAMES',
            'COPAY_LIST', 'procedure_ucr_names', 'rva_names',
            //exception list module
            'NDC_EXCEPTIONS', 'GPI_EXCEPTIONS', 'TC_EXCEPTIONS', 'DRUG_CATGY_EXCEPTION_NAMES', 'PROCEDURE_EXCEPTION_NAMES',
            'REASON_CODE_LIST_NAMES', 'BENEFIT_LIST_NAMES', 'BENEFIT_DERIVATION_NAMES', 'PROV_TYPE_PROC_ASSOC_NAMES', 'PROVIDER_TYPE_VALIDATION_NAMES',
            'PROCEDURE_EXCEPTION_NAMES', 'SUPER_BENEFIT_LIST_NAMES', 'ENTITY_NAMES', 'MAC_LIST', 'PROC_CODE_LIST_NAMES',
        ];
        $child_table_names = [
            'DIAGNOSIS_VALIDATIONS', 'PHYSICIAN_VALIDATIONS', 'PHARMACY_VALIDATIONS', 'SPECIALTY_VALIDATIONS',
            'PRICING_STRATEGY', 'COPAY_STRATEGY',
            'ACCUM_BENEFIT_STRATEGY', 'GPI_EXCLUSION_LISTS', 'NDC_EXCLUSION_LISTS',
            'PHARMACY_VALIDATIONS', 'RX_NETWORKS', 'RX_NETWORK_RULES', 'SUPER_RX_NETWORKS', 'SUPER_RX_NETWORKS',
            'COPAY_MATRIX', 'PROCEDURE_UCR_LIST', 'rva_list',
            //exception list module
            'NDC_EXCEPTION_LISTS', 'GPI_EXCEPTION_LISTS', 'TC_EXCEPTION_LISTS', 'PLAN_DRUG_CATGY_EXCEPTIONS', 'PROCEDURE_EXCEPTION_LISTS',
            'REASON_CODE_LISTS', 'BENEFIT_LIST', 'BENEFIT_DERIVATION', 'PROV_TYPE_PROC_ASSOC', 'PROVIDER_TYPE_VALIDATIONS',
            'PROCEDURE_EXCEPTION_LISTS', 'SUPER_BENEFIT_LISTS', 'PROCEDURE_XREF', 'MAC_TABLE', 'PROC_CODE_LISTS'
        ];

        $uniqueColumns = DB::table('all_ind_columns')
            ->select('column_name')
            ->whereIn('index_name', function ($query) use ($request) {
                $query->select('index_name')
                    ->from('all_indexes')
                    ->where(DB::raw('UPPER(table_name)'), strtoupper($request->table_name))
                    ->where('uniqueness', 'UNIQUE');
            })
            ->where(DB::raw('UPPER(table_name)'), strtoupper($request->table_name))
            ->orderBy('column_position', 'desc')
            // ->latest()
            ->first();

        // return $uniqueColumns->column_name;
        $current_table =  array_search(strtoupper($request->table_name), $all_table_names);
        $modify_url = $table_url[$current_table];

        $ifExists = DB::table($request->table_name)
            ->where(DB::raw('UPPER(' . $uniqueColumns->column_name . ')'), strtoupper($request->destination_id))
            ->get()
            ->count();
        if (strtoupper($request->source_id) == strtoupper($request->destination_id) || $ifExists >= 1) {
            return $this->respondWithToken($this->token(), 'Record Already Exists', ['', $modify_url], false);
        }

        $get_source_record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->source_id)
            ->first();

        $sourceCustomer = $request->source_id;
        $destinationCustomer = $request->destination_id;

        $record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $sourceCustomer)
            ->first();



            $tableName = $request->table_name;
            $columns1 = ['effective_date', 'termination_date'];
            $existingColumns1 = Schema::getColumnListing($tableName);

                    $columnsExist1 = true;
                    foreach ($columns1 as $column1) {
                        if (!in_array($column1, $existingColumns1)) {
                            $columnsExist1 = false;
                            break;
                        }
                    }



                    $columns2 = ['effective_date', 'termination_date'];
                    $existingColumns2 = Schema::getColumnListing($tableName);
        
                            $columnsExist2= true;
                            foreach ($columns2 as $column2) {
                                if (!in_array($column2, $existingColumns2)) {
                                    $columnsExist2 = false;
                                    break;
                                }
                            }

                if ($columnsExist1) {
                
                    $newRecord = (array) $record;
                    $newRecord[$uniqueColumns->column_name] = $destinationCustomer;
                    $newRecord['form_id'] = 'COPY';
                    $newRecord['date_time_modified'] = date('d-F-y');
                    $newRecord['date_time_created'] = date('d-F-y');
                    $newRecord['effective_date']=$request->effective_date;
                    $newRecord['termination_date']=$request->termination_date;
                    $newRecord['customer_name']=$request->description;
                } else if($columnsExist2){
                    $newRecord = (array) $record;
                    $newRecord[$uniqueColumns->column_name] = $destinationCustomer;
                    $newRecord['form_id'] = 'COPY';
                    $newRecord['date_time_modified'] = date('d-F-y');
                    $newRecord['date_time_created'] = date('d-F-y');
                    $newRecord['effective_date']=$request->effective_date;
                    $newRecord['termination_date']=$request->termination_date;
                    $newRecord['customer_name']=$request->description;
                }else{

                    $newRecord = (array) $record;
                    $newRecord[$uniqueColumns->column_name] = $destinationCustomer;
                    $newRecord['form_id'] = 'COPY';
                    $newRecord['date_time_modified'] = date('d-F-y');
                    $newRecord['date_time_created'] = date('d-F-y');

                }





        // return $newRecord;
        $excludedColumns = [
            $uniqueColumns->column_name, 'form_id' => 'COPY', 'date_time_modified' => date('d-F-y'),
            'date_time_created' => date('d-F-y')
        ]; // Add any other duplicate column names here

        // return $excludedColumns;

        $columns = array_diff(array_keys($newRecord), [
            strtolower($excludedColumns[0]), $excludedColumns['form_id'],
            $excludedColumns['date_time_modified'], $excludedColumns['date_time_created']
        ]);

        // to insert data  into db PARENT
        $copy_source_to_dest = DB::table($request->table_name)
            ->insert(array_intersect_key($newRecord, array_flip($columns)));

        $get_dest_record = DB::table($request->table_name)
            ->where($uniqueColumns->column_name, $request->destination_id)
            ->first();

        //Add to Audit 
        $record_snapshot = json_encode($get_dest_record);
        $save_audit = $this->auditMethod('IN', $record_snapshot, strtoupper($request->table_name));

        //To insert data into child table


        if (in_array($request->table_name, $parent_table_name)) {
            $parent_key =  array_search($request->table_name, $parent_table_name); // to get a key of tbl name
            $child_table_name = $child_table_names[$parent_key]; //to get child table name

            $child_records = DB::table($child_table_name)
                ->where(DB::raw('UPPER(' . $uniqueColumns->column_name . ')'), strtoupper($sourceCustomer))
                ->get();
            foreach ($child_records as $child) {
                $newRecord = (array)$child;
                $newRecord[$uniqueColumns->column_name] = $destinationCustomer;
                $newRecord['effective_date']=$request->effective_date;
                $newRecord['termination_date']=$request->termination_date;

                $excludedColumns = [$uniqueColumns->column_name,'effective_date','termination_date'];
                $columns = array_diff(array_keys($newRecord), [strtolower($excludedColumns[0])]);

                // to insert data  into db PARENT
                $copy_source_to_dest = DB::table($child_table_name)
                    ->insert(array_intersect_key($newRecord, array_flip($columns)));
            }

            //for audit 
            foreach ($child_records as $child) {
                // $record_snapshot = json_encode($child);
                // $save_audit = $this->auditMethod('IN', json_encode($child), $child_table_name);
            }
        }

        // To redirect URL to Modify
        $current_table =  array_search(strtoupper($request->table_name), $all_table_names);
        $modify_url = $table_url[$current_table];
        // return $modify_url;
        return $this->respondWithToken($this->token(), 'Record Cloned Successfully', [$get_dest_record, $modify_url]);
    }
}
