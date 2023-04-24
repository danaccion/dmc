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

Route::resource('pensopay', App\Http\Controllers\pensopayController::class);

Route::get('/pensopay', [App\Http\Controllers\pensopayController::class, 'pensopay'])->name('pensopay');

Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::delete('/clients/{id}', [App\Http\Controllers\HomeController::class, 'delete'])->name('clients.delete');
Route::get('/clients/search', [HomeController::class, 'search'])->name('clients.search');
