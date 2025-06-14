@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle"></i> Профиль пользователя: {{ $user->name }}
                    </h4>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Назад
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Основная информация -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Основная информация</h5>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">ФИО:</dt>
                                    <dd class="col-sm-8">{{ $user->name }}</dd>

                                    <dt class="col-sm-4">Email:</dt>
                                    <dd class="col-sm-8">{{ $user->email }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Подразделения -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-sitemap"></i> Подразделения</h5>
                            </div>
                            <div class="card-body">
                                @if($user->positions->isNotEmpty())
                                    <ul class="list-group list-group-flush">
                                        @foreach($user->positions as $position)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $position->unit->name }}</strong>
                                                    <div class="text-muted">{{ $position->name }}</div>
                                                </div>
                                                @if($position->unit->head_id === $user->id)
                                                    <span class="badge bg-warning">
                                            <i class="fas fa-crown"></i> Руководитель
                                        </span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        Пользователь не привязан к подразделениям
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Служебные записки -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Служебные записки</h5>
                    </div>
                    <div class="card-body">
                        @if($applications->isNotEmpty())
                            @foreach($applications as $application)
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                        <div>
                                    <span class="badge bg-{{ $application->status === 'active' ? 'success' : ($application->status === 'completed' ? 'secondary' : 'warning') }}">
                                        {{ $application->status_text }}
                                    </span>
                                            <small class="text-muted ms-2">
                                                {{ $application->created_at->format('d.m.Y H:i') }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('applications.download', $application) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <h6>Домен: {{ $application->domain }}</h6>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Руководитель:</strong></p>
                                                <p>{{ optional($application->head)->full_name ?? 'Не указан' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Ответственный:</strong></p>
                                                <p>{{ optional($application->responsible)->name ?? 'Не назначен' }}</p>
                                            </div>
                                        </div>

                                        @if($application->notes)
                                            <div class="mt-3">
                                                <p class="mb-1"><strong>Комментарий:</strong></p>
                                                <p class="text">{{ $application->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <!-- Пагинация -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $applications->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                У пользователя нет служебных записок
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
