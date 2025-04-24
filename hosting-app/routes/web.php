<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HomeController;

Auth::routes(['register' => false]); // Отключаем публичную регистрацию если не нужно

// Группа для публичных маршрутов
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

// Группа защищенных маршрутов
Route::middleware(['auth', 'verified'])->group(function () { // Добавляем проверку верификации email
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications');
    Route::get('/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::delete('/applications/{id}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::get('/applications/{id}/download', [ApplicationController::class, 'download'])->name('applications.download');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
});
