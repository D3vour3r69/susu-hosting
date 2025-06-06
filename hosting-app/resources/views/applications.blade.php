@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Мои заявки</h1>

        @if($applications->isEmpty())
            <div class="alert alert-info">У вас нет активных заявок</div>
        @else
            <div class="list-group">
                @foreach($applications as $app)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <h5>Заявка #{{ $app->id }}</h5>
                            <small>{{ $app->created_at->format('d.m.Y H:i') }}</small>
                        </div>

                        <div class="mb-2">
                            @foreach($app->featureItems as $item)
                                <span class="badge bg-primary">
                                {{ $item->feature->name }}: {{ $item->name }}
                            </span>
                            @endforeach
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge bg-{{ [
                                'active' => 'success',
                                'inactive' => 'warning',
                                'completed' => 'secondary'
                            ][$app->status] }}">
                                {{ $app->status_text }}
                            </span>

                            <form action="{{ route('applications.destroy', $app->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </form>
                        </div>

                        @if($app->notes)
                            <div class="alert alert-secondary">
                                {{ $app->notes }}
                            </div>
                        @endif

                        <a href="{{ route('applications.download', $app->id) }}"
                           class="btn btn-sm btn-outline-success">
                            Скачать
                        </a>

                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
