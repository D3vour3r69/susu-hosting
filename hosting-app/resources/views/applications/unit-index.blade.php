@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href={{asset('css/select2-custom.css')}} rel="stylesheet" />
@endpush

@section('content')
    <div class="container">
        @if(auth()->user()->hasRole('admin'))
            <h1 class="mb-4">Служебные записки по подразделениям</h1>
        @else
            <h1 class="mb-4">Служебные записки по подразделению</h1>
        @endif
        @if(auth()->user()->hasRole('admin'))
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('applications.unit-index') }}" id="unit-filter-form">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <select name="unit_id" class="form-select select2-unit" id="unit-select">
                                <option value="">Все подразделения</option>
                                @foreach($units->sortBy('name') as $unit)
                                    <option value="{{ $unit->id }}"
                                            {{ $selectedUnitId == $unit->id ? 'selected' : '' }}
                                            data-head="{{ $unit->head ? $unit->head->name : '' }}"
                                            data-unit-name="{{ $unit->name }}">
                                        {{ $unit->name }}
                                        @if($unit->head)
                                            (Руководитель: {{ $unit->head->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
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
                    <div class="d-flex justify-content-center mt-4">
                        {{ $applications->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/select2-init.js') }}"></script>
@endpush
