<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
// Временный маршрут для автоматической авторизации тестового пользователя
Route::get('/login-test-user', function () {
    $user = User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password')
        ]
    );
    auth()->login($user);
    return redirect('/applications/create');
});
Route::get('/applications/{id}/download', [ApplicationController::class, 'download'])
    ->name('applications.download');
