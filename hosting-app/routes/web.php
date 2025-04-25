<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HomeController;

// Маршруты аутентификации
Auth::routes(['register' => true]); // Для отключения регистрации измените на false

// Главная страница
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('applications.index') // Исправлено имя маршрута
        : view('welcome');
})->name('home');

// Защищенные маршруты
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('applications', ApplicationController::class)
        ->except(['show']);


    Route::get('/applications/{application}/download', [ApplicationController::class, 'download'])
        ->name('applications.download');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/applications/by-unit', [UnitApplicationController::class, 'index'])
        ->name('applications.by-unit');
});

