<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ListViewController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Routes publik tanpa middleware
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/auth', [LoginController::class, 'login'])->name('auth');

// Routes yang butuh login (middleware checklogin)
Route::middleware(['checklogin'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/calculation', [HomeController::class, 'calculation'])->name('home.calculation');
    Route::post('/saveData', [HomeController::class, 'store'])->name('schedule.store');

    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/data_filter', [ScheduleController::class, 'dataFilter'])->name('schedule.filterdata');

    // Route::get('/view', [ListViewController::class, 'index'])->name('view');
    Route::get('/view', [ListViewController::class, 'index2'])->name('view');
    Route::get('/schedule/{item_code}', [ListViewController::class, 'getScheduleByItemCode'])->name('viewItem');

    Route::get('/api/items', [ListViewController::class, 'getItems']);
    Route::get('/api/items/{itemCode}/detail', [ListViewController::class, 'getItemDetail']);

});
