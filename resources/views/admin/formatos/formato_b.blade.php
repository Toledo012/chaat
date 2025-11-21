@extends('layouts.admin')

@section('title', 'Formato B - Equipos e Impresoras')
@section('header_title', 'Formato B - Equipos / Impresoras')
@section('header_subtitle', 'Registro de mantenimiento de equipos e impresoras')

@section('styles')
    <style>
        .card-header { background-color: #399e91; color: white; font-weight: 600; }
        .form-control, .form-select { border-radius: 8px; }
        .btn-primary { background-color: #399e91; border-color: #399e91; }
        .btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
        .alert-info { background-color: #d1f0eb; border-color: #399e91; color: #25685d; font-weight: 500; }
    </style>
@endsection

@section('content')

    <div class="alert alert-info mb-4 d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        Todos los campos marcados deben completarse antes de guardar.
    </div>

    <div class="card shadow border-0">
        <div class="card-header"><i class="fas fa-desktop me-2"></i>Formulario de Formato B</div>
        <div class="card-body">

            <form method="POST" action="{{ route('admin.formatos.b.store') }}">
                @csrf

                <!-- SUBTIPO -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Subtipo de equipo</label>
                        <select name="subtipo" class="form-select" id="selectSubtipo" required>
                            <option value="">Seleccionar</option>
                            <option value="Computadora">Computadora</option>
                            <option value="Impresora">Impresora</option>
                        </select>
                    </div>
                </div>

                <hr>

                <!-- Campos GENERALES visibles SIEMPRE -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Equipo</label>
                        <input name="equipo" class="form-control" placeholder="Nombre del equipo">
                    </div>
                    <div class="col-md-4">
                        <label>Marca</label>
                        <input name="marca" class="form-control" placeholder="HP, Lenovo, Epson...">
                    </div>
                    <div class="col-md-4">
                        <label>Modelo</label>
                        <input name="modelo" class="form-control" placeholder="Modelo del equipo">
                    </div>


                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Número de Inventario</label>
                        <input name="numero_inventario" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Número de Serie</label>
                        <input name="numero_serie" class="form-control">
                    </div>
                </div>

                <!-- Campos SOLO COMPUTADORA -->
                <div id="bloqueComputadora" style="display:none;">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Procesador</label>
                            <input name="procesador" class="form-control" placeholder="Ej. Intel i5 10th Gen">
                        </div>

                        <div class="col-md-6">
                            <label>Memoria RAM</label>
                            <select name="ram" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>4 GB</option>
                                <option>8 GB</option>
                                <option>16 GB</option>
                                <option>32 GB</option>
                                <option>64 GB</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Disco duro</label>
                            <select name="disco_duro" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>HDD 500 GB</option>
                                <option>HDD 1 TB</option>
                                <option>SSD 240 GB</option>
                                <option>SSD 480 GB</option>
                                <option>SSD 1 TB</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Sistema operativo</label>
                            <select name="sistema_operativo" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>Windows 10</option>
                                <option>Windows 11</option>
                                <option>Linux</option>
                                <option>MacOS</option>
                                <option>Otro</option>
                            </select>
                        </div>
                    </div>

                </div>

                <hr>

                <!-- DESCRIPCIÓN / DIAGNÓSTICO / TIPO DE SERVICIO -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Descripción del servicio</label>
                        <textarea name="descripcion_servicio" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Tipo de servicio</label>
                        <select name="tipo_servicio" class="form-select">
                            <option value="">Seleccionar</option>
                            <option>Preventivo</option>
                            <option>Correctivo</option>
                            <option>Instalación</option>
                            <option>Corrección</option>
                            <option>Diagnóstico</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Diagnóstico</label>
                        <textarea name="diagnostico" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Origen de la falla</label>
                        <select name="origen_falla" class="form-select">
                            <option>Desgaste natural</option>
                            <option>Mala operación</option>
                            <option>Otro</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Trabajo realizado</label>
                        <textarea name="trabajo_realizado" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Conclusión del servicio</label>
                        <textarea name="conclusion_servicio" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Detalle del servicio</label>
                    <textarea name="detalle_realizado" class="form-control" rows="2"></textarea>
                </div>

                <hr>

                <!-- MATERIALES -->
                <h6><i class="fas fa-cogs me-1"></i>Materiales utilizados</h6>

                <table class="table table-bordered" id="tablaMateriales">
                    <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th>Cantidad</th>
                        <th>Acción</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td>
                            <select name="materiales[0][id_material]" class="form-select">
                                <option value="">Seleccionar material</option>
                                @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                    <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="materiales[0][cantidad]" class="form-control" min="1">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-success btn-sm agregar-material">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    </tr>

                    </tbody>
                </table>

                <hr>

                <!-- FIRMAS -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Firma solicitante</label>
                        <input name="firma_usuario" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Técnico</label>
                        <input name="firma_tecnico" readonly class="form-control"
                               value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                    </div>
                    <div class="col-md-4">
                        <label>Jefe de Área</label>
                        <input name="firma_jefe_area" readonly class="form-control"
                               value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
                    <a href="{{ route('admin.formatos.create') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>

            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){

            // Inicialmente colapsar sidebar
            const sidebar = document.getElementById('navigation');
            if (sidebar && !sidebar.classList.contains('collapsed')) {
                sidebar.classList.add('collapsed');
                localStorage.setItem('sidebarCollapsed', true);
            }

            // Mostrar / ocultar campos de computadora
            const subtipo = document.getElementById('selectSubtipo');
            const bloqueComputadora = document.getElementById('bloqueComputadora');

            function actualizarBloques(){
                if(subtipo.value === "Computadora"){
                    bloqueComputadora.style.display = "block";
                } else {
                    bloqueComputadora.style.display = "none";
                }
            }

            subtipo.addEventListener('change', actualizarBloques);

            // AGREGAR / ELIMINAR MATERIALES
            document.addEventListener('click', e=>{
                if(e.target.closest('.agregar-material')){
                    const tbody = document.querySelector('#tablaMateriales tbody');
                    const index = tbody.querySelectorAll('tr').length;

                    const fila = document.createElement('tr');
                    fila.innerHTML = `
        <td>
          <select name="materiales[${index}][id_material]" class="form-select">
            <option value="">Seleccionar material</option>
            @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                    <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
            @endforeach
                    </select>
                  </td>
                  <td>
                    <input type="number" name="materiales[${index}][cantidad]" class="form-control" min="1">
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-outline-danger btn-sm eliminar-material">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `;

                    tbody.appendChild(fila);
                }

                if(e.target.closest('.eliminar-material')){
                    e.target.closest('tr').remove();
                }
            });

        });
    </script>
@endsection