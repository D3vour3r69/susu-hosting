@extends('layouts.app')

@section('content')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-cogs"></i> Управление параметрами
                </h4>
            </div>

            <div class="card-body">
                <div class="mb-4">
                    <h5>Добавить новую категорию</h5>
                    <form action="{{ route('features.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="name" class="form-control"
                                       placeholder="Название категории" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="slug" class="form-control"
                                       placeholder="Уникальный идентификатор" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus"></i> Добавить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                @foreach($features as $feature)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $feature->name }}</h5>
                            <form action="{{ route('features.destroy', $feature) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('features.items.store', $feature) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" name="name" class="form-control"
                                               placeholder="Название параметра" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="slug" class="form-control"
                                               placeholder="Уникальный идентификатор(Slug)" required>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-plus"></i> Добавить вариант
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-3">
                                @foreach($feature->items as $item)
                                    <div class="badge bg-secondary me-2 mb-2 p-2">
                                        {{ $item->name }}
                                        <form action="{{ route('features.items.destroy', $item) }}"
                                              method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger ms-2">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
