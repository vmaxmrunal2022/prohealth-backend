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
use App\Http\Controllers\Exception\ReasonCodeExceptionController;
use App\Http\Controllers\Exception\ProviderTypeProcController;
use App\Http\Controllers\Exception\ProcedureCrossReferenceController;



use App\Http\Controllers\user_access\UserAccessControl;





use App\Http\Controllers\Exception\BenefitDerivationController;


use App\Http\Controllers\Exception\GPIExceptionController;
use App\Http\Controllers\Exception\NDCExceptionController;
use App\Http\Controllers\Exception\LimitationsController;



// use App\Http\Controllers\membership\PlanValidationController;
// use App\Http\Controllers\membership\PriorAuthController;
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
use App\Http\Controllers\administrator\ClaimHistoryController;
use App\Http\Controllers\administrator\ZipCodeController;
use App\Http\Controllers\administrator\AuditTrailController;
use App\Http\Controllers\administrator\SystemParameterController;
use App\Http\Controllers\administrator\UserDefinationController;
use App\Http\Controllers\administrator\VerifyDrugVCoverage;
use App\Http\Controllers\Provider\SuperProviderNetworkController;
use App\Http\Controllers\Provider\TraditionalNetworkController;
use App\Http\Controllers\Provider\FlexibleNetworkController;
use App\Http\Controllers\Provider\ChainController;
use App\Http\Controllers\Provider\PrioritiseNetworkController;
use App\Http\Controllers\Provider\ProviderDataProviderController;
use App\Http\Controllers\PrescriberData\PrescriberController;
use App\Http\Controllers\Exception\ProcedureController as ExceptionProcedureController;
use App\Http\Controllers\Exception\TherapyClassController;
use App\Http\Controllers\drug_information\DrugDatabaseController;
use App\Http\Controllers\drug_information\NdcGpiController;

use App\Http\Controllers\Exception\PrcedureCodeListController;
use App\Http\Controllers\Exception\ProviderTypeValidationController;
use App\Http\Controllers\plan_design\PlanAssociationController;
use App\Http\Controllers\Exception\SuperBenefitControler;
use App\Http\Controllers\Exception\DrugClassController;
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
Route::group(['middleware' => 'apisession'], function ($router) {

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

        Route::get('/benefits/all_codes', [BenifitController::class, 'get_all'])->name('benefit.all.get'); // SEARCH


        // REASON CODES
        Route::get('/reasons', [ReasonsController::class, 'get'])->name('reasons.get'); // SEARCH
        Route::post('/reasons/submit', [ReasonsController::class, 'add'])->name('reasons.submit'); // add
        Route::post('/reasons/delete', [ReasonsController::class, 'delete'])->name('reasons.delete'); // DELETE
        Route::get('/check-reason-exist', [ReasonsController::class, 'checkReasonExist']);
        Route::get('/reasons/all', [ReasonsController::class, 'all'])->name('reasons.get'); // SEARCH


        // PROCEDURE
        Route::get('/procedure', [ProcedureController::class, 'get'])->name('procedure.get'); // SEARCH
        Route::post('/procedure/submit', [ProcedureController::class, 'add'])->name('procedure.submit');  // add
        Route::post('/procedure/delete', [ProcedureController::class, 'delete'])->name('procedure.delete'); // DELETE
        Route::get('/check-procedure-exist', [ProcedureController::class, 'checkProcedureCodeExist']);
        Route::get('/procedure/codes', [ProcedureController::class, 'getCodes'])->name('procedure.get'); // SEARCH


        // PROVIDER TYPE
        Route::get('/provider-type', [ProviderTypeController::class, 'get'])->name('providertype.get'); // SEARCH
        Route::post('/provider-type/submit', [ProviderTypeController::class, 'add'])->name('providertype.submit');  // add
        Route::post('/provider-type/delete', [ProviderTypeController::class, 'delete'])->name('providertype.delete'); // DELETE
        Route::get('/provider/id/search', [ProviderTypeController::class, 'IdSearch'])->name('providertype.search'); // SEARCH

        Route::get('/check-provider-type-exist', [ProviderTypeController::class, 'checkProviderTypeExist']);

        // DIAGNOSIS
        Route::get('/diagnosis', [DiagnosisController::class, 'get'])->name('diagnosis.get'); // SEARCH
        Route::post('/diagnosis/submit', [DiagnosisController::class, 'add'])->name('diagnosis.submit'); // add
        Route::post('/diagnosis/delete', [DiagnosisController::class, 'delete'])->name('diagnosis.delete'); // DELETE
        // Route::get('/diagnosis/all', [DiagnosisController::class, 'get'])->name('diagnosis.get'); // SEARCH
        Route::get('/check-diagnosis-exist', [DiagnosisController::class, 'checkDiagnosisCodeExist']);
        Route::get('/diagnosis/all', [DiagnosisController::class, 'all'])->name('diagnosis.get'); // SEARCH


        // SERVICE TYPES
        Route::get('/service-type', [ServiceTypeController::class, 'get'])->name('servicetype.get'); // SEARCH
        Route::post('/service-type/submit', [ServiceTypeController::class, 'add'])->name('servicetype.submit'); // add
        Route::post('/service-type/delete', [ServiceTypeController::class, 'delete'])->name('servicetype.delete'); // DELETE
        Route::get('/check-service-type-exist', [ServiceTypeController::class, 'checkServiceTypeExist']);
        Route::get('/service-type/all', [ServiceTypeController::class, 'getallServicetypes'])->name('servicetype.all.servicetypes'); // SEARCH




        // SERVICE MODIFIER
        Route::get('/service-modifier', [ServiceModifierController::class, 'get'])->name('servicemodifier.get');  // SEARCH
        Route::post('/service-modifier/submit', [ServiceModifierController::class, 'add'])->name('servicemodifier.submit'); // add
        Route::post('/service-modifier/delete', [ServiceModifierController::class, 'delete'])->name('servicemodifier.delete'); // DELETE
        Route::get('/check-service-modifier-exist', [ServiceModifierController::class, 'checkServiceExist']);
        Route::get('/service-modifier-all', [ServiceModifierController::class, 'get_all'])->name('servicemodifier.get.all');  // SEARCH


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
        Route::get('/ndc/details/{ndcid}/{ndcid2}', [NDCExceptionController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAILS
        Route::post('/ndc/add', [NDCExceptionController::class, 'add'])->name('ndsc.search'); // add

        Route::get('/ndc/list', [NDCExceptionController::class, 'ndcList'])->name('ndsc.search'); // SEARCH


        //REASON-CODE-EXCEPTION
        Route::get('/ndc/ndc-drop-down', [NDCExceptionController::class, 'getNdcDropDown']); // drop down

        Route::get('/reason/exception/search', [ReasonCodeExceptionController::class, 'search'])->name('reason.exception.search'); // SEARCH
        Route::get('/reason/exception/details/{ndcid}', [ReasonCodeExceptionController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAILS
        Route::post('/reason/exception/add', [ReasonCodeExceptionController::class, 'add'])->name('reason.exception.add'); // DETAILS


        // NDC
        Route::get('/diagnosis/search', [ValidationListsController::class, 'search'])->name('diagnosis.search'); // SEARCH
        Route::get('/diagnosis/get/{ndcid}', [ValidationListsController::class, 'getDiagnosisList'])->name('diagnosis.list.get'); // LIST ITEMS
        Route::get('/diagnosis/details/{ndcid}', [ValidationListsController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAIL

        Route::get('/limitations', [DiagnosisController::class, 'getLimitations'])->name('diagnosis.get'); // SEARCH
        Route::get('/limitations/search', [LimitationsController::class, 'search'])->name('limitations.search'); // SEARCH
        Route::get('/limitations/details/{id}', [LimitationsController::class, 'getDetails'])->name('limitations.search'); // SEARCH
        Route::post('/limitations/add', [LimitationsController::class, 'add'])->name('limitations.add'); // SEARCH

        Route::get('/procedurecodes', [ProcedureCrossReferenceController::class, 'ProcedureCodes'])->name('diagnosis.get'); // SEARCH


        Route::get('/providertype-proc/search', [ProviderTypeProcController::class, 'search'])->name('provtype.search'); // SEARCH
        Route::get('/providertype-proc/details/{id}', [ProviderTypeProcController::class, 'getDetails'])->name('provtype.get'); // SEARCH
        Route::post('/providertype-proc/add', [ProviderTypeProcController::class, 'add'])->name('provtype.add'); // SEARCH
        Route::get('/providertype-proc/getlist/{id}', [ProviderTypeProcController::class, 'getList'])->name('provtype.get'); // SEARCH
        Route::get('/get-all-ndc', [NDCExceptionController::class, 'getAllNDCS'])->name('getall.ndcs'); // SEARCH




        Route::get('/speciality/search', [SpecialityController::class, 'search'])->name('speciality.search'); // SEARCH
        Route::get('/speciality/get/{specialty_id}', [SpecialityController::class, 'getSpecialityList'])->name('diagnosis.list.get'); // LIST ITEMS
        Route::get('/speciality/details/{specialty_id}/{specialty_list}', [SpecialityController::class, 'getSpecialityDetails'])->name('ndsc.details.get'); // DETAIL
        Route::post('/speciality/submit-speciality-form', [SpecialityController::class, 'addSpeciality']); // add update speciality

        //exception provider list
        Route::get('/provider/getAll', [ProviderDataProviderController::class, 'getAll'])->name('exception.provider.getAll');

        //exception Price Schedule list

        //exception speciality list
        Route::get('/speciality/getAll', [SpecialityController::class, 'getAll'])->name('exception.speciality.getAll');

        //exception copay Schedule list
        Route::get('copay-schedule/getAll', [CopayScheduleController::class, 'getAll'])->name('exception.getAll.copay');

        //exception prescriber list
        Route::get('/prescriber/getAll', [PrescriberController::class, 'getAll'])->name('exception.prescriber.getAll');

        //exception diagnosis list
        Route::get('/diagnosis/all', [DiagnosisController::class, 'all'])->name('exception.diagnosis.get');

        //exception copay Schedule list
        Route::get('copay-schedule/getAll', [CopayScheduleController::class, 'getAll'])->name('exception.getAll.copay');


        Route::get('/eligibility/search', [EligibilityValidationListController::class, 'search']); // SEARCH
        // Route::get('/eligibility/get/{ndcid}', [EligibilityValidationListController::class, 'getSpecialityList'])->name('diagnosis.list.get'); // LIST ITEMS
        // Route::get('/eligibility/details/{elig_lis_id}', [EligibilityValidationListController::class, 'getEligibilityDetails']); // DETAIL
        // Route::post('/eligibility/submit-eligiblity-form', [EligibilityValidationListController::class, 'addEligiblityData']);
        Route::get('/eligibility/dropdown', [EligibilityValidationListController::class, 'DropDown']);





        Route::get('/provider/search', [ProviderController::class, 'search'])->name('provider.search'); // SEARCH
        Route::get('/provider/get/{provider_list}', [ProviderController::class, 'getProviderValidationList'])->name('provider.list.get'); // LIST ITEMS
        Route::get('/provider/details/{provider_list}/{provider_nabp}', [ProviderController::class, 'getProviderDetails'])->name('ndsc.details.get'); // DETAIL
        Route::post('/provider/submit-provider-form', [ProviderController::class, 'addProviderData']);
        Route::get('/provider/provider-list-drop-down/', [ProviderController::class, 'searchDropDownProviderList']);


        //DIAGNOSIS VALIDATION LIST
        Route::get('/diagnosisvalidation/search', [DiagnosisValidationListController::class, 'search'])->name('diagnosisvalidation.search'); // SEARCH
        Route::get('/diagnosisvalidation/get/{diagnosis_list}', [DiagnosisValidationListController::class, 'getPriorityDiagnosis'])->name('diagnosisvalidation.list.get'); // LIST ITEMS
        Route::get('diagnosisvalidation/diagnosis-code-list/{disgnosis_code?}', [DiagnosisValidationListController::class, 'getDiagnosisCodeList']); //diagnosis code drop down with search
        Route::get('diagnosisvalidation/limitation-code-list/{limitation_code?}', [DiagnosisValidationListController::class, 'getLimitationsCode']); //limitationid drop down
        Route::get('/diagnosisvalidation/diagnosis_limitations/{diagnosis_list}/{diagnosis_id}', [DiagnosisValidationListController::class, 'getDiagnosisLimitations'])->name('diagnosisvalidation.details.get'); // DETAIL
        Route::post('/diagnosisvalidation/submit-diagnosis-form', [DiagnosisValidationListController::class, 'addDiagnosisValidations']); //add and update diagnosis data
        Route::post('/diagnosisvalidation/submit-diagnosis-limitation-form', [DiagnosisValidationListController::class, 'DiagnosisLimitationAdd']);

        Route::get('/diagnosisvalidation/validation-list/{diagnosis_list}', [DiagnosisValidationListController::class, 'getDiagnosisValidations']);

        Route::get('/diagnosisvalidation/details/{diagnosis_list}/{diagnosis_id}', [DiagnosisValidationListController::class, 'getDiagnosisDetails']);

        Route::post('/diagnosisvalidation/submit-diagnosis-validation-form', [DiagnosisValidationListController::class, 'updatePriorityDiagnosisValidation']);


        Route::get('/pricingstrategy/search', [PricingStrategyController::class, 'search'])->name('pricingstrategy.search'); // SEARCH
        Route::get('/pricingstrategy/get/{ndcid}', [PricingStrategyController::class, 'getProviderList'])->name('pricingstrategy.list.get'); // LIST ITEMS
        Route::get('/pricingstrategy/details/{ndcid}', [PricingStrategyController::class, 'getNDCItemDetails'])->name('pricingstrategy.details.get'); // DETAIL
        Route::post('/pricingstrategy/add', [PricingStrategyController::class, 'add'])->name('pricingstrategy.add'); // SEARCH
        Route::get('/pricingstrategy/all', [PricingStrategyController::class, 'get_all'])->name('pricingstrategy.get.all'); // SEARCH



        Route::get('/copay/search', [CopayStrategyController::class, 'search'])->name('copay.search'); // SEARCH
        Route::get('/copay/get/{ndcid}', [CopayStrategyController::class, 'getList'])->name('copay.list.get'); // LIST ITEMS
        Route::get('/copay/details/{ndcid}', [CopayStrategyController::class, 'getDetails'])->name('copay.details.get'); // DETAIL
        Route::post('/copay/add', [CopayStrategyController::class, 'add'])->name('copay.add'); // SEARCH
        Route::get('/copay/drop-down', [CopayStrategyController::class, 'CopayDropDown'])->name('copay.dropdown'); // SEARCH



        Route::get('/accumulated/search', [AccumlatedController::class, 'search'])->name('accumulated.search'); // SEARCH
        Route::get('/accumulated/get/{ndcid}', [AccumlatedController::class, 'getList'])->name('accumulated.list.get'); // LIST ITEMS
        Route::get('/accumulated/details/{ndcid}', [AccumlatedController::class, 'getDetails'])->name('accumulated.details.get'); // DETAIL
        Route::post('/accumulated/add', [AccumlatedController::class, 'add'])->name('accumulated.add'); // SEARCH
        Route::get('/accumulated/drop-down', [AccumlatedController::class, 'AccumlatedDropDown'])->name('accumulated.dropdown'); // SEARCH



        Route::get('/accumulated/benifit/search', [AccumlatedBenifitController::class, 'search'])->name('accumulated.benifit.search'); // SEARCH
        Route::get('/accumulated/benifit/get/{ndcid}', [AccumlatedBenifitController::class, 'getList'])->name('accumulated.benifit.list.get'); // LIST ITEMS
        Route::get('/accumulated/benifit/details/{ndcid}', [AccumlatedBenifitController::class, 'getDetails'])->name('accumulated.benifit.details.get'); // DETAIL
        Route::post('/accumulated/benifit/add', [AccumlatedBenifitController::class, 'add'])->name('accumulated.benifit.add'); // SEARCH
        Route::get('/accumulated/benifit/all', [AccumlatedBenifitController::class, 'get_all'])->name('accumulated.benifit.search'); // SEARCH

        Route::get('/accumulated/drop-down', [AccumlatedController::class, 'getAllAcuumlatedBenefits'])->name('accumulated.all'); // SEARCH

        Route::get('/gpiExclusion/search', [GpiExclusionController::class, 'search'])->name('gpiExclusion.search'); // SEARCH


        Route::get('/gpiExclusion/get/{ndcid}', [GpiExclusionController::class, 'getList'])->name('accumulated.benifit.list.get'); // LIST ITEMS

        Route::get('/gpiExclusion/details/{ndcid}', [GpiExclusionController::class, 'getDetails'])->name('gpiExclusion.get'); // DETAIL
        Route::post('/gpiExclusion/add', [GpiExclusionController::class, 'add'])->name('gpiExclusion.add'); // ADD




        Route::get('/ndcExclusion/search', [NdcExlusionController::class, 'search'])->name('ndcExclusion.search'); // SEARCH


        Route::get('/ndcExclusion/get/{ndcid}', [NdcExlusionController::class, 'getList'])->name('ndcExclusion.list.get'); // LIST ITEMS

        Route::get('/ndcExclusion/details/{ndcid}', [NdcExlusionController::class, 'getDetails'])->name('ndcExclusion.get'); // DETAIL
        Route::post('/ndcExclusion/add', [NdcExlusionController::class, 'add'])->name('ndcExclusion.add'); // SEARCH



        Route::get('customer/search', [MajorMedicalController::class, 'search']);
        Route::get('client/get/{customerid}', [MajorMedicalController::class, 'getClient']);

        Route::get('clientgroup/get/{client}', [MajorMedicalController::class, 'getClientGroup']);

        Route::get('clientgroup/details/{client}', [MajorMedicalController::class, 'getDetails']);
        Route::post('major/medical/add', [MajorMedicalController::class, 'add']);

        Route::post('clientgroup/add', [ClientGroupController::class, 'add']);
        // getDetails


        Route::get('/prescriber/search', [PrescriberValidationController::class, 'search'])->name('prescriber.search'); // SEARCH
        Route::get('/prescriber/get/{physicain_list}', [PrescriberValidationController::class, 'getProviderValidationList'])->name('prescriber.list.get'); // LIST ITEMS
        Route::get('/prescriber/details/{physicain_list}/{physicain_id}', [PrescriberValidationController::class, 'getProviderDetails'])->name('prescriber.details.get'); // DETAIL
        Route::get('prescriber/prescriber-list-drop-down', [PrescriberValidationController::class, 'searchDropDownPrescriberList']);
        Route::post('/prescriber/submit-prescriber-form', [PrescriberValidationController::class, 'addPrescriberData']);

        //exception Price Schedule list
        Route::get('price-schedule/getAll', [PriceScheduleController::class, 'getAll']);

        //exception copay Schedule list
        Route::get('copay-schedule/getAll', [CopayScheduleController::class, 'getAll'])->name('exception.getAll.copay');

        //exception prescriber list
        Route::get('/prescriber/getAll', [PrescriberController::class, 'getAll'])->name('exception.prescriber.getAll');

        //exception speciality list
        Route::get('/speciality/getAll', [SpecialityController::class, 'getAll'])->name('exception.speciality.getAll');

        //exception provider list
        Route::get('/provider/getAll', [ProviderDataProviderController::class, 'getAll'])->name('exception.provider.getAll');

        //exception diagnosis list

        Route::get('/diagnosis/all', [DiagnosisController::class, 'all'])->name('exception.diagnosis.get');
        Route::get('/diagnosis/all', [DiagnosisController::class, 'all'])->name('exception.diagnosis.get');

        Route::get('Procedure-cross-reference/search', [ProcedureCrossReferenceController::class, 'search'])->name('cross-reference.search');

        Route::get('Procedure-cross-reference/list/{id}', [ProcedureCrossReferenceController::class, 'List'])->name('cross-reference.list');
        Route::get('procedure-cross-reference/details/{id}/{id2}/{id3}/{id4}', [ProcedureCrossReferenceController::class, 'getDetails'])->name('cross-reference.details');


        Route::post('procedure-cross-reference/add', [ProcedureCrossReferenceController::class, 'add'])->name('cross-reference.add');
    });

    Route::group(['prefix' => 'prescriberdata'], function ($router) {


        Route::get('/prescriber/search', [PrescriberController::class, 'search'])->name('prescriber.search'); // SEARCH

        Route::get('/prescriber/details/{ndcid}', [PrescriberController::class, 'getDetails'])->name('prescriber.get'); // DETAIL

        Route::post('/prescriber/update/{pres_id}', [PrescriberController::class, 'updatePrescriber'])->name('prescriber.update'); // UPDATE
        Route::post('/prescriber/add', [PrescriberController::class, 'add'])->name('prescriber.add'); // UPDATE


    });

    Route::post('customer/add', [CustomerController::class, 'add']);
    Route::post('customer/id/generate', [CustomerController::class, 'generateCustomerId']);
    Route::get('customer/get', [CustomerController::class, 'searchCutomer']);

    Route::get('plan/get/{planid}', [CustomerController::class, 'getPlanId']);


    Route::get('planid/search', [CustomerController::class, 'searchPlanId']);



    Route::get('superprovidernetwork/get/{id}', [CustomerController::class, 'searchSuperProviderNetworkId']);
    Route::get('superprovidernetworkids', [CustomerController::class, 'ALLSuperProviderNetworkIdS']);


    Route::get('customer/get/{customerid}', [CustomerController::class, 'GetCustomer']);


    Route::get('client/get', [ClientController::class, 'searchClient']);
    Route::get('client/get/{clientid}', [ClientController::class, 'GetOneClient']);
    Route::post('client/add', [ClientController::class, 'add']);


    Route::get('clientgroup/get', [ClientGroupController::class, 'searchClientgroup']);
    Route::get('clientgroup/get/{clientgrpid}', [ClientGroupController::class, 'GetOneClientGroup']);




    // COMMOM
    Route::get('/countries', [Controller::class, 'Contries'])->name('countries');
    Route::get('/countries/search/{c_id?}', [Controller::class, 'ContriesSearch'])->name('countries.search');
    Route::get('/states/{countryid}', [Controller::class, 'getStatesOfCountry'])->name('states');
    //Route::get('/state/search/{stateid?}', [Controller::class, 'getStatesOfCountrySearch'])->name('state.search');
    Route::get('/state/search', [Controller::class, 'getStatesOfCountrySearch'])->name('state.search');
    Route::get('/states', [Controller::class, 'getStatesOfCountry'])->name('states');
    Route::get('/member', [Controller::class, 'getMember'])->name('member');
    Route::get('/provider', [Controller::class, 'getProvider']);
    Route::get('/get-customer-id', [ClaimHistoryController::class, 'getCustomerId']);
    Route::get('/get-client', [PlanAssociationController::class, 'getClient']);
    Route::get('/get-client-group', [PlanAssociationController::class, 'getClientGroup']);
    Route::get('/get-member-id', [MemberController::class, 'get']);
    Route::get('/ndc-drop-down', [NDCExceptionController::class, 'getNdcDropDown']);
    Route::get('/get-plan-id', [PlanAssociationController::class, 'getPlanId']);


    Route::group(['prefix' => 'providerdata'], function ($router) {


        Route::get('/provider/search', [ProviderDataProviderController::class, 'search'])->name('provider.search'); // SEARCH
        Route::get('/provider/get/{ndcid}', [ProviderDataProviderController::class, 'getProviderList'])->name('provider.list.get'); // LIST ITEMS
        Route::get('/provider/details/{ndcid}', [ProviderDataProviderController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAIL
        Route::post('/provider/add', [ProviderDataProviderController::class, 'add'])->name('ndsc.details.get'); // DETAIL
        Route::get('/get-provider-networks', [ProviderDataProviderController::class, 'getProviderNetworks']);

        //SUPER PROVIDER NETWORK
        // Route::post('customer/add', [CustomerController::class, 'saveIdentification']);
        // Route::post('customer/id/generate', [CustomerController::class, 'generateCustomerId']);
        Route::get('supernetwork/search', [SuperProviderNetworkController::class, 'search']);

        Route::get('supernetwork/get/{ndcid}', [SuperProviderNetworkController::class, 'networkList']);
        Route::post('superprovider/add', [SuperProviderNetworkController::class, 'add']);
        Route::get('supernetwork/dropdown', [SuperProviderNetworkController::class, 'dropDown']);

        Route::get('supernetwork/getDetails/{ndcid}', [SuperProviderNetworkController::class, 'getDetails']);


        //TRADITIONAL NETWORK

        Route::get('traditionalnetwork/search', [TraditionalNetworkController::class, 'search']);
        Route::get('traditionalnetwork/get/{ndcid}', [TraditionalNetworkController::class, 'getList']);

        Route::get('traditionalnetwork/details/{ndcid}', [TraditionalNetworkController::class, 'getDetails']);

        Route::post('traditionalnetwork/add', [TraditionalNetworkController::class, 'add']);


        //Flexible Network

        Route::get('flexiblenetwork/search', [FlexibleNetworkController::class, 'search']);
        Route::get('flexiblenetwork/get/{ndcid}', [FlexibleNetworkController::class, 'getList']);

        Route::get('flexiblenetwork/details/{ndcid}', [FlexibleNetworkController::class, 'getDetails']);

        Route::post('flexiblenetwork/add', [FlexibleNetworkController::class, 'add']);
        Route::get('flexiblenetwork/dropdown', [FlexibleNetworkController::class, 'flexibledropdown']);



        //Rule Id 

        Route::get('ruleid/search', [FlexibleNetworkController::class, 'RuleIdsearch']);




        //Prioritize  Network

        Route::get('prioritize/search', [PrioritiseNetworkController::class, 'search']);

        Route::get('prioritize/get/{ndcid}', [PrioritiseNetworkController::class, 'networkList']);
        Route::post('prioritize/add', [PrioritiseNetworkController::class, 'add']);

        Route::get('chains/search', [ChainController::class, 'search']);

        Route::get('chains/dropdowns', [ChainController::class, 'dropdowns']);


        Route::get('chain/get/{ndcid}', [ChainController::class, 'getList']);
        Route::post('chain/add', [ChainController::class, 'add']);
    });


    //Provider Type Validation
    Route::get('/provider-type-validation', [ProviderTypeValidationController::class, 'test']);
    Route::get('/provider-type-validation/get', [ProviderTypeValidationController::class, 'get'])->name('provider-type-validation-get');
    Route::get('/provider-type-validation/getFormData', [ProviderTypeValidationController::class, 'getFormData'])->name('provider-type-validation-getFormData');


    //Drug Information
    Route::group(['prefix' => "drug-information/"], function () {
        Route::post('drug-database/add', [DrugDatabaseController::class, 'add']);

        Route::get('drug-database/get', [DrugDatabaseController::class, 'get']);
        Route::get('drug-database/get-drug-prices', [DrugDatabaseController::class, 'getDrugPrices']);
        Route::post('drug-price/add', [DrugDatabaseController::class, 'addDrugPrice']);
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
        Route::get('plan-association/get-client-customer', [PlanAssociationController::class, 'getClientCustomer']);
        Route::get('plan-association/get-client-group', [PlanAssociationController::class, 'getClientGroup']);
        Route::get('plan-association/get-transaction-type', [PlanAssociationController::class, 'getTransactionType']);
        Route::get('plan-association/get-transaction-association', [PlanAssociationController::class, 'getTransactionAssociation']);
        Route::get('plan-association/get-client-group-label', [PlanAssociationController::class, 'getClientGroupLabel']);
        Route::get('plan-association/get-plan-id', [PlanAssociationController::class, 'getPlanId']);

        //Plan Edit
        Route::get('plan-edit/get', [PlanEditController::class, 'get']);
        Route::post('plan-edit/add', [PlanEditController::class, 'add']);

        Route::get('plan-edit/get-plan-edit-data/{planid}', [PlanEditController::class, 'getPlanEditData']);
        Route::get('plan-edit/get-plan-classification', [PlanEditController::class, 'getPlanClassification']);
        Route::get('plan-edit/get-exp-flag', [PlanEditController::class, 'getExpFlag']);
        Route::get('plan-edit/get-pharm-exp-flag', [PlanEditController::class, 'getPharmExpFlag']);
        Route::get('plan-edit/get-prisc-exp-flag', [PlanEditController::class, 'getPriscExpFlag']);
        Route::get('plan-edit/get-exhausted', [PlanEditController::class, 'getExhausted']);
        Route::get('plan-edit/get-tax', [PlanEditController::class, 'getTax']);
        Route::get('plan-edit/get-uc-plan', [PlanEditController::class, 'getUCPlan']);
        Route::get('plan-edit/get-search-indication', [PlanEditController::class, 'getSearchIndication']);
        Route::get('plan-edit/get-formulary', [PlanEditController::class, 'getFormulary']);
        Route::get('plan-association/get-client-customer', [PlanAssociationController::class, 'getClientCustomer']);

        Route::get('plan-edit/get-super-provider-network', [PlanEditController::class, 'getSuperProviderNetwork']);
        Route::get('plan-edit/get-exhausted-benefits', [PlanEditController::class, 'getExhaustedBenefits']);
        Route::get('plan-edit/get-procedure-exception', [PlanEditController::class, 'getProcedureException']);
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
        Route::get('memberdata/dropdown', [MemberController::class, 'getMembersDropDownList']);

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
        Route::get('prior-authorization/authcode_auto_generate', [PriorAuthController::class, 'priorAuthCodeGenerate']);


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
        Route::post('user-defination/submitGroup', [UserDefinationController::class, 'submitGroup']);
        Route::get('user-defination/get-group-id', [UserDefinationController::class, 'getGroupIds']);
        Route::get('user-defination/get-user-access', [UserAccessControl::class, 'getUserAccessMenus']);

        //Verify Drug Coverage
        Route::get('verify-drug-coverage/get-pharmacy-var-ind', [VerifyDrugVCoverage::class, 'getPharmacyVarInd']);
        Route::get('verify-drug-coverage/get-network-var-ind', [VerifyDrugVCoverage::class, 'getNetworkVarInd']);
        Route::get('verify-drug-coverage/get-claim-var-ind', [VerifyDrugVCoverage::class, 'getClaimVarInd']);
        Route::post('verify-drug-coverage/submit', [VerifyDrugVCoverage::class, 'submitVerifyDrugCoverage']);
        Route::get('verify-drug-coverage/get-member-data', [VerifyDrugVCoverage::class, 'getMemberDetails']);

        //Search Audit Trail
        Route::get('search-audit-trial/get-tables', [AuditTrailController::class, 'getTables'])->name('getAllTables');
        Route::post('search-audit-trial/get-user-records', [AuditTrailController::class, 'getUserAllRecord']);
        Route::get('search-audit-trial/get-user_ids', [AuditTrailController::class, 'getUserIds'])->name('getUserIds');
        Route::get('search-audit-trial/get-record-actions', [AuditTrailController::class, 'getRecordAction'])->name('getRecordAction');
        Route::post('search-audit-trial/search-user-log', [AuditTrailController::class, 'searchUserLog'])->name('searchUserLog');
        Route::post('check-query', [AuditTrailController::class, 'check_query']);
        Route::post('search-audit-trial/get-old-user-log', [AuditTrailController::class, 'getOldUserLog']);

        //System parameters
        Route::get('system-parameter/get-parameters', [SystemParameterController::class, 'getSystemParameters']);
        Route::post('system-parameter/add', [SystemParameterController::class, 'updateSystemParameter'])->name('add.SystemParameters');

        Route::get('system-parameters/get-states', [SystemParameterController::class, 'getState'])->name('getState');
        Route::get('system-parameters/get-countries', [SystemParameterController::class, 'getCountries'])->name('getCountries');
        Route::get('system-parameters/get-third-party-types', [SystemParameterController::class, 'getThirdPartyPrice']);

        Route::get('system-parameters/get-UC-plan', [SystemParameterController::class, 'getUCPlan']);
        Route::get('system-parameters/get-tax-status', [SystemParameterController::class, 'getTaxStatus']);
        Route::get('system-parameters/get-automated-termination', [SystemParameterController::class, 'getAutomatedTermination']);
        Route::get('system-parameters/get-overlap-coverage-tie', [SystemParameterController::class, 'getOverlapCoverageTie']);
        Route::get('system-parameters/get-processor-control-flag', [SystemParameterController::class, 'getProcessorControlFlag']);
        Route::get('system-parameters/get-eligibility-change-log', [SystemParameterController::class, 'getEligibilityChangeLog']);

        //Claim History
        Route::post('claim-history/search', [ClaimHistoryController::class, 'searchHistory']);
        Route::get('claim-history/get-ndcdrops', [ClaimHistoryController::class, 'getNDCDropdown']);
        Route::get('claim-history/get-gpidrops', [ClaimHistoryController::class, 'getGPIDropdown']);
        Route::get('claim-history/get-proceduer-code', [ClaimHistoryController::class, 'getProcedureCode']);
        Route::get('claim-history/get-customer-id', [ClaimHistoryController::class, 'getCustomerId']);
        Route::get('claim-history/get-client-id', [ClaimHistoryController::class, 'getClientId']);
        Route::get('claim-history/get-client-group', [ClaimHistoryController::class, 'getClientGroup']);
        Route::post('claim-history/search-optional-data', [ClaimHistoryController::class, 'searchOptionalData']);

        //Zip Codes
        Route::get('zipcode/search', [ZipCodeController::class, 'search']);
        Route::get('zipcode/get/{zip_code}', [ZipCodeController::class, 'getZipCodeList']);
        Route::post('zipcode/submit', [ZipCodeController::class, 'submitFormData']);

        //claim history
        Route::get('claim-history', [ClaimHistoryController::class, 'get']);
        Route::post('search-claim-history', [ClaimHistoryController::class, 'searchClaimHistory']);
    });
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
    Route::get('/provider/id/search', [ProviderTypeController::class, 'IdSearch'])->name('providertype.search'); // SEARCH

    Route::get('/check-provider-type-exist', [ProviderTypeController::class, 'checkProviderTypeExist']);

    // DIAGNOSIS
    Route::get('/diagnosis', [DiagnosisController::class, 'get'])->name('diagnosis.get'); // SEARCH
    Route::post('/diagnosis/submit', [DiagnosisController::class, 'add'])->name('diagnosis.submit'); // add
    Route::post('/diagnosis/delete', [DiagnosisController::class, 'delete'])->name('diagnosis.delete'); // DELETE
    // Route::get('/diagnosis/all', [DiagnosisController::class, 'get'])->name('diagnosis.get'); // SEARCH
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
    Route::post('/ndc/add', [NDCExceptionController::class, 'add'])->name('ndsc.search'); // add

    Route::get('/ndc/list', [NDCExceptionController::class, 'ndcList'])->name('ndsc.search'); // SEARCH
    // Route::get('/get-ndc-list', [NDCExceptionController::class, 'getNdcAll'])->name('ndc.list'); // SEARCH

    Route::get('/ndc/get/{ndcid}/{ndc_name}', [NDCExceptionController::class, 'getNDC'])->name('ndsc.list.get'); // LIST ITEMS

    //REASON-CODE-EXCEPTION
    Route::get('/ndc/ndc-drop-down', [NDCExceptionController::class, 'getNdcDropDown']); // drop down

    Route::get('/reason/exception/search', [ReasonCodeExceptionController::class, 'search'])->name('reason.exception.search'); // SEARCH
    Route::get('/reason/exception/details/{ndcid}', [ReasonCodeExceptionController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAILS
    Route::post('/reason/exception/add', [ReasonCodeExceptionController::class, 'add'])->name('reason.exception.add'); // DETAILS
    Route::get('/reason/exception/get-reject-code', [ReasonCodeExceptionController::class, 'getRejectCode']);
    Route::get('/reason/exception/get-reason-code', [ReasonCodeExceptionController::class, 'getReasonCode']);

    // Drug Classification
    Route::get('/drugcalss/search', [DrugClassController::class, 'search'])->name('drugclass.search'); // SEARCH
    Route::get('drugcalss/get-ndc', [DrugClassController::class, 'getNdc']);
    Route::get('/drugcalss/get/{ndcid}', [DrugClassController::class, 'getDetailsList'])->name('drugclass.list.get'); // LIST ITEMS
    Route::get('/drugcalss/details/{ndcid}', [DrugClassController::class, 'getNDCItemDetails'])->name('drugclass.details.get'); // DETAILS
    Route::post('/drugcalss/add', [DrugClassController::class, 'add']); // add
    Route::get('/drugcategories', [DrugClassController::class, 'DrugCategoryList']); // SEARCH
    Route::get('/drugclass/dropdown', [DrugClassController::class, 'drugClassDropDown']); // SEARCH

    // GPI

    Route::post('/gpi/add', [GPIExceptionController::class, 'add'])->name('gpi.search'); // SEARCH
    Route::get('/gpi/search', [GPIExceptionController::class, 'search'])->name('gpi.search'); // SEARCH
    Route::get('/gpi/get/{ndcid}', [GPIExceptionController::class, 'getNDCList'])->name('gpi.list.get'); // LIST ITEMS
    Route::get('/gpi/details/{ndcid}/{ncdid2}', [GPIExceptionController::class, 'getNDCItemDetails'])->name('gpi.details.get'); // DETAILS
    Route::post('/gpi/add', [GPIExceptionController::class, 'add'])->name('gpi.add'); // add
    Route::get('/gpi/gpi-drop-down', [GPIExceptionController::class, 'getGpiDropDown']);
    Route::get('/gpi/list', [GPIExceptionController::class, 'GpiList'])->name('gpi.search'); // SEARCH




    // THERAPY CLASS
    Route::get('/therapy-class/search', [TherapyClassController::class, 'search'])->name('therapyclass.search'); // SEARCH
    Route::get('/therapy-class/get/{ndcid}', [TherapyClassController::class, 'getTCList'])->name('therapyclass.list.get'); // LIST ITEMS
    Route::get('/therapy-class/details/{ndcid}/{ncdid2}', [TherapyClassController::class, 'getTCItemDetails'])->name('therapyclass.details.get'); // DETAILS
    Route::post('/therapy/add', [TherapyClassController::class, 'add'])->name('therapy.add'); // add
    Route::get('/therapy-class-list', [TherapyClassController::class, 'TherapyClassList'])->name('therapyclass.search'); // SEARCH
    Route::get('/therapy-class-with-desc', [TherapyClassController::class, 'exceptionswithDesc'])->name('therapyclass.exceptionswithDesc'); // SEARCH



    // PROCEDURE EXCEPTION
    Route::get('/procedure/search', [ExceptionProcedureController::class, 'search'])->name('procedure.search'); // SEARCH
    Route::get('/procedure/get/{ndcid}', [ExceptionProcedureController::class, 'getPCList'])->name('procedure.list.get'); // LIST ITEMS
    Route::get('/procedure/details', [ExceptionProcedureController::class, 'getPCItemDetails'])->name('procedure.details.get'); // DETAILS
    Route::post('/procedure/add', [ExceptionProcedureController::class, 'add'])->name('procedure.add'); // SEARCH
    Route::get('/allphysicain_lists', [ExceptionProcedureController::class, 'AllPhysicainLists'])->name('allphysicians'); // SEARCH


    // BENEFIT LIST EXCEPTION
    Route::get('/benefit/search', [BenefitListController::class, 'search'])->name('benefit.search'); // SEARCH
    Route::get('/benefit/get/{ndcid}', [BenefitListController::class, 'getBLList'])->name('benefit.list.get'); // LIST ITEMS
    Route::get('/benefit/details/{ndcid}', [BenefitListController::class, 'getBLItemDetails'])->name('benefit.details.get'); // DETAILS
    Route::post('/benefit/add', [BenefitListController::class, 'add'])->name('benefit.search'); // SEARCH

    Route::get('/benefitderivation/all', [BenefitDerivationController::class, 'getAll'])->name('benefit.all'); // SEARCH
    Route::get('/benefitderivation/search', [BenefitDerivationController::class, 'search'])->name('benefit.search'); // SEARCH
    Route::get('/benefitderivation/get/{ndcid}', [BenefitDerivationController::class, 'getBLList'])->name('benefit.list.get'); // LIST ITEMS
    Route::get('/benefitderivation/details/{ndcid}/{ndcid2}', [BenefitDerivationController::class, 'getBLItemDetails'])->name('benefit.details.get'); // DETAILS
    Route::post('/benefitderivation/add', [BenefitDerivationController::class, 'add'])->name('benefit.search'); // SEARCH
    Route::get('/benifitcodes/all', [BenefitListController::class, 'index']);

    Route::get('/benifitcodes/list/all', [BenefitListController::class, 'BenefitLists']);




    //Provider Type Validation 
    Route::get('/provider-type-validation/get', [ProviderTypeValidationController::class, 'get'])->name('provider-type-validation-get');
    Route::get('/provider-type-validation/getList/{ncdid}', [ProviderTypeValidationController::class, 'getList'])->name('provider-type-validation-get');
    Route::post('/provider-type-validation/add', [ProviderTypeValidationController::class, 'add'])->name('provider-type-validation-get');
    Route::get('/provider-type-validation/getDetails/{ncdid}/{ndcid2}', [ProviderTypeValidationController::class, 'getNDCItemDetails'])->name('provider-type-validation-getFormData');
    Route::get('/provider-type-validation-association-names/list', [ProviderTypeValidationController::class, 'getAllNames'])->name('provider-type-validation-get');





    //Procedure Code List
    Route::get('/procedure-code-list/get', [PrcedureCodeListController::class, 'get'])->name('procedure-code-list-get');
    Route::get('/procedure-code-list/get-code-list', [PrcedureCodeListController::class, 'getProcCodeList'])->name('procedure-code-list-get');
    Route::post('/procedure-code-list/add', [PrcedureCodeListController::class, 'add'])->name('procedure-code-add');

    Route::get('/procedure-code-list/getAll', [PrcedureCodeListController::class, 'getAll'])->name('procedure-code-list-getAll');



    //Super Benefit List
    Route::get('/super-benefit-list/get', [SuperBenefitControler::class, 'get']);
    Route::get('/super-benefit-list/get-super-benefit-code', [SuperBenefitControler::class, 'getBenefitCode']);
    Route::post('/super-benefit-list/add', [SuperBenefitControler::class, 'add']);

    Route::get('/super-benefit-list/get/{id}', [SuperBenefitControler::class, 'getNDCItemDetails']);
});


Route::group(['prefix' => 'validationlist'], function ($router) {

    // NDC
    Route::get('/diagnosis/search', [ValidationListsController::class, 'search'])->name('diagnosis.search'); // SEARCH
    Route::get('/diagnosis/get/{ndcid}', [ValidationListsController::class, 'getDiagnosisList'])->name('diagnosis.list.get'); // LIST ITEMS
    Route::get('/diagnosis/details/{ndcid}', [ValidationListsController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAIL

    Route::get('/limitations', [DiagnosisController::class, 'getLimitations'])->name('diagnosis.get'); // SEARCH

    Route::get('/speciality/search', [SpecialityController::class, 'search'])->name('speciality.search'); // SEARCH
    Route::get('/speciality/get/{specialty_id}', [SpecialityController::class, 'getSpecialityList'])->name('diagnosis.list.get'); // LIST ITEMS
    Route::get('/speciality/details/{specialty_id}/{specialty_list}', [SpecialityController::class, 'getSpecialityDetails'])->name('ndsc.details.get'); // DETAIL
    Route::post('/speciality/submit-speciality-form', [SpecialityController::class, 'addSpeciality']); // add update speciality

    Route::get('/eligibility/search', [EligibilityValidationListController::class, 'search']); // SEARCH
    // Route::get('/eligibility/get/{ndcid}', [EligibilityValidationListController::class, 'getSpecialityList'])->name('diagnosis.list.get'); // LIST ITEMS
    // Route::get('/eligibility/details/{elig_lis_id}', [EligibilityValidationListController::class, 'getEligibilityDetails'])->name('eligibility.details.get'); // DETAIL
    // Route::post('/eligibility/submit-eligiblity-form', [EligibilityValidationListController::class, 'addEligiblityData']);

    Route::get('/eligibility/dropdown', [EligibilityValidationListController::class, 'DropDown']);


    Route::get('/provider/search', [ProviderController::class, 'search'])->name('provider.search'); // SEARCH
    Route::get('/provider/get-provider-options', [ProviderController::class, 'getProviderOptions']); // SEARCH
    Route::get('/provider/get/{provider_list}', [ProviderController::class, 'getProviderValidationList'])->name('provider.list.get'); // LIST ITEMS
    Route::get('/provider/details/{provider_list}/{provider_nabp}', [ProviderController::class, 'getProviderDetails'])->name('ndsc.details.get'); // DETAIL
    Route::post('/provider/submit-provider-form', [ProviderController::class, 'addProviderData']);
    Route::get('/provider/provider-list-drop-down/', [ProviderController::class, 'searchDropDownProviderList']);


    //DIAGNOSIS VALIDATION LIST
    Route::get('/diagnosisvalidation/search', [DiagnosisValidationListController::class, 'search'])->name('diagnosisvalidation.search'); // SEARCH
    Route::get('/diagnosisvalidation/get/{diagnosis_list}', [DiagnosisValidationListController::class, 'getPriorityDiagnosis'])->name('diagnosisvalidation.list.get'); // LIST ITEMS
    Route::get('diagnosisvalidation/diagnosis-code-list/{disgnosis_code?}', [DiagnosisValidationListController::class, 'getDiagnosisCodeList']); //diagnosis code drop down with search
    Route::get('diagnosisvalidation/limitation-code-list/{limitation_code?}', [DiagnosisValidationListController::class, 'getLimitationsCode']); //limitationid drop down
    Route::get('/diagnosisvalidation/diagnosis_limitations/{diagnosis_list}/{diagnosis_id}', [DiagnosisValidationListController::class, 'getDiagnosisLimitations'])->name('diagnosisvalidation.details.get'); // DETAIL
    Route::post('/diagnosisvalidation/submit-diagnosis-form', [DiagnosisValidationListController::class, 'addDiagnosisValidations']); //add and update diagnosis data
    // Route::post('/diagnosisvalidation/submit-diagnosis-form', function () {
    //     return response()->json(['error' => 'checking']);
    // }); //add and update diagnosis data
    Route::post('/diagnosisvalidation/submit-diagnosis-limitation-form', [DiagnosisValidationListController::class, 'DiagnosisLimitationAdd']);
    Route::get('/diagnosisvalidation/validation-list/{diagnosis_list}', [DiagnosisValidationListController::class, 'getDiagnosisValidations']);
    Route::get('/diagnosisvalidation/details/{diagnosis_list}/{diagnosis_id}', [DiagnosisValidationListController::class, 'getDiagnosisDetails']);
    Route::get('/diagnosisvalidation/getAll', [DiagnosisValidationListController::class, 'getAll']);
    Route::post('/diagnosisvalidation/submit-diagnosis-validation-form', [DiagnosisValidationListController::class, 'updatePriorityDiagnosisValidation']);
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

    Route::get('/accumulated/all', [AccumlatedController::class, 'getAllAcuumlatedBenefits'])->name('accumulated.all'); // DETAIL


    Route::get('/accumulated/benifit/search', [AccumlatedBenifitController::class, 'search'])->name('accumulated.benifit.search'); // SEARCH
    Route::get('/accumulated/benifit/get/{ndcid}', [AccumlatedBenifitController::class, 'getList'])->name('accumulated.benifit.list.get'); // LIST ITEMS
    Route::get('/accumulated/benifit/details/{ndcid}', [AccumlatedBenifitController::class, 'getDetails'])->name('accumulated.benifit.details.get'); // DETAIL
    Route::post('/accumulated/benifit/add', [AccumlatedBenifitController::class, 'add'])->name('accumulated.benifit.add'); // SEARCH


    Route::get('/gpiExclusion/search', [GpiExclusionController::class, 'search'])->name('gpiExclusion.search'); // SEARCH


    Route::get('/gpiExclusion/get/{ndcid}', [GpiExclusionController::class, 'getList'])->name('accumulated.benifit.list.get'); // LIST ITEMS
    Route::get('/gpi/dropdowns', [GpiExclusionController::class, 'GPIS'])->name('gpi.search'); // SEARCH
    Route::get('/gpiExclusion/details/{ndcid}', [GpiExclusionController::class, 'getDetails'])->name('gpiExclusion.get'); // DETAIL
    Route::post('/gpiExclusion/add', [GpiExclusionController::class, 'add'])->name('gpiExclusion.add'); // ADD
    Route::get('/gpi/exclusions/dropdowns', [GpiExclusionController::class, 'allGpiExclusions'])->name('gpi.search'); // SEARCH




    Route::get('/ndcExclusion/search', [NdcExlusionController::class, 'search'])->name('ndcExclusion.search'); // SEARCH


    Route::get('/ndcExclusion/get/{ndcid}', [NdcExlusionController::class, 'getList'])->name('ndcExclusion.list.get'); // LIST ITEMS

    Route::get('/ndcExclusion/details/{ndcid}', [NdcExlusionController::class, 'getDetails'])->name('ndcExclusion.get'); // DETAIL
    Route::post('/ndcExclusion/add', [NdcExlusionController::class, 'add'])->name('ndcExclusion.add'); // SEARCH

    Route::get('/ndcExclusion/all', [NdcExlusionController::class, 'AllNdcGpisExcusions'])->name('ndcExclusion.list.get'); // LIST ITEMS

    Route::get('/ndcExclusion/dropdown', [NdcExlusionController::class, 'NdcExclusiondropdowns'])->name('ndcExclusion.get'); // LIST ITEMS


    Route::get('customer/search', [MajorMedicalController::class, 'search']);
    Route::get('client/get/{customerid}', [MajorMedicalController::class, 'getClient']);

    Route::get('clientgroup/get/{client}', [MajorMedicalController::class, 'getClientGroup']);

    Route::get('clientgroup/details/{client}', [MajorMedicalController::class, 'getDetails']);
    Route::post('major/medical/add', [MajorMedicalController::class, 'add']);

    Route::post('clientgroup/add', [ClientGroupController::class, 'add']);



    // getDetails


    Route::get('/prescriber/search', [PrescriberValidationController::class, 'search'])->name('prescriber.search'); // SEARCH
    Route::get('/prescriber/get/{physicain_list}', [PrescriberValidationController::class, 'getProviderValidationList'])->name('prescriber.list.get'); // LIST ITEMS
    Route::get('/prescriber/details/{physicain_list}/{physicain_id}', [PrescriberValidationController::class, 'getProviderDetails'])->name('prescriber.details.get'); // DETAIL
    Route::get('prescriber/prescriber-list-drop-down', [PrescriberValidationController::class, 'searchDropDownPrescriberList']);
    Route::post('/prescriber/submit-prescriber-form', [PrescriberValidationController::class, 'addPrescriberData']);
});


Route::group(['prefix' => 'prescriberdata'], function ($router) {


    Route::get('/prescriber/search', [PrescriberController::class, 'search'])->name('prescriber.search'); // SEARCH

    Route::get('/prescriber/details/{ndcid}', [PrescriberController::class, 'getDetails'])->name('prescriber.get'); // DETAIL

    Route::post('/prescriber/add', [PrescriberController::class, 'add'])->name('prescriber.add'); // UPDATE


});




Route::post('customer/add', [CustomerController::class, 'add']);
Route::post('customer/id/generate', [CustomerController::class, 'generateCustomerId']);
Route::get('customer/get', [CustomerController::class, 'searchCutomer']);

Route::get('plan/get/{planid}', [CustomerController::class, 'getPlanId']);
Route::get('planids/all', [CustomerController::class, 'All']);


Route::get('planid/search', [CustomerController::class, 'searchPlanId']);



Route::get('superprovidernetwork/get/{id}', [CustomerController::class, 'searchSuperProviderNetworkId']);
Route::get('superprovidernetworkids', [CustomerController::class, 'ALLSuperProviderNetworkIdS']);


Route::get('customer/get/{customerid}', [CustomerController::class, 'GetCustomer']);


Route::get('client/get', [ClientController::class, 'searchClient']);
Route::get('client/get/{clientid}', [ClientController::class, 'GetOneClient']);
Route::post('client/add', [ClientController::class, 'add']);


Route::get('clientgroup/get', [ClientGroupController::class, 'searchClientgroup']);
Route::get('clientgroup/get/{clientgrpid}', [ClientGroupController::class, 'GetOneClientGroup']);




// COMMOM
Route::get('/countries', [Controller::class, 'Contries'])->name('countries');
Route::get('/countries/search/{c_id?}', [Controller::class, 'ContriesSearch'])->name('countries.search');

Route::get('/states/{countryid}', [Controller::class, 'getStatesOfCountry'])->name('states');
Route::get('/state/search/{stateid?}', [Controller::class, 'getStatesOfCountrySearch'])->name('state.search');
Route::get('/states', [Controller::class, 'getStatesOfCountry'])->name('states');
Route::get('/member', [Controller::class, 'getMember'])->name('member');
Route::get('/provider', [Controller::class, 'getProvider']);
Route::post('/validationlist/eligibility/submit-eligiblity-form', [EligibilityValidationListController::class, 'addEligiblityData']);
Route::get('/validationlist/eligibility/details/{elig_lis_id}', [EligibilityValidationListController::class, 'getEligibilityDetails']); // DETAIL




Route::group(['prefix' => 'providerdata'], function ($router) {
    Route::get('/provider/search', [ProviderDataProviderController::class, 'search'])->name('provider.search'); // SEARCH
    Route::get('/provider/get/{ndcid}', [ProviderDataProviderController::class, 'getProviderList'])->name('provider.list.get'); // LIST ITEMS
    Route::get('/provider/details/{ndcid}', [ProviderDataProviderController::class, 'getNDCItemDetails'])->name('ndsc.details.get'); // DETAIL
    Route::post('/provider/add', [ProviderDataProviderController::class, 'add'])->name('ndsc.details.get'); // DETAIL
    Route::get('/provider/traditionalid/search', [ProviderDataProviderController::class, 'TraditionalIdsearch'])->name('provider.traditionalid.search'); // SEARCH
    Route::post('/provider/traditionalnetwork/add', [ProviderDataProviderController::class, 'addTraditionalNetwork'])->name('traditinal.add'); // DETAIL
    Route::get('/provider/flexiblelid/search', [ProviderDataProviderController::class, 'FlexibleIdsearch'])->name('provider.traditionalid.search'); // SEARCH
    Route::post('/provider/flexiblenetwork/add', [ProviderDataProviderController::class, 'addFlexibleNetwork'])->name('flexi.add'); // DETAIL


    //SUPER PROVIDER NETWORK
    // Route::post('customer/add', [CustomerController::class, 'saveIdentification']);
    // Route::post('customer/id/generate', [CustomerController::class, 'generateCustomerId']);
    Route::get('supernetwork/search', [SuperProviderNetworkController::class, 'search']);

    Route::get('supernetwork/get/{ndcid}', [SuperProviderNetworkController::class, 'networkList']);
    Route::post('superprovider/add', [SuperProviderNetworkController::class, 'add']);
    Route::get('supernetwork/dropdown', [SuperProviderNetworkController::class, 'dropDown']);



    //TRADITIONAL NETWORK

    Route::get('traditionalnetwork/search', [TraditionalNetworkController::class, 'search']);
    Route::get('traditionalnetwork/get/{ndcid}', [TraditionalNetworkController::class, 'getList']);
    Route::get('traditionalnetwork/details/{ndcid}', [TraditionalNetworkController::class, 'getDetails']);
    Route::post('traditionalnetwork/add', [TraditionalNetworkController::class, 'add']);
    Route::get('traditionalnetwork/all', [TraditionalNetworkController::class, 'all']);

    Route::get('traditionalnetworks/dropdowns', [TraditionalNetworkController::class, 'TraditionalNetworkIdsDropdwon']);




    //Flexible Network

    Route::get('flexiblenetwork/search', [FlexibleNetworkController::class, 'search']);
    Route::get('flexiblenetwork/get/{ndcid}', [FlexibleNetworkController::class, 'getList']);
    Route::get('flexiblenetwork/details/{ndcid}', [FlexibleNetworkController::class, 'getDetails']);
    Route::post('flexiblenetwork/add', [FlexibleNetworkController::class, 'add']);
    Route::get('flexiblenetwork/all', [FlexibleNetworkController::class, 'all']);


    //Rule Id 

    Route::get('ruleid/search', [FlexibleNetworkController::class, 'RuleIdsearch']);




    //Prioritize  Network

    Route::get('prioritize/search', [PrioritiseNetworkController::class, 'search']);

    Route::get('prioritize/get/{ndcid}', [PrioritiseNetworkController::class, 'networkList']);
    Route::post('prioritize/add', [PrioritiseNetworkController::class, 'add']);
    Route::get('prioritize/details/{ndcid}/{ncdid2}/{id3}', [PrioritiseNetworkController::class, 'getDetails']);
});


//Provider Type Validation
Route::get('/provider-type-validation', [ProviderTypeValidationController::class, 'test']);
Route::get('/provider-type-validation/get', [ProviderTypeValidationController::class, 'get'])->name('provider-type-validation-get');
Route::get('/provider-type-validation/getFormData', [ProviderTypeValidationController::class, 'getFormData'])->name('provider-type-validation-getFormData');

//Third Party Pricing(module)
Route::group(['prefix' => 'third-party-pricing/'], function () {
    //Price Schedule
    Route::get('price-schedule/get', [PriceScheduleController::class, 'get']);
    Route::get('price-schedule/get-all', [PriceScheduleController::class, 'getAll']);
    Route::get('price-schedule/get-price-schedule-data', [PriceScheduleController::class, 'getPriceScheduleDetails']);
    // Route::post('price-schedule/update', [PriceScheduleController::class, 'updateBrandItem'])->name('price_schedule_update');
    Route::get('price-schedule/get-brand-type', [PriceScheduleController::class, 'getBrandType']);
    Route::get('price-schedule/get-brand-source', [PriceScheduleController::class, 'getBrandSource']);
    Route::post('price-schedule/submit', [PriceScheduleController::class, 'submitPriceSchedule']);
    Route::get('price-schedule/get-all', [PriceScheduleController::class, 'getAll']);

    //Copay Schedule
    Route::get('copay-schedule/get', [CopayScheduleController::class, 'get'])->name('get.copay');
    Route::get('copay-schedule/get-copay-data', [CopayScheduleController::class, 'getCopayData'])->name('get.copay.single');
    Route::get('copay-schedule/get-source', [CopayScheduleController::class, 'getSourceOptions']);
    Route::get('copay-schedule/get-factor', [CopayScheduleController::class, 'getFactor']);
    Route::get('copay-schedule/get-list-options', [CopayScheduleController::class, 'getListOptions']);
    Route::get('copay-schedule/get-daw-options', [CopayScheduleController::class, 'getDawOptions']);
    Route::get('copay-schedule/get-coinsurance-calculation-option', [CopayScheduleController::class, 'getConinsuranceCalculationOption']);
    Route::post('copay-schedule/submit', [CopayScheduleController::class, 'submitCopaySchedule']);

    //Copay Step Schedule
    Route::get('copay-step-schedule/get', [CopayStepScheduleController::class, 'get'])->name('get.copay-step');
    Route::get('copay-step-schedule/get-days-supply', [CopayStepScheduleController::class, 'getDaysSupply']);
    Route::get('copay-step-schedule/get-max-cost', [CopayStepScheduleController::class, 'getMaxCost']);
    Route::get('copay-step-schedule/check-copay-list-existing', [CopayStepScheduleController::class, 'checkCopayListExist']);
    Route::post('copay-step-schedule/submit', [CopayStepScheduleController::class, 'submit'])->name('submitstep');
    Route::get('copay-step-schedule/getmaxcosts/{id}', [CopayStepScheduleController::class, 'getmaxList'])->name('getmaxlists');


    Route::get('copay-step-schedule/getcopaylistdata', [CopayStepScheduleController::class, 'getList']);


    //MAC List
    Route::get('mac-list/get', [MacListController::class, 'get'])->name('get.macList');
    Route::get('mac-list/get-mac-list', [MacListController::class, 'getMacList'])->name('get.mac-list.single');
    Route::get('mac-list/get-price-source', [MacListController::class, 'getPriceSource']);
    Route::get('mac-list/get-price-type', [MacListController::class, 'getPriceType']);
    Route::post('mac-list/submit', [MacListController::class, 'submit']);

    //Tax Schedule
    Route::get('tax-schedule/get', [TaxScheduleController::class, 'get']);
    Route::get('tax-schedule/get-calculations', [TaxScheduleController::class, 'getCalculations']);
    Route::get('tax-schedule/get-base-prices', [TaxScheduleController::class, 'getBasePrices']);
    Route::post('tax-schedule/submit', [TaxScheduleController::class, 'submitTaxSchedule']);

    //Procedure UCR list
    Route::get('procedure-ucr-list/get', [ProcedureUcrList::class, 'get']);
    Route::get('procedure-ucr-list/get-procedure-list-data', [ProcedureUcrList::class, 'getProcedureListData']);
    Route::get('procedure-ucr-list/get-procedure-code', [ProcedureUcrList::class, 'getProcedureCode']);
    Route::post('procedure-ucr-list/submit', [ProcedureUcrList::class, 'submitProcedureList']);

    //RVA List
    Route::get('rva-list/get', [RvaListController::class, 'get']);
    Route::get('rva-list/get-rva-list', [RvaListController::class, 'getRvaList']);
    Route::post('rva-list/submit', [RvaListController::class, 'submitRva']);
    Route::get('rva-list/dropdown', [RvaListController::class, 'RvaListDropdown']);
});


//Drug Information
Route::group(['prefix' => "drug-information/"], function () {
    Route::post('drug-database/add', [DrugDatabaseController::class, 'add']);

    Route::get('drug-database/get', [DrugDatabaseController::class, 'get']);
    Route::get('drug-database/get-drug-prices', [DrugDatabaseController::class, 'getDrugPrices']);
    Route::post('drug-price/add', [DrugDatabaseController::class, 'addDrugPrice']);
    Route::get('ndc-gpi/search', [NdcGpiController::class, 'search']);
    Route::get('ndc-gpi/details/{ndcid}', [NdcGpiController::class, 'getDetails']);

    Route::get('ndc-gpi/drop-down', [NdcGpiController::class, 'GpiDropDown']);
});


//Plan Design
Route::group(['prefix' => 'plan-design/'], function () {
    //Plan Association
    Route::get('plan-association/get/{id}', [PlanAssociationController::class, 'getDetails']);
    Route::get('plan-association/search', [PlanAssociationController::class, 'search']);

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
    // Route::get('memberdata/form-submit', [MemberController::class, 'submitMemberForm']);
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
    Route::post('prior-authorization/submit', [PriorAuthController::class, 'submitPriorAuthorization']);


    //Plan Validation
    Route::get('plan-validation/get', [PlanValidationController::class, 'get']);
    Route::get('plan-validation/get-client-details', [PlanValidationController::class, 'getClientDetails']);
    Route::get('plan-validation/get-plan-id', [PlanValidationController::class, 'getPlanId']);
    Route::post('plan-validation/add-plan-validaion', [PlanValidationController::class, 'addPlanValidation']);
});



Route::group(['prefix' => "drug-information/"], function () {
    Route::post('drug-database/add', [DrugDatabaseController::class, 'add']);

    Route::get('drug-database/get', [DrugDatabaseController::class, 'get']);
    Route::get('drug-database/get-drug-prices', [DrugDatabaseController::class, 'getDrugPrices']);
    Route::post('drug-price/add', [DrugDatabaseController::class, 'addDrugPrice']);
    Route::get('ndc-gpi/search', [NdcGpiController::class, 'search']);
    Route::get('ndc-gpi/details/{ndcid}', [NdcGpiController::class, 'getDetails']);

    Route::get('ndc-gpi/drop-down', [NdcGpiController::class, 'GpiDropDown']);
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
    Route::get('system-parameter/add', [SystemParameterController::class, 'add'])->name('add.SystemParameters');

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

    Route::get('zipcode/search', [ZipCodeController::class, 'search']);
    Route::get('zipcode/get/{zip_code}', [ZipCodeController::class, 'getZipCodeList']);
    Route::post('zipcode/submit', [ZipCodeController::class, 'submitFormData']);

    //claim history
    Route::get('claim-history', [ClaimHistoryController::class, 'get']);
});
