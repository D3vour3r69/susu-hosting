@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Управление пользователями</h1>

        <!-- Поисковая форма -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                               placeholder="Поиск по имени или email..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Найти
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Таблица пользователей -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Просмотр
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Пользователи не найдены</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
@endsection
