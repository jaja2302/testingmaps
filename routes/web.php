<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\updateController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DashboardController::class, 'home'])->name('dashboard');
Route::get('/drawmaps', [DashboardController::class, 'drawmaps'])->name('drawmaps');
Route::get('/update', [updateController::class, 'index'])->name('uploadjson');
Route::post('/uploaddata', [updateController::class, 'uploaddata'])->name('uploaddata');
