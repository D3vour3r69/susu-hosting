@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-file-alt me-2"></i>Новая служебная записка</h3>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                        <div class="mt-2">
                            <a href="{{ route('applications.download', session('application_id')) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i> Скачать записку
                            </a>
                            <a href="{{ route('applications.index') }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-list me-1"></i> К списку записок
                            </a>
                        </div>
                    </div>
                @endif

                <form action="{{ route('applications.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="head_select">Выберите главу</label>
                        <select class="form-select" id="head_select" name="head_id" required>
                            <option value="">-- Выберите --</option>
                            @foreach($heads as $head)
                                <option value="{{ $head->id }}" {{ old('head_id') == $head->id ? 'selected' : '' }}>
                                    {{ $head->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('head_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Блоки с параметрами -->
                    <div class="mb-4">
                        <label for="features_select" class="form-label">Выберите параметры</label>
                        <select id="features_select" name="features[]" class="form-select" multiple="multiple" required style="width: 100%;">
                            @foreach($features as $feature)
                                <optgroup label="{{ $feature->name }}">
                                    @foreach($feature->items as $item)
                                        <option value="{{ $item->id }}" title="{{ $item->description ?? '' }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Выберите один или несколько вариантов из списка</small>
                    </div>
                    <div class="mb-3">
                        <label for="domain" class="form-label">Домен будущего сайта</label>
                        <input type="text" class="form-control" id="domain" name="domain" placeholder="example.com" required>
                    </div>
                    <!-- Дополнительные поля -->
                    <div class="mb-3">
                        @if($userUnits = auth()->user()->positions->pluck('unit')->unique())
                            @if($userUnits->count() > 1)
                                <div class="mb-3">
                                    <label class="form-label">Выберите подразделение:</label>
                                    <select name="unit_id" class="form-select" required>
                                        @foreach($userUnits as $unit)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="unit_id" value="{{$userUnits->first()->id }}">
                            @endif
                        @endif
                            <div class="mb-3">
                                <label for="responsible_id" class="form-label">Ответственный за сайт</label>
                                <select name="responsible_id" id="responsible_id" class="form-select" required>
                                    @foreach($responsibles as $responsible)
                                        <option value="{{ $responsible->id }}">{{ $responsible->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Опишите для чего требуется хостинг</label>
                            <textarea class="form-control"
                                      id="notes"
                                      name="notes"
                                      rows="4"
                                      placeholder="Дополнительные примечания..."></textarea>
                        </div>

                        <div class="mb-4">
                            @if(auth()->user()->hasRole('admin'))
                                <label class="form-label">Статус записки</label>
                                <select name="status" class="form-select">
                                    <option value="active">Активная</option>
                                    <option value="inactive">Черновик</option>
                                    <option value="completed">Завершенная</option>
                                </select>
                            @else
                                <input type="hidden" name="status" value="active">
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('applications.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Создать записку
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .option-card {
                transition: all 0.2s ease;
                border: 2px solid transparent;
            }

            .option-card:hover {
                transform: translateY(-3px);
                border-color: #0d6efd;
                cursor: pointer;
            }

            .form-check-input:checked + .form-check-label {
                color: #0d6efd;
            }

            [type="radio"]:checked {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }

            .card-header {
                border-radius: 0.5rem 0.5rem 0 0;
            }
        </style>
    @endpush

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#features_select').select2({
                placeholder: "Выберите параметры",
                allowClear: true,
                width: '100%',
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var title = $(data.element).attr('title');
                    var text = data.text;
                    return $('<span></span>').attr('title', title).text(text);
                }
            });
        });
    </script>
@endpush
