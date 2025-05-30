<form method="GET" action="{{ url()->current() }}" class="mb-3">
    <div class="input-group">
        <input type="text" name="domain" value="{{ request('domain') }}" class="form-control" placeholder="Фильтр по домену">
        <button class="btn btn-outline-primary" type="submit">
            <i class="fas fa-filter"></i> Фильтровать
        </button>
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Сбросить</a>
    </div>
</form>
