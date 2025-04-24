@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card welcome-card">
                <div class="card-header">Добро пожаловать</div>
                <div class="card-body">
                    @guest
                        <h4 class="mb-4">Система электронных служебных записок</h4>
                        <p>Для работы в системе требуется авторизация</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Войти в систему</a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">Регистрация</a>
                            @endif
                        </div>
                    @else
                        <div class="text-center">
                            <h4>Добро пожаловать, {{ Auth::user()->name }}!</h4>
                            <p>Выберите действие в верхнем меню</p>
                            <a href="{{ route('applications.create') }}" class="btn btn-primary btn-lg mt-3">
                                Создать новую записку
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
@endsection
