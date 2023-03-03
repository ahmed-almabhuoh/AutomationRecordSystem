<?php

use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\BlocksController;
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

//Authentication Routes
Route::prefix('auto')->group(function () {
    Route::post('/login', [\App\Http\Controllers\api\AuthenticationController::class, 'login']);
});

Route::prefix('auto')->group(function () {
    Route::prefix('/')->group(function () {
        Route::resource('admins', AdminController::class);
    });

    Route::prefix('/')->group(function () {
        // Excel
        Route::get('/admin/excel/report', [AdminController::class, 'getReport']);
        Route::get('/admin/excel/report/{id}', [AdminController::class, 'getReportSpecificAdmin']);

        // Blocks
        Route::prefix('blocks')->group(function () {
            Route::get('/', [BlocksController::class, 'index']);

            // Admins
            Route::get('admins', [BlocksController::class, 'admins']);
            Route::post('admins/between', [BlocksController::class, 'blocksBetween']);
            Route::get('admins-status/{status?}', [BlocksController::class, 'blockAdminStatus']);
            Route::get('admins/{id}', [BlocksController::class, 'getAdminBlocks']);
            Route::get('admins/{id}/{status?}', [BlocksController::class, 'getAdminWithStatusBlocks']);
            Route::get('admins-search/{search?}', [BlocksController::class, 'searchForAdminBlocks']);
        });
    });
});

Route::post('test', [AdminController::class, 'test']);
