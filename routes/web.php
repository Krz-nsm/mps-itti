<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ListViewController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/calculation', [HomeController::class, 'calculation'])->name('home.calculation');
Route::POST('/saveData', [HomeController::class, 'store'])->name('schedule.store');


Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
Route::get('/data_filter', [ScheduleController::class, 'dataFilter'])->name('schedule.filterdata');

Route::get('/view', [ListViewController::class, 'index'])->name('view');
Route::get('/schedule/{item_code}', [ListViewController::class, 'getScheduleByItemCode'])->name('viewItem');



// Route::middleware(['auth'])->group(function () {

    // Route::prefix('schedule')->group(function () {
    //     Route::resource('/', [ScheduleController::class])->names([
    //         'index' => 'schedule.index',
    //     ]);

     // Route::resource('/group-shift', App\Http\Controllers\APPS\PAYROLL\SHIFT\GroupShiftController::class)->names([
        //     'index' => 'payroll.admin.shift.group-shift.index',
        //     'store' => 'payroll.admin.shift.group-shift.store',
        //     'update' => 'payroll.admin.shift.group-shift.update',
        //     'destroy' => 'payroll.admin.shift.group-shift.destroy',
        // ]);

        // Route::get('approve_bon/data',[App\Http\Controllers\APP\ApprovalDocument\ApproveBonOrder\ListApproveController::class,'tabledata'])
        //     ->name('approvaldocument.approve_bon.index.data');

        // Route::post('approve_bon/recipes', [App\Http\Controllers\APP\ApprovalDocument\ApproveBonOrder\ListApproveController::class, 'approve'])
        //     ->name('approvaldocument.approve_bon.index.approve');

        // Route::post('approve_bon/recipes/reject', [App\Http\Controllers\APP\ApprovalDocument\ApproveBonOrder\ListApproveController::class, 'reject'])
        //     ->name('approvaldocument.approve_bon.index.reject');

        // Route::post('approve_bon/recipes/delete', [App\Http\Controllers\APP\ApprovalDocument\ApproveBonOrder\ListApproveController::class, 'delete'])
        //     ->name('approvaldocument.approve_bon.index.delete');

        // Route::get('approve_bon/dataModal',[App\Http\Controllers\APP\ApprovalDocument\ApproveBonOrder\ListApproveController::class,'dataModal'])
        //     ->name('approvaldocument.approve_bon.index.dataModal');
        // });
