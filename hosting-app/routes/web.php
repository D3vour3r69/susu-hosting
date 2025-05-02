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

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/units', [ProfileController::class, 'storeUnit'])->name('profile.units.store');
        Route::put('/units/{unit}', [ProfileController::class, 'updateUnit'])->name('profile.units.update');
        Route::delete('/units/{unit}', [ProfileController::class, 'destroyUnit'])->name('profile.units.destroy');
    });




    Route::resource('applications', ApplicationController::class)
        ->except(['show', 'edit', 'update']);


    Route::get('/applications/{application}/download', [ApplicationController::class, 'download'])
        ->name('applications.download');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/applications/unit', [ApplicationController::class, 'unitIndex'])
        ->name('applications.unit-index');
});

