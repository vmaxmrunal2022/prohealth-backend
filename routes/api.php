<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientGroupController;
use App\Http\Controllers\Code\BenifitController;
use App\Http\Controllers\Code\CouseOfLossController;
use App\Http\Controllers\Code\DiagnosisController;
use App\Http\Controllers\Code\ProcedureController;
use App\Http\Controllers\Code\ProviderTypeController;
use App\Http\Controllers\Code\ReasonsController;
use App\Http\Controllers\Code\ServiceModifierController;
use App\Http\Controllers\Code\ServiceTypeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Exception\BenefitListController;
use App\Http\Controllers\Exception\GPIExceptionController;
use App\Http\Controllers\Exception\NDCExceptionController;

use App\Http\Controllers\ValidationLists\ValidationListsController;

use App\Http\Controllers\ValidationLists\PrescriberValidationController;




use App\Http\Controllers\Speciality\SpecialityController;

use App\Http\Controllers\ValidationLists\EligibilityValidationListController;

use App\Http\Controllers\ValidationLists\ProviderController;


use App\Http\Controllers\ValidationLists\DiagnosisValidationListController;
use App\Http\Controllers\Strategies\PricingStrategyController;

use App\Http\Controllers\Strategies\CopayStrategyController;
use App\Http\Controllers\Strategies\AccumlatedController;
use App\Http\Controllers\AccumLatedBenifits\AccumlatedBenifitController;
use App\Http\Controllers\AccumLatedBenifits\GpiExclusionController;
use App\Http\Controllers\AccumLatedBenifits\NdcExlusionController;
use App\Http\Controllers\AccumLatedBenifits\MajorMedicalController;
use App\Http\Controllers\administrator\AuditTrailController;
use App\Http\Controllers\administrator\ClaimHistoryController;
use App\Http\Controllers\administrator\SystemParameterController;
use App\Http\Controllers\administrator\UserDefinationController;
use App\Http\Controllers\Provider\SuperProviderNetworkController;
use App\Http\Controllers\Provider\TraditionalNetworkController;
use App\Http\Controllers\Provider\PrioritiseNetworkController;

use App\Http\Controllers\PrescriberData\PrescriberController;






use App\Http\Controllers\Exception\ProcedureController as ExceptionProcedureController;
use App\Http\Controllers\Exception\TherapyClassController;
use App\Http\Controllers\drug_information\DrugDatabaseController;
use App\Http\Controllers\exception_list\PrcedureCodeListController;
use App\Http\Controllers\exception_list\ProviderTypeValidationController;
use App\Http\Controllers\plan_design\PlanAssociationController;
use App\Http\Controllers\exception_list\SuperBenefitControler;
use App\Http\Controllers\membership\MemberController;
use App\Http\Controllers\membership\PlanValidationController;
use App\Http\Controllers\membership\PriorAuthController;
use App\Http\Controllers\plan_design\PlanEditController;
use App\Http\Controllers\third_party_pricing\CopayScheduleController;
use App\Http\Controllers\third_party_pricing\CopayStepScheduleController;
use App\Http\Controllers\third_party_pricing\MacListController;
use App\Http\Controllers\third_party_pricing\PriceScheduleController;
use App\Http\Controllers\third_party_pricing\ProcedureUcrList;
use App\Http\Controllers\third_party_pricing\RvaListController;
use App\Http\Controllers\third_party_pricing\TaxScheduleController;
use App\Http\Controllers\UserController;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Nette\Schema\Context;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'users'], function ($router) {
    Route::post('/register', [UserController::class, 'register'])->name('register.user');
    Route::post('/login', [UserController::class, 'login'])->name('login.user');
    Route::get('/view-profile', [UserController::class, 'viewProfile'])->name('profile.user');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout.user');
});


Route::group(['prefix' => 'codes'], function ($router) {

    // BENEFITS
    Route::get('/benefits', [BenifitController::class, 'get'])->name('benefit.get'); // SEARCH
    Route::post('/benefits/submit', [BenifitController::class, 'add'])->name('benefit.submit');  // add
    Route::post('/benefits/delete', [BenifitController::class, 'delete'])->name('benefit.delete'); // DELETE
    Route::get('/check-benifit-exist', [BenifitController::class, 'checkBenifitCodeExist']);

    // REASON CODES
    Route::get('/reasons', [ReasonsController::class, 'get'])->name('reasons.get'); // SEARCH
    Route::post('/reasons/submit', [ReasonsController::class, 'add'])->name('reasons.submit'); // add
    Route::post('/reasons/delete', [ReasonsController::class, 'delete'])->name('reasons.delete'); // DELETE
    Route::get('/check-reason-exist', [ReasonsController::class, 'checkReasonExist']);



    // PROCEDURE 
    Route::get('/procedure', [ProcedureController::class, 'get'])->name('procedure.get'); // SEARCH
    Route::post('/procedure/submit', [ProcedureController::class, 'add'])->name('procedure.submit');  // add
    Route::post('/procedure/delete', [ProcedureController::class, 'delete'])->name('procedure.delete'); // DELETE
    Route::get('/check-procedure-exist', [ProcedureController::class, 'checkProcedureCodeExist']);


    // PROVIDER TYPE
    Route::get('/provider-type', [ProviderTypeController::class, 'get'])->name('providertype.get'); // SEARCH
    Route::post('/provider-type/submit', [ProviderTypeController::class, 'add'])->name('providertype.submit');  // add
    Route::post('/provider-type/delete', [ProviderTypeController::class, 'delete'])->name('providertype.delete'); // DELETE
    Route::get('/check-provider-type-exist', [ProviderTypeController::class, 'checkProviderTypeExist']);

    // DIAGNOSIS
    Route::get('/diagnosis', [DiagnosisController::class, 'get'])->name('diagnosis.get'); // SEARCH
    Route::post('/diagnosis/submit', [DiagnosisController::class, 'add'])->name('diagnosis.submit'); // add
    Route::post('/diagnosis/delete', [DiagnosisController::class, 'delete'])->name('diagnosis.delete'); // DELETE
    Route::get('/check-diagnosis-exist', [DiagnosisController::class, 'checkDiagnosisCodeExist']);


    // SERVICE TYPES
    Route::get('/service-type', [ServiceTypeController::class, 'get'])->name('servicetype.get'); // SEARCH
    Route::post('/service-type/submit', [ServiceTypeController::class, 'add'])->name('servicetype.submit'); // add
    Route::post('/service-type/delete', [ServiceTypeController::class, 'delete'])->name('servicetype.delete'); // DELETE
    Route::get('/check-service-type-exist', [ServiceTypeController::class, 'checkServiceTypeExist']);


    // SERVICE MODIFIER
    Route::get('/service-modifier', [ServiceModifierController::class, 'get'])->name('servicemodifier.get');  // SEARCH
    Route::post('/service-modifier/submit', [ServiceModifierController::class, 'add'])->name('servicemodifier.submit'); // add
    Route::post('/service-modifier/delete', [ServiceModifierController::class, 'delete'])->name('servicemodifier.delete'); // DELETE
    Route::get('/check-service-modifier-exist', [ServiceModifierController::class, 'checkServiceExist']);


    // COUSE OF LOSS
    Route::get('/couse-of-loss', [CouseOfLossController::class, 'get'])->name('couseofloss.get'); // SEARCH
    Route::post('/couse-of-loss/submit', [CouseOfLossController::class, 'add'])->name('couseofloss.submit'); // add
    Route::post('/couse-of-loss/delete', [CouseOfLossController::class, 'delete'])->name('couseofloss.delete'); // DELETE
    Route::get('/check-couse-of-loss-existed', [CouseOfLossController::class, 'checkCauseOfLossExisted']);
});

Route::group(['prefix' => 'exception'], function ($router) {

    // NDC
    Route::get('/ndc/search', [NDCExceptionController::class, 'search'])->name('ndsc.search'); // SEARCH
    Route::get('/ndc/get/{ndcid}', [NDCExceptionController::class, 'getNDCList'])->name('ndsc.list.get'); // LIST ITEMS
    Route::get('/ndc/details/{ndcid}', [NDCExceptionController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAILS





    // GPI 
    Route::get('/gpi/search', [GPIExceptionController::class, 'search'])->name('gpi.search'); // SEARCH
    Route::get('/gpi/get/{ndcid}', [GPIExceptionController::class, 'getNDCList'])->name('gpi.list.get'); // LIST ITEMS
    Route::get('/gpi/details/{ndcid}', [GPIExceptionController::class, 'getNDCItemDetails'])->name('gpi.details.get'); // DETAILS

    // THERAPY CLASS
    Route::get('/therapy-class/search', [TherapyClassController::class, 'search'])->name('therapyclass.search'); // SEARCH
    Route::get('/therapy-class/get/{ndcid}', [TherapyClassController::class, 'getTCList'])->name('therapyclass.list.get'); // LIST ITEMS
    Route::get('/therapy-class/details/{ndcid}', [TherapyClassController::class, 'getTCItemDetails'])->name('therapyclass.details.get'); // DETAILS

    // PROCEDURE EXCEPTION
    Route::get('/procedure/search', [ExceptionProcedureController::class, 'search'])->name('procedure.search'); // SEARCH
    Route::get('/procedure/get/{ndcid}', [ExceptionProcedureController::class, 'getPCList'])->name('procedure.list.get'); // LIST ITEMS
    Route::get('/procedure/details/{ndcid}', [ExceptionProcedureController::class, 'getPCItemDetails'])->name('procedure.details.get'); // DETAILS

    // BENEFIT LIST EXCEPTION
    Route::get('/benefit/search', [BenefitListController::class, 'search'])->name('benefit.search'); // SEARCH
    Route::get('/benefit/get/{ndcid}', [BenefitListController::class, 'getBLList'])->name('benefit.list.get'); // LIST ITEMS
    Route::get('/benefit/details/{ndcid}', [BenefitListController::class, 'getBLItemDetails'])->name('benefit.details.get'); // DETAILS

});

Route::group(['prefix' => 'validationlist'], function ($router) {

    // NDC
    Route::get('/diagnosis/search', [ValidationListsController::class, 'search'])->name('diagnosis.search'); // SEARCH
    Route::get('/diagnosis/get/{ndcid}', [ValidationListsController::class, 'getDiagnosisList'])->name('diagnosis.list.get'); // LIST ITEMS
    Route::get('/diagnosis/details/{ndcid}', [ValidationListsController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAIL


    Route::get('/speciality/search', [SpecialityController::class, 'search'])->name('speciality.search'); // SEARCH
    Route::get('/speciality/get/{ndcid}', [SpecialityController::class, 'getSpecialityList'])->name('diagnosis.list.get'); // LIST ITEMS
    Route::get('/speciality/details/{ndcid}', [SpecialityController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAIL



    Route::get('/eligibility/search', [EligibilityValidationListController::class, 'search'])->name('speciality.search'); // SEARCH
    // Route::get('/eligibility/get/{ndcid}', [EligibilityValidationListController::class, 'getSpecialityList'])->name('diagnosis.list.get'); // LIST ITEMS
    Route::get('/eligibility/details/{ndcid}', [EligibilityValidationListController::class, 'getNDCItemDetails'])->name('eligibility.details.get'); // DETAIL



    Route::get('/provider/search', [ProviderController::class, 'search'])->name('provider.search'); // SEARCH
    Route::get('/provider/get/{ndcid}', [ProviderController::class, 'getProviderList'])->name('provider.list.get'); // LIST ITEMS
    Route::get('/provider/details/{ndcid}', [ProviderController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAIL





    Route::get('/diagnosisvalidation/search', [DiagnosisValidationListController::class, 'search'])->name('diagnosisvalidation.search'); // SEARCH
    Route::get('/diagnosisvalidation/get/{ndcid}', [DiagnosisValidationListController::class, 'getProviderList'])->name('diagnosisvalidation.list.get'); // LIST ITEMS
    Route::get('/diagnosisvalidation/details/{ndcid}', [DiagnosisValidationListController::class, 'getNDCItemDetails'])->name('diagnosisvalidation.details.get'); // DETAIL



    Route::get('/pricingstrategy/search', [PricingStrategyController::class, 'search'])->name('pricingstrategy.search'); // SEARCH
    Route::get('/pricingstrategy/get/{ndcid}', [PricingStrategyController::class, 'getProviderList'])->name('pricingstrategy.list.get'); // LIST ITEMS
    Route::get('/pricingstrategy/details/{ndcid}', [PricingStrategyController::class, 'getNDCItemDetails'])->name('pricingstrategy.details.get'); // DETAIL
    Route::post('/pricingstrategy/add', [PricingStrategyController::class, 'add'])->name('pricingstrategy.add'); // SEARCH



    Route::get('/copay/search', [CopayStrategyController::class, 'search'])->name('copay.search'); // SEARCH
    Route::get('/copay/get/{ndcid}', [CopayStrategyController::class, 'getList'])->name('copay.list.get'); // LIST ITEMS
    Route::get('/copay/details/{ndcid}', [CopayStrategyController::class, 'getDetails'])->name('copay.details.get'); // DETAIL
    Route::post('/copay/add', [CopayStrategyController::class, 'add'])->name('copay.add'); // SEARCH



    Route::get('/accumulated/search', [AccumlatedController::class, 'search'])->name('accumulated.search'); // SEARCH
    Route::get('/accumulated/get/{ndcid}', [AccumlatedController::class, 'getList'])->name('accumulated.list.get'); // LIST ITEMS
    Route::get('/accumulated/details/{ndcid}', [AccumlatedController::class, 'getDetails'])->name('accumulated.details.get'); // DETAIL
    Route::post('/accumulated/add', [AccumlatedController::class, 'add'])->name('accumulated.add'); // SEARCH



    Route::get('/accumulated/benifit/search', [AccumlatedBenifitController::class, 'search'])->name('accumulated.benifit.search'); // SEARCH
    Route::get('/accumulated/benifit/get/{ndcid}', [AccumlatedBenifitController::class, 'getList'])->name('accumulated.benifit.list.get'); // LIST ITEMS
    // Route::get('/accumulated/benifit/details/{ndcid}', [AccumlatedBenifitController::class, 'getDetails'])->name('accumulated.benifit.details.get'); // DETAIL

    Route::get('/gpiExclusion/search', [GpiExclusionController::class, 'search'])->name('gpiExclusion.search'); // SEARCH


    Route::get('/gpiExclusion/get/{ndcid}', [GpiExclusionController::class, 'getList'])->name('accumulated.benifit.list.get'); // LIST ITEMS

    Route::get('/gpiExclusion/details/{ndcid}', [GpiExclusionController::class, 'getDetails'])->name('gpiExclusion.get'); // DETAIL



    Route::get('/ndcExclusion/search', [NdcExlusionController::class, 'search'])->name('ndcExclusion.search'); // SEARCH


    Route::get('/ndcExclusion/get/{ndcid}', [NdcExlusionController::class, 'getList'])->name('ndcExclusion.list.get'); // LIST ITEMS

    Route::get('/ndcExclusion/details/{ndcid}', [NdcExlusionController::class, 'getDetails'])->name('ndcExclusion.get'); // DETAIL



    Route::get('customer/search', [MajorMedicalController::class, 'search']);
    Route::get('client/get/{customerid}', [MajorMedicalController::class, 'getClient']);

    Route::get('clientgroup/get/{client}', [MajorMedicalController::class, 'getClientGroup']);

    Route::get('clientgroup/details/{client}', [MajorMedicalController::class, 'getDetails']);



    // getDetails


    Route::get('/prescriber/search', [PrescriberValidationController::class, 'search'])->name('prescriber.search'); // SEARCH
    Route::get('/prescriber/get/{ndcid}', [PrescriberValidationController::class, 'getProviderList'])->name('prescriber.list.get'); // LIST ITEMS
    Route::get('/prescriber/details/{ndcid}', [PrescriberValidationController::class, 'getNDCItemDetails'])->name('prescriber.details.get'); // DETAIL






});


Route::group(['prefix' => 'prescriberdata'], function ($router) {


    Route::get('/prescriber/search', [PrescriberController::class, 'search'])->name('prescriber.search'); // SEARCH

    Route::get('/prescriber/details/{ndcid}', [PrescriberController::class, 'getDetails'])->name('prescriber.get'); // DETAIL

    Route::post('/prescriber/update/{pres_id}', [PrescriberController::class, 'updatePrescriber'])->name('prescriber.update'); // UPDATE


});




Route::post('customer/add', [CustomerController::class, 'saveIdentification']);
Route::post('customer/id/generate', [CustomerController::class, 'generateCustomerId']);
Route::get('customer/get', [CustomerController::class, 'searchCutomer']);

Route::get('plan/get/{planid}', [CustomerController::class, 'searchPlanId']);


Route::get('superprovidernetwork/get/{id}', [CustomerController::class, 'searchSuperProviderNetworkId']);
Route::get('superprovidernetworkids', [CustomerController::class, 'ALLSuperProviderNetworkIdS']);


Route::get('customer/get/{customerid}', [CustomerController::class, 'GetCustomer']);


Route::get('client/get', [ClientController::class, 'searchClient']);
Route::get('client/get/{clientid}', [ClientController::class, 'GetOneClient']);


Route::get('clientgroup/get', [ClientGroupController::class, 'searchClientgroup']);
Route::get('clientgroup/get/{clientgrpid}', [ClientGroupController::class, 'GetOneClientGroup']);




// COMMOM
Route::get('/countries', [Controller::class, 'Contries'])->name('countries');
Route::get('/states', [Controller::class, 'getStatesOfCountry'])->name('states');
Route::get('/member', [Controller::class, 'getMember'])->name('member');
Route::get('/provider', [Controller::class, 'getProvider']);




Route::group(['prefix' => 'provider'], function ($router) {

    //SUPER PROVIDER NETWORK
    // Route::post('customer/add', [CustomerController::class, 'saveIdentification']);
    // Route::post('customer/id/generate', [CustomerController::class, 'generateCustomerId']);
    Route::get('supernetwork/search', [SuperProviderNetworkController::class, 'search']);

    Route::get('supernetwork/get/{ndcid}', [SuperProviderNetworkController::class, 'networkList']);


    //TRADITIONAL NETWORK 

    Route::get('traditionalnetwork/search', [TraditionalNetworkController::class, 'search']);
    Route::get('traditionalnetwork/get/{ndcid}', [TraditionalNetworkController::class, 'getList']);

    //Prioritize  Network

    Route::get('prioritize/search', [PrioritiseNetworkController::class, 'search']);

    Route::get('prioritize/get/{ndcid}', [PrioritiseNetworkController::class, 'networkList']);
});


//Provider Type Validation 
Route::get('/provider-type-validation', [ProviderTypeValidationController::class, 'test']);
Route::get('/provider-type-validation/get', [ProviderTypeValidationController::class, 'get'])->name('provider-type-validation-get');
Route::get('/provider-type-validation/getFormData', [ProviderTypeValidationController::class, 'getFormData'])->name('provider-type-validation-getFormData');

//Procedure Code List
Route::get('/procedure-code-list/get', [PrcedureCodeListController::class, 'get'])->name('procedure-code-list-get');
Route::get('/procedure-code-list/get-code-list', [PrcedureCodeListController::class, 'getProcCodeList'])->name('procedure-code-list-get');

//Super Benefit List
Route::get('/super-benefit-list/get', [SuperBenefitControler::class, 'get']);
Route::get('/super-benefit-list/get-super-benefit-code', [SuperBenefitControler::class, 'getBenefitCode']);

//Third Party Pricing(module)
Route::group(['prefix' => 'third-party-pricing/'], function () {
    //Price Schedule
    Route::get('price-schedule/get', [PriceScheduleController::class, 'get']);
    Route::get('price-schedule/get-price-schedule-data', [PriceScheduleController::class, 'getPriceScheduleDetails']);
    // Route::post('price-schedule/update', [PriceScheduleController::class, 'updateBrandItem'])->name('price_schedule_update');
    Route::get('price-schedule/get-brand-type', [PriceScheduleController::class, 'getBrandType']);
    Route::get('price-schedule/get-brand-source', [PriceScheduleController::class, 'getBrandSource']);
    Route::post('price-schedule/submit', [PriceScheduleController::class, 'submitPriceSchedule']);

    //Copay Schedule
    Route::get('copay-schedule/get', [CopayScheduleController::class, 'get'])->name('get.copay');
    Route::get('copay-schedule/get-copay-data', [CopayScheduleController::class, 'getCopayData'])->name('get.copay.single');

    //Copay Step Schedule
    Route::get('copay-step-schedule/get', [CopayStepScheduleController::class, 'get'])->name('get.copay-step');
    Route::get('copay-step-schedule/check-copay-list-existing', [CopayStepScheduleController::class, 'checkCopayListExist']);
    Route::post('copay-step-schedule/submit', [CopayStepScheduleController::class, 'submit'])->name('submit.copay-step');

    //MAC List
    Route::get('mac-list/get', [MacListController::class, 'get'])->name('get.macList');
    Route::get('mac-list/get-mac-list', [MacListController::class, 'getMacList'])->name('get.mac-list.single');
    Route::get('mac-list/get-price-source', [MacListController::class, 'getPriceSource']);
    Route::get('mac-list/get-price-type', [MacListController::class, 'getPriceType']);
    Route::post('mac-list/submit', [MacListController::class, 'submit']);

    //Tax Schedule
    Route::get('tax-schedule/get', [TaxScheduleController::class, 'get']);

    //Procedure UCR list
    Route::get('procedure-ucr-list/get', [ProcedureUcrList::class, 'get']);
    Route::get('procedure-ucr-list/get-procedure-list-data', [ProcedureUcrList::class, 'getProcedureListData']);

    //RVA List
    Route::get('rva-list/get', [RvaListController::class, 'get']);
    Route::get('rva-list/get-rva-list', [RvaListController::class, 'getRvaList']);
});

//Drug Information
Route::group(['prefix' => "drug-information/"], function () {
    Route::get('drug-database/get', [DrugDatabaseController::class, 'get']);
    Route::get('drug-database/get-drug-prices', [DrugDatabaseController::class, 'getDrugPrices']);
});


//Plan Design
Route::group(['prefix' => 'plan-design/'], function () {
    //Plan Association
    Route::get('plan-association/get', [PlanAssociationController::class, 'get']);
    Route::post('plan-association/submit-form', [PlanAssociationController::class, 'submitPlanAssociation']);
    Route::get('plan-association/get-pharmacy-chain', [PlanAssociationController::class, 'getPharmacyChain']);
    Route::get('plan-association/get-form-id', [PlanAssociationController::class, 'getFormId']);
    Route::get('plan-association/get-membership-process-flag', [PlanAssociationController::class, 'getMemProcFlag']);
    Route::get('plan-association/get-customer', [PlanAssociationController::class, 'getCustomer']);
    Route::get('plan-association/get-client', [PlanAssociationController::class, 'getClient']);
    Route::get('plan-association/get-client-group', [PlanAssociationController::class, 'getClientGroup']);
    Route::get('plan-association/get-transaction-type', [PlanAssociationController::class, 'getTransactionType']);
    Route::get('plan-association/get-transaction-association', [PlanAssociationController::class, 'getTransactionAssociation']);
    Route::get('plan-association/get-client-group-label', [PlanAssociationController::class, 'getClientGroupLabel']);
    Route::get('plan-association/get-plan-id', [PlanAssociationController::class, 'getPlanId']);

    //Plan Edit
    Route::get('plan-edit/get', [PlanEditController::class, 'get']);
    Route::get('plan-edit/get-plan-edit-data', [PlanEditController::class, 'getPlanEditData']);
    Route::get('plan-edit/get-plan-classification', [PlanEditController::class, 'getPlanClassification']);
    Route::get('plan-edit/get-exp-flag', [PlanEditController::class, 'getExpFlag']);
    Route::get('plan-edit/get-pharm-exp-flag', [PlanEditController::class, 'getPharmExpFlag']);
    Route::get('plan-edit/get-prisc-exp-flag', [PlanEditController::class, 'getPriscExpFlag']);
    Route::get('plan-edit/get-exhausted', [PlanEditController::class, 'getExhausted']);
    Route::get('plan-edit/get-tax', [PlanEditController::class, 'getTax']);
    Route::get('plan-edit/get-uc-plan', [PlanEditController::class, 'getUCPlan']);
    Route::get('plan-edit/get-search-indication', [PlanEditController::class, 'getSearchIndication']);
    Route::get('plan-edit/get-formulary', [PlanEditController::class, 'getFormulary']);
});

//Membership
Route::group(['prefix' => 'membership/'], function () {
    //Member
    Route::get('memberdata/get', [MemberController::class, 'get']);
    Route::get('memberdata/get-member-coverage-history-data', [MemberController::class, 'getCoverageHistory']);
    Route::get('memberdata/get-health-condition', [MemberController::class, 'getHealthCondition']);
    Route::get('memberdata/get-diagnosis-history', [MemberController::class, 'getDiagnosisHistory']);
    Route::get('memberdata/get-prior-authorization', [MemberController::class, 'getPriorAuthorization']);
    Route::get('memberdata/get-log-change-data', [MemberController::class, 'getLogChangeData']);
    Route::get('memberdata/get-eligibility', [MemberController::class, 'getEligibility'])->name('member.eligibility');
    Route::get('memberdata/member-status', [MemberController::class, 'getMemberStatus'])->name('member.status');
    Route::get('memberdata/member-relationship', [MemberController::class, 'getMemberRelationship'])->name('member.relationship');
    Route::get('memberdata/copay-schedule-overrides', [MemberController::class, 'getCopayScheduleOverride'])->name('member.copayScheduleOverrides');
    Route::get('memberdata/accumulated-benifit-overrides', [MemberController::class, 'getAccumulatedBenifitOverride'])->name('member.accumulatedBenifitOverride');
    Route::get('memberdata/copay-strategy-id', [MemberController::class, 'getCopayStrategyId']);
    Route::get('memberdata/accumulated-benifit-strategy', [MemberController::class, 'getAccumulatedBenifitStrategy']);
    Route::get('memberdata/pricing-strategy', [MemberController::class, 'getPricingStrategy']);
    Route::get('memberdata/view-limitations', [MemberController::class, 'getViewLimitations']);
    Route::get('memberdata/form-submit', [MemberController::class, 'submitMemberForm']);
    Route::post('memberdata/submit', [MemberController::class, 'submitMemberForm']);

    //tab table routes
    Route::get('memberdata/get-coverage-information-table', [MemberController::class, 'getCoverageInformationTable']);
    Route::get('memberdata/get-health-conditions-diagnosis-table', [MemberController::class, 'getDiagnosisTable']);
    Route::get('memberdata/get-health-conditions-diagnosis-details-table', [MemberController::class, 'getDiagnosisDetailsTable']);
    Route::get('memberdata/get-claim-history-table', [MemberController::class, 'getClaimHistoryTable']);
    Route::get('memberdata/get-prior-auth-table', [MemberController::class, 'getPriorAuthTable']);
    Route::get('memberdata/get-provider-search-table', [MemberController::class, 'getProviderSearch']);
    Route::get('memberdata/get-change-log-table', [MemberController::class, 'getChangeLogTable']);


    //Prior Authorization
    Route::get('prior-authorization/get', [PriorAuthController::class, 'get']);

    //Plan Validation
    Route::get('plan-validation/get', [PlanValidationController::class, 'get']);
    Route::get('plan-validation/get-client-details', [PlanValidationController::class, 'getClientDetails']);
    Route::get('plan-validation/get-plan-id', [PlanValidationController::class, 'getPlanId']);
    Route::post('plan-validation/add-plan-validaion', [PlanValidationController::class, 'addPlanValidation']);
});

//Administrator
Route::group(['prefix' => 'administrator/'], function () {
    //User Defination
    Route::get('user-defination/get', [UserDefinationController::class, 'get']);
    Route::get('user-defination/get-group-data', [UserDefinationController::class, 'getGroupData']);
    Route::get('user-defination/get-security-options', [UserDefinationController::class, 'getSecurityOptions']);
    Route::get('user-defination/validate-group', [UserDefinationController::class, 'validateGroup']);
    Route::post('user-defination/submit', [UserDefinationController::class, 'submitFormData']);
    Route::get('user-defination/get-customers', [UserDefinationController::class, 'getCustomers']);
    Route::get('user-defination/get-customers-list', [UserDefinationController::class, 'getCustomersList']);
    Route::get('user-defination/get-clients', [UserDefinationController::class, 'getClients']);
    Route::get('user-defination/get-client-groups', [UserDefinationController::class, 'getClientGroups']);

    //Search Audit Trail
    Route::get('search-audit-trial/get-tables', [AuditTrailController::class, 'getTables'])->name('getAllTables');
    Route::get('search-audit-trial/get-user_ids', [AuditTrailController::class, 'getUserIds'])->name('getUserIds');
    Route::get('search-audit-trial/get-record-actions', [AuditTrailController::class, 'getRecordAction'])->name('getRecordAction');
    Route::post('search-audit-trial/search-user-log', [AuditTrailController::class, 'searchUserLog'])->name('searchUserLog');

    //System parameters
    Route::get('system-parameter/get-parameters', [SystemParameterController::class, 'getSystemParameters'])->name('getSystemParameters');
    Route::get('system-parameters/get-states', [SystemParameterController::class, 'getState'])->name('getState');
    Route::get('system-parameters/get-countries', [SystemParameterController::class, 'getCountries'])->name('getCountries');

    //Claim History
    Route::post('claim-history/search', [ClaimHistoryController::class, 'searchHistory']);
    Route::get('claim-history/get-ndcdrops', [ClaimHistoryController::class, 'getNDCDropdown']);
    Route::get('claim-history/get-gpidrops', [ClaimHistoryController::class, 'getGPIDropdown']);
    Route::get('claim-history/get-proceduer-code', [ClaimHistoryController::class, 'getProcedureCode']);
    Route::get('claim-history/get-customer-id', [ClaimHistoryController::class, 'getCustomerId']);
    Route::get('claim-history/get-client-id', [ClaimHistoryController::class, 'getClientId']);
    Route::get('claim-history/get-client-group', [ClaimHistoryController::class, 'getClientGroup']);
    Route::post('claim-history/search-optional-data', [ClaimHistoryController::class, 'searchOptionalData']);
});
