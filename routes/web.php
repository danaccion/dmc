<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

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

});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// QUICKPAY API

Route::get('/getAllPayment', [App\Http\Controllers\QuickPayController::class, 'getAllPayment'])->name('getAllPayment');

Route::get('/pay', [App\Http\Controllers\QuickPayController::class, 'pay'])->name('pay');

// QUICKPAY API

// PENSOPAY API

Route::get('/success', [App\Http\Controllers\pensopayController::class, 'getSuccess'])->name('success');

Route::get('/cancel', [App\Http\Controllers\pensopayController::class, 'getCancel'])->name('cancel');

Route::get('/callback', [App\Http\Controllers\pensopayController::class, 'getCallback'])->name('callback');

Route::resource('pensopay', App\Http\Controllers\pensopayController::class);

Route::get('/pensopay', [App\Http\Controllers\pensopayController::class, 'pensopay'])->name('pensopay');

Route::get('/pensopayForm', [App\Http\Controllers\pensopayController::class, 'pensopayForm'])->name('pensopayForm');

// PENSOPAY API
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::delete('/clients/{id}', [App\Http\Controllers\HomeController::class, 'delete'])->name('clients.delete');
Route::get('/clients/search', [HomeController::class, 'search'])->name('clients.search');
