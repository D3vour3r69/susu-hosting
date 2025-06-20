@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="container">
        <h1 class="mb-4">Служебные записки по подразделениям</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('applications.unit-index') }}" id="unit-filter-form">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-10">
                            <select name="unit_id" class="form-select select2-unit" id="unit-select">
                                <option value="">Все подразделения</option>
                                @foreach($units->sortBy('name') as $unit)
                                    <option value="{{ $unit->id }}"
                                            {{ $selectedUnitId == $unit->id ? 'selected' : '' }}
                                            data-head="{{ $unit->head ? $unit->head->name : '' }}">
                                        {{ $unit->name }}
                                        @if($unit->head)
                                            (Руководитель: {{ $unit->head->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('applications.unit-index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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
    <script>
        $(document).ready(function() {
            $('#unit-select').select2({
                theme: "bootstrap-5",
                placeholder: "Выберите подразделение",
                allowClear: true,
                width: '100%',
                templateResult: function(unit) {
                    if (!unit.id) return unit.text;

                    const head = $(unit.element).data('head');
                    return $(
                        `<div>
                        <div>${unit.text}</div>
                        ${head ? `<small class="text-muted">Руководитель: ${head}</small>` : ''}
                    </div>`
                    );
                }
            });

            $('#unit-select').on('change', function() {
                $('#unit-filter-form').submit();
            });
        });
    </script>

    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 5px;
        }
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #ced4da;
        }
        .select2-results__option {
            padding: 8px 12px;
        }
    </style>
@endpush
