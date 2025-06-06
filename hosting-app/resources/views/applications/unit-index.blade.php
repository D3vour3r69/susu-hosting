@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Служебные записки по подразделениям</h1>

        <!-- Форма фильтрации -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('applications.unit-index') }}">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <select name="unit_id" class="form-select">
                                <option value="">Все подразделения</option>
                                @foreach($units as $unit)
                                    <option
                                        value="{{ $unit->id }}"
                                        {{ $selectedUnitId == $unit->id ? 'selected' : '' }}
                                    >
                                        {{ $unit->name }}
                                        @if($unit->head)
                                            (Руководитель: {{ $unit->head->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Фильтровать
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Список записок -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if($applications->isEmpty())
                    <div class="alert alert-info">Нет записок для отображения</div>
                @else
                    <div class="list-group">
                        @foreach($applications as $app)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">Служебная записка #{{ $app->id }}</h5>
                                        <p class="mb-1">{{ Str::limit($app->notes, 100) }}</p>
                                        <small class="text">
                                            Автор: {{ $app->user->name }} |
                                            Статус: <span class="badge bg-{{ $app->status === 'active' ? 'success' : ($app->status === 'completed' ? 'secondary' : 'warning') }}">{{ $app->status }}</span> |
                                            Подразделение:
                                            @if($app->unit)
                                                <span class="badge bg-primary">{{ $app->unit->name }}</span>
                                            @else
                                                <span class="text-danger">Не указано</span>
                                            @endif
                                            @if($app->unit && $app->unit->head_id === $app->user_id)

                                                <span class="badge bg-warning">
                                                    <i class="fas fa-crown me-1"></i>Руководитель подразделения
                                                </span>

                                            @endif
                                            Дата создания: {{ $app->created_at }}
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('applications.download', $app) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @can('delete', $app)
                                            <form action="{{ route('applications.destroy', $app) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
