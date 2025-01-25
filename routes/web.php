<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\updateController;
use App\Http\Controllers\DashboardgisController;
use App\Http\Controllers\DashboardgisAfdelingController;
use App\Http\Controllers\DashboardgisCompanyController;
use App\Http\Controllers\DashboardgisregionalController;
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

Route::get('/convert', [updateController::class, 'conver'])->name('convert');
Route::get('/updategeo', [updateController::class, 'updategeo'])->name('updategeo');
Route::post('/geoupdate', [updateController::class, 'geoupdate'])->name('geoupdate');
Route::post('/formatjson', [updateController::class, 'formatjson'])->name('formatjson');


Route::get('/dashboardgis', [DashboardgisController::class, 'index'])->name('dashboard.gis');
Route::get('/gis/plots', [DashboardgisController::class, 'getPlots'])->name('gis.getPlots');
Route::post('/gis/save-plots', [DashboardgisController::class, 'savePlots'])->name('gis.savePlots');


Route::get('/dashboardgisafdeling', [DashboardgisAfdelingController::class, 'index'])->name('dashboard.gisafdeling');
Route::get('/gis/plotsafdeling', [DashboardgisAfdelingController::class, 'getPlots'])->name('gis.getPlotsafdeling');
Route::post('/gis/save-plotsafdeling', [DashboardgisAfdelingController::class, 'savePlots'])->name('gis.savePlotsafdeling');

Route::get('/dashboardgiscompany', [DashboardgisCompanyController::class, 'index'])->name('dashboard.giscompany');
Route::get('/gis/plotscompany', [DashboardgisCompanyController::class, 'getPlots'])->name('gis.getPlotscompany');
Route::post('/gis/save-plotscompany', [DashboardgisCompanyController::class, 'savePlots'])->name('gis.savePlotscompany');

Route::get('/dashboardgisregional', [DashboardgisregionalController::class, 'index'])->name('dashboard.gisregional');
Route::get('/gis/plotsregional', [DashboardgisregionalController::class, 'getPlots'])->name('gis.getPlotsregional');
Route::post('/gis/save-plotsregional', [DashboardgisregionalController::class, 'savePlots'])->name('gis.savePlotsregional');
