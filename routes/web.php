<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientInfoController;
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

Route::middleware('auth')->group(function () {
    // Admin dashboard route
    Route::middleware('can:isAdmin')->group(function () {
        Route::post('/remove', [App\Http\Controllers\AdminController::class, 'remove'])->name('remove');
        Route::get('/home', [App\Http\Controllers\AdminController::class, 'adminIndex'])->name('admin.index');
    });
    Route::get('/client', [ClientController::class, 'clientIndex'])->name('index');
    // 

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

    Route::get('success/{id}', [App\Http\Controllers\ClientInfoController::class, 'getSuccess'])->name('success');

    Route::get('/cancel', [App\Http\Controllers\ClientInfoController::class, 'getCancel'])->name('cancel');

    // PENSOPAY API
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    
    Route::get('/clientssearch', [App\Http\Controllers\AdminController::class, 'search'])->name('clients.search');
});

Auth::routes();

Route::middleware('auth')->prefix('client')->name('client.')->group(function () {

    Route::get('/', [ClientController::class, 'clientIndex'])->name('index');

    Route::prefix('list')->name('list.')->group(function () {

        Route::get('/table', [ClientInfoController::class, 'getAllClientInfo'])->name('index');

    });

    Route::prefix('payment')->name('payment.')->group(function () {

        Route::post('/pensopay/{client}', [QuickPayController::class, 'pensopay'])->name('pensopay');

    });
});


Route::middleware('auth', 'can:isAdmin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'adminIndex'])->name('index');

    Route::prefix('payment')->name('payment.')->group(function () {
        Route::post('/store/{client}', [App\Http\Controllers\AdminController::class, 'store'])->name('store');
    });
});