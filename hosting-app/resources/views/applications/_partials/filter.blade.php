<div class="row mb-4">
    <div class="col-md-4">
        <form method="GET" class="input-group">
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">Все статусы</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Черновики</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершенные</option>
            </select>
            @if(request()->has('status'))
                <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
</div>
