@extends('layouts.app')

@section('content')
    <div class="container">
        @if (auth()->user()->hasRole('admin'))
            <h1 class="mb-4">Записки для рассмотрения</h1>
        @else
            <h1 class="mb-4">Мои служебные записки</h1>
        @endif
            @if (!auth()->user()->hasRole('admin'))
                <a href="{{ route('applications.create') }}" class="btn btn-primary mb-4">
                    <i class="fas fa-plus-circle"></i> Создать новую
                </a>
            @endif
        @forelse($applications as $application)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Заявка #{{ $application->id }}</h5>
                        <div class="btn-group">
                            @if(auth()->user()->hasRole('admin'))
                                <form action="{{ route('applications.approve', $application) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Одобрить
                                    </button>
                                </form>
                                <form action="{{ route('applications.reject', $application) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="fas fa-times"></i> Отклонить
                                    </button>
                                </form>
                            @endif

                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal-{{ $application->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Модальное окно удаления -->
                    <div class="modal fade" id="deleteModal-{{ $application->id }}">
                        @foreach($applications as $application)
                            <div class="modal fade" id="deleteModal-{{ $application->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Подтверждение удаления</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Вы уверены, что хотите удалить заявку #{{ $application->id }}?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                            <form action="{{ route('applications.destroy', $application) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Удалить</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card mb-4 shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div>

                    <span class="badge bg-{{ $application->status === 'active' ? 'success' : ($application->status === 'completed' ? 'secondary' : 'warning') }}">
                        {{ $application->status }}
                    </span>
                        <small class="text-muted ms-2">
                            {{ $application->created_at->format('d.m.Y H:i') }}
                        </small>
                    </div>
                    <div>
                        <a href="{{ route('applications.download', $application) }}"

                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i>
                        </a>
                        <form action="{{ route('applications.destroy', $application) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="card-title">Выбранные параметры:</h5>
                    <div class="row">
                        @foreach($application->featureItems->groupBy('feature_id') as $group)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light py-2">
                                        <strong>{{ $group->first()->feature->name }}</strong>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach($group as $item)
                                            <li class="list-group-item">
                                                {{ $item->name }}
                                                @if($item->description)
                                                    <small class="text-muted d-block mt-1">{{ $item->description }}</small>
                                                @endif

                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <h5>Домен будущего сайта:</h5>
                        <div class="border p-3 rounded bg-light">
                            {{ $application->domain }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Ответственный за сайт:</h5>
                        <div class="border p-3 rounded bg-light">
                            {{ $application->responsible->name ?? 'Не назначен' }}
                        </div>
                    </div>

                    @if($application->notes)
                        <div class="mt-4">
                            <h5>Комментарий:</h5>
                            <div class="border p-3 rounded bg-light">
                                {{ $application->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                У вас пока нет созданных записок
            </div>
        @endforelse
    </div>
@endsection
