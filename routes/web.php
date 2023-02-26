<?php

use App\Events\CreatingBlockManagerEvent;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\backend\DashboardController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\KeeperController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentParentController;
use App\Http\Controllers\SupervisorController;
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

Route::prefix('/')->middleware(['auth:manager,admin,supervisor,keeper', 'activation', 'soft_deleted'])
    ->group(function () {
        Route::prefix('auto')->group(function () {
            Route::resource('managers', ManagerController::class);
            Route::resource('admins', AdminController::class);
            Route::resource('supervisors', SupervisorController::class);
            Route::resource('keepers', KeeperController::class);
            Route::resource('student_parents', StudentParentController::class);
            Route::resource('students', StudentController::class);

            Route::resource('branches', BranchController::class);
        });

        Route::prefix('auto')->group(function () {
            Route::get('/', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

            // Excel Routes
            Route::get('/manager/excel/report', [ManagerController::class, 'getReport'])->name('managers.report.xlsx');
            Route::get('/manager/excel/report/{id}', [ManagerController::class, 'getReportSpecificManager'])->name('manager.report.xlsx');
            Route::get('/branch/excel/report', [BranchController::class, 'getReport'])->name('branches.report.xlsx');
            Route::get('/branch/excel/report/{id}', [BranchController::class, 'getReportSpecificBranch'])->name('branch.report.xlsx');
            Route::get('/admin/excel/report', [AdminController::class, 'getReport'])->name('admins.report.xlsx');
            Route::get('/admin/excel/report/{id}', [AdminController::class, 'getReportSpecificAdmin'])->name('admin.report.xlsx');
            Route::get('/supervisor/excel/report', [SupervisorController::class, 'getReport'])->name('supervisors.report.xlsx');
            Route::get('/supervisor/excel/report/{id}', [SupervisorController::class, 'getReportSpecificSupervisor'])->name('supervisor.report.xlsx');
            Route::get('/keeper/excel/report', [KeeperController::class, 'getReport'])->name('keepers.report.xlsx');
            Route::get('/keeper/excel/report/{id}', [KeeperController::class, 'getReportSpecificKeeper'])->name('keeper.report.xlsx');
            Route::get('/student_parent/excel/report', [StudentParentController::class, 'getReport'])->name('student_parents.report.xlsx');
            Route::get('/student_parent/excel/report/{id}', [StudentParentController::class, 'getReportSpecificStudentParent'])->name('student_parent.report.xlsx');
            Route::get('/students/excel/report', [StudentController::class, 'getReport'])->name('students.report.xlsx');
            Route::get('/students/excel/report/{id}', [StudentController::class, 'getReportSpecificStudent'])->name('student.report.xlsx');

            // Block Routes
            Route::get('/blockes/{blocked_id}/{guard?}', [BlockController::class, 'show'])->name('user.blocks');
            Route::post('block/{blocked_id}/{guard}', [BlockController::class, 'store'])->name('blocks.store');

            Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout')->withoutMiddleware(['activation', 'soft_deleted']);
        });
    });

Route::prefix('auto')->middleware(['guest:manager,admin,supervisor,keeper'])->group(function () {
    Route::get('{guard}/login', [AuthenticationController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthenticationController::class, 'login']);
});
