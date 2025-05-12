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
                    @include('applications._partials.filter')
                </div>

                @forelse($applications as $application)
                    @include('applications._partials.application_card')
                @empty
                    <div class="alert alert-info">Нет одобренных заявок</div>
                @endforelse

                {{ $applications->links() }}
            </div>
        </div>
    </div>
@endsection
