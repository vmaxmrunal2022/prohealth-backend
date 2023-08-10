<?php

use App\Http\Controllers\administrator\UserDefinationController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/test', function () {
//     return view('welcome');
// });

// Route::get('/test', [TestController::class, 'index']);
Route::get('/test', [UserDefinationController::class, 'profileUpload']);

Route::post('/store-file', [UserDefinationController::class, 'storeFile'])->name('storeFile');
