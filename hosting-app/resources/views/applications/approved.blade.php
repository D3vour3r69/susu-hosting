@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-check-circle"></i> Одобренные заявки
                </h4>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    @include('partials.domain_filter')
                </div>

                @forelse($applications as $application)
                        @if($application->status === 'completed')
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Заявка #{{ $application->id }}</h5>
                                    <span class="badge bg-success">Одобрена</span>
                                </div>
                                <p><strong>Начальник:</strong> {{ $application->user->name }}</p>
                                <p><strong>Домен:</strong> {{ $application->domain }}</p>
                                <p><strong>Ответственный:</strong> {{ $application->responsible->name ?? 'Не назначен' }}</p>
                                <p><strong>Статус:</strong> {{ ucfirst($application->status) }}</p>
                                <p><strong>Дата создания:</strong> {{ $application->created_at->format('d.m.Y H:i') }}</p>

                                @if($application->notes)
                                    <div class="mt-3">
                                        <h6>Пояснения:</h6>
                                        <div class="border p-3 rounded bg-light">
                                            {{ $application->notes }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                       @endif
                @empty
                    <div class="alert alert-info">Нет одобренных заявок</div>
                @endforelse

                <div class="d-flex justify-content-center mt-4">
                    {{ $applications->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
