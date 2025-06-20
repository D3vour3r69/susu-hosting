<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Маршруты аутентификации
Auth::routes(['register' => true]);

// Главная страница
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('applications.index')
        : view('welcome');
})->name('home');

// Защищенные маршруты
Route::middleware(['auth'])->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/profile/positions', [ProfileController::class, 'storePosition'])->name('profile.positions.store');
        Route::put('/positions/{position}', [ProfileController::class, 'updateHeadStatus'])->name('profile.positions.update');
        Route::delete('/positions/{position}', [ProfileController::class, 'destroyPosition'])->name('profile.positions.destroy');
        Route::get('/units/{unit}/positions', function (Unit $unit) {
            return $unit->positions;
        });
    });
    Route::resource('applications', ApplicationController::class)
        ->except(['show', 'edit', 'update']);

    Route::get('/applications/{application}/download', [ApplicationController::class, 'download'])
        ->name('applications.download');

    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/applications/unit', [ApplicationController::class, 'unitIndex'])
        ->name('applications.unit-index');

});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/users', [App\Http\Controllers\UserController::class, 'index'])
        ->name('admin.users.index');

    Route::get('/admin/users/{user}', [App\Http\Controllers\UserController::class, 'show'])
        ->name('admin.users.show');

    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');

    Route::post('/features', [FeatureController::class, 'store'])->name('features.store');

    Route::post('/features/{feature}/items', [FeatureController::class, 'storeItem'])
        ->name('features.items.store');

    Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])
        ->name('features.destroy');

    Route::delete('/features/items/{item}', [FeatureController::class, 'destroyItem'])
        ->name('features.items.destroy');

    Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve'])
        ->name('applications.approve');

    Route::post('/applications/{application}/reject', [ApplicationController::class, 'reject'])
        ->name('applications.reject');

    Route::get('/applications/approved', [ApplicationController::class, 'approved'])
        ->name('applications.approved');
});
