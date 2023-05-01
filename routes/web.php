<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\pensopayController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->prefix('client')->name('client.')->group(function () {

    Route::get('/',[ClientController::class, 'clientIndex'])->name('index');

    Route::prefix('payment')->name('payment.')->group(function() {

        Route::post('/pensopay/{client}',[pensopayController::class,'pensopay'])->name('pensopay');

    });
});
Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('admin');

// QUICKPAY API

Route::get('/getAllPayment', [App\Http\Controllers\QuickPayController::class, 'getAllPayment'])->name('getAllPayment');

Route::get('/getAllPaymentByOrderId', [App\Http\Controllers\QuickPayController::class, 'getAllPaymentByOrderId'])->name('/getAllPaymentByOrderId');

Route::get('/deletePaymentById', [App\Http\Controllers\QuickPayController::class, 'deletePaymentById'])->name('/deletePaymentById');

Route::get('/getHistory', [App\Http\Controllers\QuickPayController::class, 'getHistory'])->name('/getHistory');

Route::get('/pay', [App\Http\Controllers\QuickPayController::class, 'pay'])->name('pay');

// QUICKPAY API

// PENSOPAY API

Route::get('/success', [App\Http\Controllers\pensopayController::class, 'getSuccess'])->name('success');

Route::get('/cancel', [App\Http\Controllers\pensopayController::class, 'getCancel'])->name('cancel');

Route::post('/callback', [App\Http\Controllers\pensopayController::class, 'getCallback'])->name('callback');

Route::resource('pensopay', App\Http\Controllers\pensopayController::class);


Route::get('/pensopayForm', [App\Http\Controllers\pensopayController::class, 'pensopayForm'])->name('pensopayForm');

// PENSOPAY API
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::delete('/clients/{id}', [App\Http\Controllers\HomeController::class, 'delete'])->name('clients.delete');
Route::get('/clients/search', [HomeController::class, 'search'])->name('clients.search');
