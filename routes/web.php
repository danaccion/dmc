<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/success', [App\Http\Controllers\pensopayController::class, 'getSuccess'])->name('success');

Route::get('/cancel', [App\Http\Controllers\pensopayController::class, 'getCancel'])->name('cancel');

Route::get('/callback', [App\Http\Controllers\pensopayController::class, 'getCallback'])->name('callback');

Route::resource('pensopay', App\Http\Controllers\pensopayController::class);

Route::get('/pensopay', [App\Http\Controllers\pensopayController::class, 'pensopay'])->name('pensopay');
