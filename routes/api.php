<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// Route::middleware('guest')->group(function () {
//     Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
//     Route::post('/login', [LoginController::class, 'login']);
// });
// Route::get('/logout', function () {
//     session()->flush();
//     return redirect('/login');
// })->name('logout');
