@extends('layouts.app')

@section('content')
    <div class="container">
        @if (auth()->user()->hasRole('admin'))
            <h1 class="mb-4">Записки для рассмотрения</h1>
        @else
            <h1 class="mb-4">Мои служебные записки</h1>
        @endif

        <form method="GET" class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="domain" value="{{ old('domain', $domain) }}" class="form-control"
                           placeholder="Фильтр по домену">
                </div>

                <div class="col-auto">
                    <select name="status" class="form-select">
                        <option value="">Все статусы</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Активные</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Завершённые</option>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Применить</button>
                    <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">Сбросить</a>
                </div>
            </div>
        </form>

        @if (!auth()->user()->hasRole('admin'))
            <a href="{{ route('applications.create') }}" class="btn btn-primary mb-4">
                <i class="fas fa-plus-circle"></i> Создать новую
            </a>
        @endif

        @forelse($applications as $application)
            <div class="card mb-4 shadow-sm" id="application-row-{{ $application->id }}">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <span class="badge bg-{{ $application->status === 'active' ? 'success' : ($application->status === 'completed' ? 'secondary' : 'warning') }}">
                            {{ $application->status }}
                        </span>
                        <small class="text-muted ms-2">
                            {{ $application->created_at->format('d.m.Y H:i') }}
                        </small>
                    </div>
                    <div class="btn-group">
                        @if(auth()->user()->hasRole('admin'))
                            @if($application->status === 'active')
                                <form action="{{ route('applications.approve', $application) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Одобрить"
                                            id="approve-button-{{ $application->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            @if(!($application->status === 'inactive'))
                                <form action="{{ route('applications.reject', $application) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Отклонить"
                                            id="reject-button-{{ $application->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        @endif

                        <a href="{{ route('applications.download', $application) }}"
                           id="download-button-{{ $application->id }}"
                           class="btn btn-sm btn-outline-primary" title="Скачать">
                            <i class="fas fa-download"></i>
                        </a>

                        <form action="{{ route('applications.destroy', $application) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить"
                                    id="delete-button-{{ $application->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="card-title">Заявка #{{ $application->id }} - Выбранные параметры:</h5>
                    <div class="row">
                        @foreach($application->featureItems->groupBy('feature_id') as $group)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light py-2">
                                        <strong>{{ $group->first()->feature->name }}</strong>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach($group as $item)
                                            <li class="list-group-item">
                                                {{ $item->name }}
                                                @if($item->description)
                                                    <small
                                                        class="text-muted d-block mt-1">{{ $item->description }}</small>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <h5>Кем написано заявление:</h5>
                        <div class="border p-3 rounded bg-light">
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.users.show', $application->user) }}">
                                    {{ $application->user->name }}
                                </a>
                            @else
                                {{ $application->user->name }}
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>На кого написано заявление:</h5>
                        <div class="border p-3 rounded bg-light">
                            {{ optional($application->head)->full_name ?? 'Начальник не выбран' }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Домен будущего сайта:</h5>
                        <div class="border p-3 rounded bg-light">
                            {{ $application->domain }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Ответственный за сайт:</h5>
                        <div class="border p-3 rounded bg-light">
                            {{ $application->responsible->name ?? 'Не назначен' }}
                        </div>
                    </div>

                    @if($application->notes)
                        <div class="mt-4">
                            <h5>Комментарий:</h5>
                            <div class="border p-3 rounded bg-light">
                                {{ $application->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                У вас пока нет созданных записок
            </div>
        @endforelse
        <div class="d-flex justify-content-center mt-4">
            {{ $applications->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
