
@if(auth()->user()->can('manage', $application))
    <div class="btn-group">
        @if(!$application->approved)
            <form action="{{ route('applications.approve', $application) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i> Одобрить
                </button>
            </form>
        @endif

        @if($application->status != 'inactive')
            <form action="{{ route('applications.reject', $application) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fas fa-times"></i> Отклонить
                </button>
            </form>
        @endif
    </div>
@endif
