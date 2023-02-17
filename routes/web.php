<?php

use App\Http\Controllers\backend\DashboardController;
use App\Http\Controllers\ManagerController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('auto')->group(function () {
    Route::resource('managers', ManagerController::class);
});

Route::prefix('auto')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/manager/excel/report', [ManagerController::class, 'getReport'])->name('managers.report.xlsx');
    Route::get('/manager/excel/report/{id}', [ManagerController::class, 'getReportSpecificManager'])->name('manager.report.xlsx');
});

