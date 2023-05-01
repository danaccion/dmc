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

Route::get('/test-user', [ClientController::class, 'index'])->middleware(['auth', 'is-active']);

Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'] )->name('admin');

Route::get('/success', [App\Http\Controllers\pensopayController::class, 'getSuccess'])->name('success');

Route::get('/cancel', [App\Http\Controllers\pensopayController::class, 'getCancel'])->name('cancel');

Route::get('/callback', [App\Http\Controllers\pensopayController::class, 'getCallback'])->name('callback');

Route::resource('pensopay', App\Http\Controllers\pensopayController::class);

Route::get('/pensopay', [App\Http\Controllers\pensopayController::class, 'pensopay'])->name('pensopay');

Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::delete('/clients/{id}', [App\Http\Controllers\AdminController::class, 'delete'])->name('clients.delete');
Route::get('/clients/search', [AdminController::class, 'search'])->name('clients.search');
