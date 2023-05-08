<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\pensopayController;
use App\Http\Controllers\QuickPayController;

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
    return view('auth/login');
});

Auth::routes();

Route::middleware('auth')->prefix('client')->name('client.')->group(function () {

    Route::get('/',[ClientController::class, 'clientIndex'])->name('index');

    Route::prefix('payment')->name('payment.')->group(function() {

        Route::post('/pensopay/{client}',[QuickPayController::class,'pensopay'])->name('pensopay');

    });
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/',[App\Http\Controllers\AdminController::class, 'adminIndex'])->name('index');

    Route::prefix('payment')->name('payment.')->group(function() {

        Route::post('/store/{client}',[App\Http\Controllers\AdminController::class,'store'])->name('store');
    });
});

Route::get('/history', [App\Http\Controllers\QuickPayController::class, 'getHistory'])->name('/history');


// QUICKPAY API
Route::post('/handleCallback', [App\Http\Controllers\QuickPayController::class, 'handleCallback'])->name('handleCallback');

Route::get('/quickPayTable', [App\Http\Controllers\QuickPayController::class, 'quickPayTable'])->name('quickPayTable');

Route::get('/getAllPayment', [App\Http\Controllers\QuickPayController::class, 'getAllPayment'])->name('getAllPayment');

Route::get('/getAllPaymentByOrderId', [App\Http\Controllers\QuickPayController::class, 'getAllPaymentByOrderId'])->name('/getAllPaymentByOrderId');

Route::get('/deletePaymentById', [App\Http\Controllers\QuickPayController::class, 'deletePaymentById'])->name('/deletePaymentById');

Route::get('/history', [App\Http\Controllers\QuickPayController::class, 'getHistory'])->name('/getHistory');

Route::get('/getInvoice', [App\Http\Controllers\QuickPayController::class, 'getInvoice'])->name('/getInvoice');

Route::get('/pay', [App\Http\Controllers\QuickPayController::class, 'pay'])->name('pay');

// QUICKPAY API

// PENSOPAY API


Route::get('/success', [App\Http\Controllers\pensopayController::class, 'getSuccess'])->name('success');

Route::get('/cancel', [App\Http\Controllers\pensopayController::class, 'getCancel'])->name('cancel');

Route::resource('pensopay', App\Http\Controllers\pensopayController::class);


Route::get('/pensopayForm', [App\Http\Controllers\pensopayController::class, 'pensopayForm'])->name('pensopayForm');

// PENSOPAY API
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::delete('/clients/{id}', [App\Http\Controllers\AdminController::class, 'delete'])->name('clients.delete');
Route::get('/clients/search', [AdminController::class, 'search'])->name('clients.search');
