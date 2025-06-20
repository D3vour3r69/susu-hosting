 <!DOCTYPE html>
<html lang="ru">
<head>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Служебные записки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        .navbar-brand { font-weight: 600; }
        .main-container { padding: 20px 0; }
        .welcome-card { margin-top: 50px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">

        <a class="navbar-brand" href="{{ route('home') }}">Служебные записки</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
                <ul class="navbar-nav me-auto">
                    @if(!Auth::user()->hasRole('admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('applications.create') ? 'active' : '' }}"
                               href="{{ route('applications.create') }}">Новая записка</a>
                        </li>
                    @endif
                    @if(Auth::user()->hasRole('admin'))

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('applications.index') ? 'active' : '' }}"
                               href="{{ route('applications.index') }}">Заявки на рассмотрение</a>
                        </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users"></i> Пользователи
                                </a>
                            </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('applications.index') ? 'active' : '' }}"
                               href="{{ route('applications.index') }}">Мои записки</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('applications.unit-index') ? 'active' : '' }}"
                           href="{{ route('applications.unit-index') }}">
                            @if(auth()->user()->hasRole('admin'))
                                <i class="fas fa-building me-2"></i>Заявки по подразделениям
                            @else
                                <i class="fas fa-building me-2"></i>Заявки по своему подразделению
                            @endif
                        </a>
                    </li>
                    @if(auth()->user()->hasRole('admin'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-tools"></i> Администрирование
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('features.index') }}">
                                    <i class="fas fa-cog"></i> Управление параметрами
                                </a>
                                <a class="dropdown-item" href="{{ route('applications.approved') }}">
                                    <i class="fas fa-check-circle"></i> Одобренные заявки
                                </a>
                            </div>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('profile.show') }}">Профиль</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Выход</button>
                            </form>
                        </div>
                    </li>
                </ul>
            @else
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Вход</a>
                    </li>
                    @if(Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                        </li>
                    @endif
                </ul>
            @endauth
        </div>
    </div>
</nav>

<main class="main-container">
    <div class="container">
        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
