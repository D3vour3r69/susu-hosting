@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-id-card"></i> Профиль пользователя
                </h4>
            </div>

            <div class="card-body">
                <!-- Основная информация -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user"></i> Личные данные
                        </h5>
                        <dl class="row">
                            <dt class="col-sm-3">ФИО:</dt>
                            <dd class="col-sm-9">{{ $user->name }}</dd>

                            <dt class="col-sm-3">Email:</dt>
                            <dd class="col-sm-9">{{ $user->email }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Блок подразделений -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-building"></i> Подразделения
                        </h5>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <p class="mb-0">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <!-- Список подразделений -->
                        @if($user->positions->isNotEmpty())
                            <div class="mb-4">
                                @foreach($user->positions as $position)
                                    <div class="card mb-2">
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">
                                                    {{ $position->unit->name }}
                                                    @if($position->unit->head_id === $user->id)
                                                        <span class="badge bg-warning ms-2">
                                                        <i class="fas fa-crown"></i> Руководитель
                                                    </span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ $position->name }}</small>
                                            </div>
                                            <form action="{{ route('profile.positions.destroy', $position) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Нет привязанных подразделений
                            </div>
                        @endif

                        <!-- Форма добавления с CSS-переключением -->
                        <!-- Форма добавления -->
                        <div class="card border-primary mt-3">
                            <div class="card-header bg-light">
                                <label class="cursor-pointer d-flex align-items-center gap-2 mb-0">
                                    Добавьте подразделение для пользователя

{{--                                    <span class="fs-6">--}}
{{--                                        <i class="fas fa-plus me-1"></i>Добавить подразделение--}}
{{--                                    </span>--}}
                                </label>
                            </div>
                            <!-- Скрытый контент -->
                            <div class="card-body" id="formContent">
                                <form action="{{ route('profile.positions.store') }}" method="POST">
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
                                            <input
                                                type="text"
                                                name="position_name"
                                                class="form-control"
                                                placeholder="Должность"
                                                required
                                            >
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input m-0"
                                                    id="isHead"
                                                    name="is_head"
                                                    value="1"
                                                    autocomplete="off"
                                                >
                                                <label class="form-check-label" for="isHead">
                                                    Руководитель
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save me-2"></i>Сохранить
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


