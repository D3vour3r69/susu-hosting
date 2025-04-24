<?php

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
    // Ресурсный маршрут для ApplicationController
    Route::resource('applications', ApplicationController::class)
        ->except(['show']); // Убираем ненужный show

//    Route::get('/applications/my', [ApplicationController::class, 'myApplications'])
//        ->name('applications.my');

    // Дополнительные специфические маршруты
    Route::get('/applications/{application}/download', [ApplicationController::class, 'download'])
        ->name('applications.download');

    // Домашняя страница (если нужна)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/applications/by-unit', [UnitApplicationController::class, 'index'])
        ->name('applications.by-unit');
});

