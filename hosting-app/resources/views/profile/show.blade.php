@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-lg">
            <!-- Основная информация -->
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-id-card"></i> Профиль пользователя
                </h4>
            </div>

            <div class="card-body">
                <!-- Блок персональных данных -->
                <div class="row mb-5">
                    <div class="col-md-8">
                        <h5 class="border-bottom pb-2 mb-4">
                            <i class="fas fa-user"></i> Основная информация
                        </h5>
                        <dl class="row mt-3">
                            <dt class="col-sm-3">ФИО:</dt>
                            <dd class="col-sm-9 fs-5">{{ $user->name }}</dd>

                            <dt class="col-sm-3">Email:</dt>
                            <dd class="col-sm-9 fs-5">{{ $user->email }}</dd>

                            <!-- Блок обязательных подразделений -->
                            <dt class="col-sm-3">Подразделения:</dt>
                            <dd class="col-sm-9">
                                @php
                                    // Объединяем обычные подразделения и те, где пользователь руководитель
                                    $allUnits = $user->units->merge($user->managedUnits)->unique('id');
                                @endphp

                                @if($allUnits->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach($allUnits as $unit)
                                            <span class="badge bg-primary rounded-pill p-2">
                                                {{ $unit->name }}
                                                <span class="ms-2 badge bg-light text-dark">
                                                    @if($user->managedUnits->contains('id', $unit->id))
                                                        <i class="fas fa-crown me-1 text-warning"></i>Руководитель
                                                    @else
                                                        <i class="fas fa-user me-1"></i>
                                                        {{ $unit->pivot->position ?? 'Сотрудник' }}
                                                    @endif
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">Не назначен в подразделения</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Форма добавления нового подразделения -->
                <div class="card border-primary">
                    <div class="card-header bg-light">
                        Добавить новое подразделение
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.units.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <select class="form-select" name="unit_id" required>
                                        <option value="">Выберите подразделение</option>
                                        @foreach($availableUnits as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" name="role" required>
                                        <option value="member">Сотрудник</option>
                                        <option value="head">Руководитель</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Добавить
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
