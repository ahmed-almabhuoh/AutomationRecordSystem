<?php

use App\Events\CreatingBlockManagerEvent;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\backend\DashboardController;
use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

Route::prefix('/')->middleware(['auth:manager', 'activation'])->group(function () {
    Route::prefix('auto')->group(function () {
        Route::resource('managers', ManagerController::class);
    });
    
    Route::prefix('auto')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    
        Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout')->withoutMiddleware('activation');

        Route::get('/manager/excel/report', [ManagerController::class, 'getReport'])->name('managers.report.xlsx');
        Route::get('/manager/excel/report/{id}', [ManagerController::class, 'getReportSpecificManager'])->name('manager.report.xlsx');
    });
});

Route::prefix('auto')->middleware(['guest:manager'])->group(function () {
    Route::get('{guard}/login', [AuthenticationController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthenticationController::class, 'login']);
});

// Route::get('/', function () {
//     event(new CreatingBlockManagerEvent(['name' => 'Ahmad ']));
// });