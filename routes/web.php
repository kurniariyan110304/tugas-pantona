<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login-process', [AuthController::class, 'loginProcess'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('login.check')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
});