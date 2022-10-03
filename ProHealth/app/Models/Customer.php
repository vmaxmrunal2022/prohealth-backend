<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'CUSTOMER_ID', 'CUSTOMER_NAME', 'ADDRESS_1', 'ADDRESS_2', 'CITY', 'STATE', 'ZIP_CODE', 'ZIP_PLUS_2', 'PHONE', 'FAX', 'CONTACT', 'EDI_ADDRESS',
        'EFFECTIVE_DATE', 'TERMINATION_DATE', 'CUSTOMER_TYPE', 'CAP_AMOUNT', 'COMM_CHARGE_PAID', 'COMM_CHARGE_REJECT', 'DATE_TIME_CREATED',
        'USER_ID', 'DATE_TIME_MODIFIED', 'FORM_ID', 'PROCESSING_CYCLE', 'AUTO_TERM_DAYS', 'PRIOR_AUTH_BASIS_TYPE', 'AUTO_TERM_DAYS', 'PRIOR_AUTH_BASIS_TYPE',
        'NON_FORMULARY_BASIS_TYPE', 'PLAN_ID_REQUIRED', 'ADMIN_FEE', 'DMR_FEE', 'ADMIN_PERCENT', 'AUTO_TERM_LEVEL', 'OVERLAP_COVERAGE_TIE_BREAKER',
        'AUTO_FAM_MEMBER_TERM', 'MEMBERSHIP_PROCESSING_FLAG', 'OTHER_COV_PROC_FLAG', 'CENSUS_DATE', 'NUM_OF_ACTIVE_CONTRACTS', 'NUM_OF_ACTIVE_MEMBERS',
        'NUM_OF_TERMED_CONTRACTS', 'NUM_OF_TERMED_MEMBERS', 'NUM_OF_PENDING_CONTRACTS', 'NUM_OF_PENDING_MEMBERS', 'ELIG_DATE_EDIT_OVR_FLAG', 'UCF_FEE',
        'ELIG_UPD_FEE', 'PRIOR_AUTH_FEE', 'MAIL_ORD_LETTER_FEE', 'MSG_PRIORITY_ID', 'DUR_EXCEPTION_LIST', 'MAX_NUM_TRANS_INTERIM_ELIG', 'MAX_DAYS_INTERIM_ELIG',
        'STEP_THERAPY_ID', 'COVERAGE_EFF_DATE_1', 'COVERAGE_STRATEGY_ID_1', 'PLAN_ID_1', 'MISC_DATA_1', 'COVERAGE_EFF_DATE_2', 'COVERAGE_STRATEGY_ID_2',
        'PLAN_ID_2', 'MISC_DATA_2', 'COVERAGE_EFF_DATE_3', 'COVERAGE_STRATEGY_ID_3', 'PLAN_ID_3', 'MISC_DATA_3', 'ELIGIBILITY_EXCEPTIONS_FLAG', 'ELIG_VALIDATION_ID',
        'PHARMACY_EXCEPTIONS_FLAG', 'SUPER_RX_NETWORK_ID', 'PRESCRIBER_EXCEPTIONS_FLAG', 'SUPER_MD_NETWORK_ID', 'MD_STRATEGY_ID', 'PHYSICIAN_TEMPLATE_ID',
        'ACCUM_BENE_FAM_SUM_IND', 'USER_ID_CREATED', 'DRUG_COV_STRATEGY_ID_1', 'PREF_MAINT_DRUG_STRATEGY_ID_1', 'PREF_MAINT_DRUG_STRATEGY_ID_1',
        'PRICING_STRATEGY_ID_1', 'ACCUM_BENE_STRATEGY_ID_1', 'DRUG_COV_STRATEGY_ID_2', 'PREF_MAINT_DRUG_STRATEGY_ID_2', 'PRICING_STRATEGY_ID_2',
        'COPAY_STRATEGY_ID_2', 'ACCUM_BENE_STRATEGY_ID_2', 'DRUG_COV_STRATEGY_ID_3', 'PREF_MAINT_DRUG_STRATEGY_ID_3', 'PRICING_STRATEGY_ID_3',
        'COPAY_STRATEGY_ID_3', 'ACCUM_BENE_STRATEGY_ID_3', 'GENERIC_CODE_CONV_ID', 'DATE_WRITTEN_TO_FIRST_FILL', 'DATE_FILLED_TO_SUB_ONLINE', 'DATE_FILLED_TO_SUB_DMR',
        'DATE_SUB_TO_FILLED_FUTURE', 'DAYS_FOR_REVERSALS', 'NON_PROFIT_TAX_EXEMPT_FLAG', 'REQD_U_AND_C_FLAG', 'EXCL_PLAN_NDC_GPI_EXCEP_FLAG', 'EXCL_SYS_NDC_GPI_EXCEP_FLAG',
        'AUTH_XFER_IND', 'ELIG_TYPE', 'PRESCRIBER_EXCEPTIONS_FLAG_2', 'MEMBER_CHANGE_LOG_OPT', 'PHYS_FILE_SRCE_ID', 'SHOEBOX_ACCESS', 'COUNTRY', 'POLICY_ANNIV_MONTH',
        'POLICY_ANNIV_DAY', 'SMBPP', 'RVA_LIST_ID', 'COUNTRY_CODE', 'HISTORY_XFER_IND'
    ];
}
