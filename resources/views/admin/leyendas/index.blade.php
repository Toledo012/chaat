@extends('layouts.admin')

@section('title', 'Leyenda del encabezado')
@section('header_title', 'Gestión de Formatos')
@section('header_subtitle', 'Leyenda anual del encabezado de los formatos')

@section('content')
<div class="container-fluid">

    <a href="{{ route('admin.formatos.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left me-1"></i> Volver a formatos
    </a>

    <div class="row g-4">

        {{-- FORMULARIO --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-heading text-primary fa-lg"></i>
                    <div>
                        <h5 class="mb-0">Agregar o editar leyenda</h5>
                        <small class="text-body-secondary">Si el año ya existe, se reemplaza su texto</small>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.leyendas.guardar') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="anio" class="form-label small fw-bold text-body-secondary">AÑO</label>
                            <input type="number" name="anio" id="anio" min="2000" max="2100"
                                   class="form-control @error('anio') is-invalid @enderror"
                                   value="{{ old('anio', now()->year) }}" required>
                            @error('anio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="texto" class="form-label small fw-bold text-body-secondary">LEYENDA</label>
                            <input type="text" name="texto" id="texto" maxlength="255"
                                   class="form-control @error('texto') is-invalid @enderror"
                                   value="{{ old('texto') }}" placeholder="{{ now()->year }}, Año de ..." required>
                            @error('texto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-body-secondary">Sin comillas: el formato las agrega.</small>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- LISTADO --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">Leyendas registradas</h5>
                    <small class="text-body-secondary">
                        Cada formato usa la de su año; si su año no tiene, la del año anterior más reciente.
                    </small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="90">Año</th>
                                <th>Así se ve en el encabezado</th>
                                <th width="90"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leyendas as $leyenda)
                                <tr>
                                    <td class="fw-semibold">{{ $leyenda->anio }}</td>
                                    <td><em>"{{ $leyenda->texto }}"</em></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-anio="{{ $leyenda->anio }}" data-texto="{{ $leyenda->texto }}"
                                                onclick="editarLeyenda(this)">
                                            <i class="fas fa-pen"></i> Editar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-body-secondary py-5">
                                        Aún no hay leyendas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function editarLeyenda(boton) {
        document.getElementById('anio').value = boton.dataset.anio;
        const texto = document.getElementById('texto');
        texto.value = boton.dataset.texto;
        texto.focus();
    }
</script>
@endsection
