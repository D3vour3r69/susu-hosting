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

                    <!-- Блоки с параметрами -->
                    @foreach($features as $feature)
                        <div class="mb-4 border-bottom pb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-primary">{{ $feature->name }}</h4>
                                <small class="text-muted">Выберите один вариант</small>
                            </div>

                            @if($feature->description)
                                <p class="text-muted">{{ $feature->description }}</p>
                            @endif

                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                @foreach($feature->items as $item)
                                    <div class="col">
                                        <div class="card h-100 option-card">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           name="features[{{ $feature->id }}]"
                                                           value="{{ $item->id }}"
                                                           id="item_{{ $item->id }}"
                                                           required>
                                                    <label class="form-check-label fw-bold"
                                                           for="item_{{ $item->id }}">
                                                        {{ $item->name }}
                                                    </label>
                                                </div>
                                                @if($item->description)
                                                    <p class="text-muted mt-2 mb-0 small">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(auth()->user()->units->count() > 1)
                                <div class="mb-3">
                                    <label class="form-label">Выберите подразделение:</label>
                                    <select name="unit_id" class="form-select" required>
                                        @foreach(auth()->user()->units as $unit)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                                ({{ $unit->pivot->position }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="unit_id" value="{{ auth()->user()->units->first()->id }}">
                            @endif
                        </div>
                    @endforeach

                    <!-- Дополнительные поля -->
                    <div class="mt-4">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Комментарий</label>
                            <textarea class="form-control"
                                      id="notes"
                                      name="notes"
                                      rows="4"
                                      placeholder="Дополнительные примечания..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Статус записки</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Активная</option>
                                <option value="inactive">Черновик</option>
                                <option value="completed">Завершенная</option>
                            </select>
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
