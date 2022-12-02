<?php

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
use App\Http\Controllers\drug_information\DrugDatabaseController;
use App\Http\Controllers\exception_list\PrcedureCodeListController;
use App\Http\Controllers\exception_list\ProviderTypeValidationController;
use App\Http\Controllers\plan_design\PlanAssociationController;
use App\Http\Controllers\exception_list\SuperBenefitControler;
use App\Http\Controllers\membership\MemberController;
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

Route::group(['prefix' => 'users', 'middleware' => 'CORS'], function ($router) {
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

    // REASON CODES
    Route::get('/reasons', [ReasonsController::class, 'get'])->name('reasons.get'); // SEARCH
    Route::post('/reasons/submit', [ReasonsController::class, 'add'])->name('reasons.submit'); // add
    Route::post('/reasons/delete', [ReasonsController::class, 'delete'])->name('reasons.delete'); // DELETE


    // PROCEDURE 
    Route::get('/procedure', [ProcedureController::class, 'get'])->name('procedure.get'); // SEARCH
    Route::post('/procedure/submit', [ProcedureController::class, 'add'])->name('procedure.submit');  // add
    Route::post('/procedure/delete', [ProcedureController::class, 'delete'])->name('procedure.delete'); // DELETE


    // PROVIDER TYPE
    Route::get('/provider-type', [ProviderTypeController::class, 'get'])->name('providertype.get'); // SEARCH
    Route::post('/provider-type/submit', [ProviderTypeController::class, 'add'])->name('providertype.submit');  // add
    Route::post('/provider-type/delete', [ProviderTypeController::class, 'delete'])->name('providertype.delete'); // DELETE


    // DIAGNOSIS
    Route::get('/diagnosis', [DiagnosisController::class, 'get'])->name('diagnosis.get'); // SEARCH
    Route::post('/diagnosis/submit', [DiagnosisController::class, 'add'])->name('diagnosis.submit'); // add
    Route::post('/diagnosis/delete', [DiagnosisController::class, 'delete'])->name('diagnosis.delete'); // DELETE


    // SERVICE TYPES
    Route::get('/service-type', [ServiceTypeController::class, 'get'])->name('servicetype.get'); // SEARCH
    Route::post('/service-type/submit', [ServiceTypeController::class, 'add'])->name('servicetype.submit'); // add
    Route::post('/service-type/delete', [ServiceTypeController::class, 'delete'])->name('servicetype.delete'); // DELETE


    // SERVICE MODIFIER
    Route::get('/service-modifier', [ServiceModifierController::class, 'get'])->name('servicemodifier.get');  // SEARCH
    Route::post('/service-modifier/submit', [ServiceModifierController::class, 'add'])->name('servicemodifier.submit'); // add
    Route::post('/service-modifier/delete', [ServiceModifierController::class, 'delete'])->name('servicemodifier.delete'); // DELETE


    // COUSE OF LOSS
    Route::get('/couse-of-loss', [CouseOfLossController::class, 'get'])->name('couseofloss.get'); // SEARCH
    Route::post('/couse-of-loss/submit', [CouseOfLossController::class, 'add'])->name('couseofloss.submit'); // add
    Route::post('/couse-of-loss/delete', [CouseOfLossController::class, 'delete'])->name('couseofloss.delete'); // DELETE

    
});


Route::post('customer/add', [CustomerController::class, 'saveIdentification']);
Route::post('customer/id/generate', [CustomerController::class, 'generateCustomerId']);


// COMMOM
Route::get('/countries', [Controller::class, 'Contries'])->name('countries');

Route::get('/states/{countryid}', [Controller::class, 'getStatesOfCountry'])->name('states');

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
Route::group(['prefix' => 'third-party-pricing/'], function(){
    //Price Schedule
    Route::get('price-schedule/get',[PriceScheduleController::class, 'get']);
    Route::get('price-schedule/get-price-schedule-data', [PriceScheduleController::class, 'getPriceScheduleDetails']);

    //Copay Schedule
    Route::get('copay-schedule/get', [CopayScheduleController::class, 'get'])->name('get.copay');
    Route::get('copay-schedule/get-copay-data', [CopayScheduleController::class, 'getCopayData'])->name('get.copay.single');

    //Copay Step Schedule
    Route::get('copay-step-schedule/get', [CopayStepScheduleController::class, 'get'])->name('get.copay-step');
    // Route::get('copay-step-schedule/get-copay-data', [CopayStepScheduleController::class, 'getCopayData'])->name('get.copay-step.single');

    //MAC List
    Route::get('mac-list/get', [MacListController::class, 'get'])->name('get.macList');
    Route::get('mac-list/get-mac-list', [MacListController::class, 'getMacList'])->name('get.mac-list.single');

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
Route::group(['prefix' => "drug-information/"], function(){
    Route::get('drug-database/get', [DrugDatabaseController::class, 'get']);
    Route::get('drug-database/get-drug-prices', [DrugDatabaseController::class, 'getDrugPrices']);
});


//Plan Design
Route::group(['prefix' => 'plan-design/'], function(){
    //Plan Association
    Route::get('plan-association/get', [PlanAssociationController::class, 'get']);
    //Plan Edit
    Route::get('plan-edit/get', [PlanEditController::class, 'get']);
    Route::get('plan-edit/get-plan-edit-data', [PlanEditController::class, 'getPlanEditData']);
});

//Membership
Route::group(['prefix' => 'membership/'], function(){
    //Member
    Route::get('memberdata/get', [MemberController::class, 'get']);
    Route::get('memberdata/get-member-coverage-history-data', [MemberController::class, 'getCoverageHistory']);
    Route::get('memberdata/get-health-condition', [MemberController::class, 'getHealthCondition']);
    Route::get('memberdata/get-diagnosis-history', [MemberController::class, 'getDiagnosisHistory']);
    Route::get('memberdata/get-prior-authorization', [MemberController::class, 'getPriorAuthorization']);
    Route::get('memberdata/get-log-change-data', [MemberController::class, 'getLogChangeData']);

    //Prior Authorization
    Route::get('prior-authorization/get', [PriorAuthController::class, 'get']);
});
 
