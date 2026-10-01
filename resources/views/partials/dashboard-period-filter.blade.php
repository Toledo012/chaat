<form method="GET" action="{{ $action }}" class="dashboard-period-filter">
    <div>
        <label for="dashboardYear" class="form-label small fw-semibold mb-1">Año</label>
        <select id="dashboardYear" name="anio" class="form-select form-select-sm">
            @foreach(range(now()->year, $yearMin) as $optionYear)
                <option value="{{ $optionYear }}" @selected($year === $optionYear)>{{ $optionYear }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="dashboardMonth" class="form-label small fw-semibold mb-1">Mes para detalle</label>
        <select id="dashboardMonth" name="mes" class="form-select form-select-sm">
            <option value="0" @selected($month === 0)>Todo el año</option>
            @foreach($months as $number => $name)
                <option value="{{ $number }}" @selected($month === $number)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary btn-sm">Aplicar</button>
</form>
