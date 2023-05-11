<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\BillController;
use Illuminate\Support\Facades\Auth;
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
// Auth::routes();
// Route::prefix('/api/v1/')->group(function(){
//     Route::resource('/bill',BillController::class);
// });

// Route::get('/', function () {
//     return redirect('/api/v1/bill');
// });

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');