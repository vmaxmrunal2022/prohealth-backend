<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('CUSTOMER_ID', 10)->unique()->primary()->index();
            $table->string('CUSTOMER_NAME', 25)->nullable();
            $table->string('ADDRESS_1', 25)->nullable();
            $table->string('ADDRESS_2', 25)->nullable();
            $table->string('CITY', 18)->nullable();
            $table->string('STATE')->nullable();
            $table->string('ZIP_CODE')->nullable();
            $table->string('ZIP_PLUS_2')->nullable();
            $table->string('PHONE')->nullable();
            $table->string('FAX')->nullable();
            $table->string('CONTACT')->nullable();
            $table->string('EDI_ADDRESS')->nullable();
            $table->dateTime('EFFECTIVE_DATE', 10)->nullable();
            $table->dateTime('TERMINATION_DATE', 10)->nullable();
            $table->string('CUSTOMER_TYPE')->nullable();
            $table->decimal('CAP_AMOUNT', 13, 3)->nullable();
            $table->integer('COMM_CHARGE_PAID')->nullable();
            $table->integer('COMM_CHARGE_REJECT')->nullable();
            $table->dateTime('DATE_TIME_CREATED')->nullable();
            $table->string('USER_ID', 10)->nullable();
            $table->dateTime('DATE_TIME_MODIFIED')->nullable();
            $table->string('FORM_ID')->nullable();
            $table->char('PROCESSING_CYCLE')->nullable();
            $table->integer('AUTO_TERM_DAYS')->nullable();
            $table->integer('PRIOR_AUTH_BASIS_TYPE')->nullable();
            $table->integer('NON_FORMULARY_BASIS_TYPE')->nullable();
            $table->char('PLAN_ID_REQUIRED')->nullable();
            $table->integer('ADMIN_FEE')->nullable();
            $table->integer('DMR_FEE')->nullable();
            $table->integer('ADMIN_PERCENT')->nullable();
            $table->char('AUTO_TERM_LEVEL')->nullable();
            $table->char('OVERLAP_COVERAGE_TIE_BREAKER')->nullable();
            $table->char('AUTO_FAM_MEMBER_TERM')->nullable();
            $table->char('MEMBERSHIP_PROCESSING_FLAG')->nullable();
            $table->char('OTHER_COV_PROC_FLAG')->nullable();
            $table->dateTime('CENSUS_DATE')->nullable();
            $table->integer('NUM_OF_ACTIVE_CONTRACTS')->nullable();
            $table->integer('NUM_OF_ACTIVE_MEMBERS')->nullable();
            $table->integer('NUM_OF_TERMED_CONTRACTS')->nullable();
            $table->integer('NUM_OF_TERMED_MEMBERS')->nullable();
            $table->integer('NUM_OF_PENDING_CONTRACTS')->nullable();
            $table->integer('NUM_OF_PENDING_MEMBERS')->nullable();
            $table->char('ELIG_DATE_EDIT_OVR_FLAG')->nullable();
            $table->float('UCF_FEE', 12, 2)->nullable();
            $table->float('ELIG_UPD_FEE', 12, 2)->nullable();
            $table->float('PRIOR_AUTH_FEE', 12,2)->nullable();
            $table->float('MAIL_ORD_LETTER_FEE', 12,2)->nullable();
            $table->string('MSG_PRIORITY_ID', 10)->nullable();
            $table->string('DUR_EXCEPTION_LIST')->nullable();
            $table->integer('MAX_NUM_TRANS_INTERIM_ELIG')->nullable();
            $table->integer('MAX_DAYS_INTERIM_ELIG')->nullable();
            $table->string('STEP_THERAPY_ID')->nullable();
            $table->integer('COVERAGE_EFF_DATE_1')->nullable();
            $table->string('COVERAGE_STRATEGY_ID_1')->nullable();
            $table->string('PLAN_ID_1')->nullable();
            $table->string('MISC_DATA_1')->nullable();
            $table->integer('COVERAGE_EFF_DATE_2')->nullable();
            $table->string('COVERAGE_STRATEGY_ID_2')->nullable();
            $table->string('PLAN_ID_2')->nullable();
            $table->string('MISC_DATA_2')->nullable();
            $table->integer('COVERAGE_EFF_DATE_3')->nullable();
            $table->string('COVERAGE_STRATEGY_ID_3')->nullable();
            $table->string('PLAN_ID_3')->nullable();
            $table->string('MISC_DATA_3')->nullable();
            $table->char('ELIGIBILITY_EXCEPTIONS_FLAG')->nullable();
            $table->string('ELIG_VALIDATION_ID')->nullable();
            $table->char('PHARMACY_EXCEPTIONS_FLAG')->nullable();
            $table->string('SUPER_RX_NETWORK_ID')->nullable();
            $table->string('PRESCRIBER_EXCEPTIONS_FLAG')->nullable();
            $table->string('SUPER_MD_NETWORK_ID')->nullable();
            $table->string('MD_STRATEGY_ID')->nullable();
            $table->string('PHYSICIAN_TEMPLATE_ID')->nullable();
            $table->char('ACCUM_BENE_FAM_SUM_IND')->nullable();
            $table->string('USER_ID_CREATED')->nullable();
            $table->string('DRUG_COV_STRATEGY_ID_1')->nullable();
            $table->string('PREF_MAINT_DRUG_STRATEGY_ID_1')->nullable();
            $table->string('PRICING_STRATEGY_ID_1')->nullable();
            $table->string('COPAY_STRATEGY_ID_1')->nullable();
            $table->string('ACCUM_BENE_STRATEGY_ID_1')->nullable();
            $table->string('DRUG_COV_STRATEGY_ID_2')->nullable();
            $table->string('PREF_MAINT_DRUG_STRATEGY_ID_2')->nullable();
            $table->string('PRICING_STRATEGY_ID_2')->nullable();
            $table->string('COPAY_STRATEGY_ID_2')->nullable();
            $table->string('ACCUM_BENE_STRATEGY_ID_2')->nullable();
            $table->string('DRUG_COV_STRATEGY_ID_3')->nullable();
            $table->string('PREF_MAINT_DRUG_STRATEGY_ID_3')->nullable();
            $table->string('PRICING_STRATEGY_ID_3')->nullable();
            $table->string('COPAY_STRATEGY_ID_3')->nullable();
            $table->string('ACCUM_BENE_STRATEGY_ID_3')->nullable();
            $table->string('GENERIC_CODE_CONV_ID')->nullable();
            $table->string('DATE_WRITTEN_TO_FIRST_FILL')->nullable();
            $table->string('DATE_FILLED_TO_SUB_ONLINE')->nullable();
            $table->string('DATE_FILLED_TO_SUB_DMR')->nullable();
            $table->string('DATE_SUB_TO_FILLED_FUTURE')->nullable();
            $table->integer('DAYS_FOR_REVERSALS')->nullable();
            $table->char('NON_PROFIT_TAX_EXEMPT_FLAG')->nullable();
            $table->char('REQD_U_AND_C_FLAG')->nullable();
            $table->char('EXCL_PLAN_NDC_GPI_EXCEP_FLAG')->nullable();
            $table->char('EXCL_SYS_NDC_GPI_EXCEP_FLAG')->nullable();
            $table->char('AUTH_XFER_IND')->nullable();
            $table->char('ELIG_TYPE')->nullable();
            $table->char('PRESCRIBER_EXCEPTIONS_FLAG_2')->nullable();
            $table->char('MEMBER_CHANGE_LOG_OPT')->nullable();
            $table->string('PHYS_FILE_SRCE_ID')->nullable();
            $table->string('SHOEBOX_ACCESS')->nullable();
            $table->string('COUNTRY')->nullable();
            $table->integer('POLICY_ANNIV_MONTH')->nullable();
            $table->integer('POLICY_ANNIV_DAY')->nullable();
            $table->integer('SMBPP')->nullable();
            $table->string('RVA_LIST_ID')->nullable();
            $table->string('COUNTRY_CODE')->nullable();
            $table->char('HISTORY_XFER_IND')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
