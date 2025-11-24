@extends('layouts.admin')

@section('title', 'Materiales')
@section('header_title', 'Catálogo de Materiales')
@section('header_subtitle', 'Administración de insumos')

@section('styles')
<style>
    .card-modern {
        background: #1e1f22;
        border-radius: 14px;
        border: 1px solid #2b2d31;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        animation: fadeIn .4s ease-out;
    }

    .btn-sem {
        background: #399e91 !important;
        border: none;
        color: white !important;
        font-weight: 600;
        transition: .2s;
    }

    .btn-sem:hover {
        opacity: .9;
        transform: translateY(-2px);
    }

    .table-dark-custom {
        background: #2b2d31;
        color: #dcdcdc;
    }

    .table-dark-custom thead {
        background: #232428;
        color: #bfbfbf;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .table-dark-custom tbody tr:hover {
        background: #33353a;
    }

    .titulo-materiales {
        font-weight: 700;
        color: white;
        transition: .3s;
    }

    /*  Color turquesa SOLO en modo oscuro */
    .dark-mode .titulo-materiales,
    .dark-mode .titulo-materiales i {
        color: #399e91 !important;
    }

    @keyframes fadeIn {
        0% {opacity: 0; transform: translateY(10px);}
        100% {opacity: 1; transform: translateY(0);}
    }
</style>
@endsection

@section('content')

<div class="card card-modern p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0 titulo-materiales">
            <i class="fa-solid fa-box"></i> Materiales
        </h4>
        <div>
            <button class="btn btn-sem" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fa-solid fa-plus"></i> Añadir Material
            </button>
        </div>
    </div>
</div>

<div class="card card-modern p-0">
    <table class="table table-dark-custom table-hover mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Unidad</th>
                <th style="width: 140px;">Acciones</th>
            </tr>
        </thead>

        <tbody>
        @foreach($materiales as $m)
            <tr>
                <td>{{ $m->id_material }}</td>
                <td>{{ $m->nombre }}</td>
                <td>{{ $m->unidad_sugerida }}</td>
                <td>
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar{{ $m->id_material }}">
                        <i class="fa-solid fa-pen"></i>
                    </button>

                    <form action="{{ route('admin.materiales.destroy', $m->id_material) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar material?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            {{-- MODAL EDITAR --}}
            <div class="modal fade" id="modalEditar{{ $m->id_material }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background:#1e1f22; color:white;">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Material</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <form action="{{ route('admin.materiales.update', $m->id_material) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" value="{{ $m->nombre }}" class="form-control">

                                <label class="form-label mt-3">Unidad sugerida</label>
                                <input type="text" name="unidad_sugerida" value="{{ $m->unidad_sugerida }}" class="form-control">
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-sem">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endforeach
        </tbody>
    </table>
</div>
{{-- MODAL CREAR --}}
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1e1f22; color:white;">
            <div class="modal-header">
                <h5 class="modal-title">Añadir Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.materiales.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control">

                    <label class="form-label mt-3">Unidad sugerida</label>

                    <select name="unidad_sugerida" id="unidadSelect" class="form-control">
                        <option value="">Seleccionar…</option>
                        <option value="pieza">Pieza</option>
                        <option value="metro">Metro</option>
                        <option value="litro">Litro</option>
                        <option value="rollo">Rollo</option>
                        <option value="caja">Caja</option>
                        <option value="otro">Otro…</option>
                    </select>

                    {{-- CAMPO OCULTO PARA "OTRO" --}}
                    <input 
                        type="text" 
                        name="unidad_otro" 
                        id="unidadOtro" 
                        class="form-control mt-3" 
                        placeholder="Escribe la unidad" 
                        style="display: none;"
                    >

                </div>

                <div class="modal-footer">
                    <button class="btn btn-sem">Guardar Material</button>
                </div>      
            </form>
        </div>
    </div>
</div>


<script>
/* Mostrar campo "otro" */
document.getElementById('unidadSelect').addEventListener('change', function() {
    let otro = document.getElementById('unidadOtro');

    if (this.value === 'otro') {
        otro.style.display = 'block';
        otro.required = true;
    } else {
        otro.style.display = 'none';
        otro.required = false;
        otro.value = '';
    }
});

</script>

@endsection
