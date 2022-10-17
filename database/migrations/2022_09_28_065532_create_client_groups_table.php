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
        Schema::create('client_groups', function (Blueprint $table) {
            $table->string('CUSTOMER_ID');
            $table->string('CLIENT_ID');
            $table->string('CLIENT_GROUP_ID');
            $table->string('GROUP_NAME')->nullable();
            $table->string('ADDRESS_1')->nullable();
            $table->string('ADDRESS_2')->nullable();
            $table->string('CITY')->nullable();
            $table->string('STATE')->nullable();
            $table->string('ZIP_CODE')->nullable();
            $table->string('ZIP_PLUS_2')->nullable();
            $table->string('PHONE')->nullable();
            $table->string('FAX')->nullable();
            $table->string('EDI_ADDRESS')->nullable();
            $table->string('CONTACT')->nullable();
            $table->string('PLAN_ID')->nullable();
            $table->string('EFFECTIVE_DATE')->nullable();
            $table->string('MARKETING_REP_ID')->nullable();
            $table->string('ANNIV_DATE')->nullable();
            $table->string('GROUP_EFFECTIVE_DATE')->nullable();
            $table->string('GROUP_TERMINATION_DATE')->nullable();
            $table->integer('CAP_AMOUNT')->nullable();
            $table->integer('COMM_CHARGE_PAID')->nullable();
            $table->integer('COMM_CHARGE_REJECT')->nullable();
            $table->dateTime('DATE_TIME_CREATED')->nullable();
            $table->string('USER_ID')->nullable();
            $table->dateTime('DATE_TIME_MODIFIED')->nullable();
            $table->string('FORM_ID')->nullable();
            $table->integer('AUTO_TERM_DAYS')->nullable();
            $table->integer('ADMIN_FEE')->nullable();
            $table->string('COPAY_SCHED_OVR')->nullable();
            $table->string('CENSUS_DATE')->nullable();
            $table->string('MISC_DATA_1')->nullable();
            $table->string('PLAN_ID_2')->nullable();
            $table->string('MISC_DATA_2')->nullable();
            $table->string('PLAN_ID_3')->nullable();
            $table->string('MISC_DATA_3')->nullable();
            $table->string('ELIG_VALIDATION_ID')->nullable();
            $table->string('SUPER_RX_NETWORK_ID')->nullable();
            $table->string('MD_STRATEGY_ID')->nullable();
            $table->string('USER_ID_CREATED')->nullable();
            $table->string('PHYS_FILE_SRCE_ID')->nullable();
            $table->string('COUNTRY')->nullable();
            $table->string('RVA_LIST_ID')->nullable();
            $table->string('COUNTRY_CODE')->nullable();

            $table->integer('DMR_FEE')->nullable();
            $table->integer('ADMIN_PERCENT')->nullable();
            $table->integer('NUM_OF_ACTIVE_CONTRACTS')->nullable();
            $table->integer('NUM_OF_ACTIVE_MEMBERS')->nullable();
            $table->integer('NUM_OF_TERMED_CONTRACTS')->nullable();
            $table->integer('NUM_OF_TERMED_MEMBERS')->nullable();
            $table->integer('NUM_OF_PENDING_CONTRACTS')->nullable();
            $table->integer('NUM_OF_PENDING_MEMBERS')->nullable();
            $table->integer('UCF_FEE')->nullable();
            $table->integer('ELIG_UPD_FEE')->nullable();
            $table->integer('PRIOR_AUTH_FEE')->nullable();
            $table->integer('MAIL_ORD_LETTER_FEE')->nullable();
            $table->integer('MAX_NUM_TRANS_INTERIM_ELIG')->nullable();
            $table->integer('MAX_DAYS_INTERIM_ELIG')->nullable();
            $table->integer('COVERAGE_EFF_DATE_1')->nullable();
            $table->integer('COVERAGE_EFF_DATE_2')->nullable();
            $table->integer('COVERAGE_EFF_DATE_3')->nullable();
            $table->integer('DATE_WRITTEN_TO_FIRST_FILL')->nullable();
            $table->integer('DATE_FILLED_TO_SUB_ONLINE')->nullable();
            $table->integer('DATE_FILLED_TO_SUB_DMR')->nullable();
            $table->integer('DATE_SUB_TO_FILLED_FUTURE')->nullable();
            $table->integer('DAYS_FOR_REVERSALS')->nullable();
            $table->integer('POLICY_ANNIV_MONTH')->nullable();
            $table->integer('POLICY_ANNIV_DAY')->nullable();
            $table->integer('SMBPP')->nullable();


            $table->char('AUTO_FAM_MEMBER_TERM')->nullable();
            $table->char('COPAY_SCHED_OVR_FLAG')->nullable();
            $table->char('MEMBERSHIP_PROCESSING_FLAG')->nullable();
            $table->char('PERSON_CODE_REQD_FLAG')->nullable();
            $table->char('OTHER_COV_PROC_FLAG')->nullable();
            $table->char('ELIG_DATE_EDIT_OVR_FLAG')->nullable();
            $table->char('ELIGIBILITY_EXCEPTIONS_FLAG')->nullable();
            $table->char('PHARMACY_EXCEPTIONS_FLAG')->nullable();
            $table->char('PRESCRIBER_EXCEPTIONS_FLAG')->nullable();
            $table->char('ACCUM_BENE_FAM_SUM_IND')->nullable();
            $table->char('NON_PROFIT_TAX_EXEMPT_FLAG')->nullable();
            $table->char('REQD_U_AND_C_FLAG')->nullable();
            $table->char('EXCL_PLAN_NDC_GPI_EXCEP_FLAG')->nullable();
            $table->char('EXCL_SYS_NDC_GPI_EXCEP_FLAG')->nullable();
            $table->char('PLAN_CLASSIFICATION')->nullable();
            $table->char('ELIG_TYPE')->nullable();
            $table->char('PRESCRIBER_EXCEPTIONS_FLAG_2')->nullable();
            $table->char('MEMBER_CHANGE_LOG_OPT')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_groups');
    }
};
