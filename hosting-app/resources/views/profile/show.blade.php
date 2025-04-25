@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Мой профиль</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Имя пользователя -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Имя</label>
                                <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                >
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Выбор подразделений -->
                            <div class="mb-4">
                                <label class="form-label">Мои подразделения</label>
                                <div class="row">
                                    @foreach($units as $unit)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="units[]"
                                                    value="{{ $unit->id }}"
                                                    id="unit{{ $unit->id }}"
                                                    {{ in_array($unit->id, $userUnits) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="unit{{ $unit->id }}">
                                                    {{ $unit->name }}
                                                    @if($unit->description)
                                                        <small class="text-muted d-block">{{ $unit->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('units')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
