<?php

use App\Http\Controllers\api\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auto')->group(function () {
    Route::prefix('/')->group(function () {
        Route::resource('admins', AdminController::class);
    });

    Route::prefix('/')->group(function () {
        // Excel
        Route::get('/admin/excel/report', [AdminController::class, 'getReport']);
        Route::get('/admin/excel/report/{id}', [AdminController::class, 'getReportSpecificAdmin']);
    });
});

Route::post('test', [AdminController::class, 'test']);
