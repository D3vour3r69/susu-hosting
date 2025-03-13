@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Создать служебную записку</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('applications.store') }}" method="POST">
            @csrf

            <!-- Выбор параметров -->
            @foreach($features as $feature)
                <div class="mb-4">
                    <h4>{{ $feature->name }}</h4>
                    <p>{{ $feature->description }}</p>

                    @foreach($feature->items as $item)
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="feature_items[]"
                                value="{{ $item->id }}"
                                id="item_{{ $item->id }}"
                            >
                            <label class="form-check-label" for="item_{{ $item->id }}">
                                {{ $item->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <!-- Поле для заметок -->
            <div class="mb-3">
                <label for="notes" class="form-label">Дополнительные заметки</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>
@endsection
