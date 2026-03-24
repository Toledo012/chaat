    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Vista previa - Formato A</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { font-family: Arial, sans-serif; background:#f8f9fa; padding:2rem; }
            .header { border-bottom:3px solid #399e91; padding-bottom:10px; margin-bottom:20px; }
            .header img { width:110px; }
            .titulo  { text-align:center; font-weight:bold; color:#0a3622; text-transform:uppercase; margin-top:.5rem; }
            .subtitulo { text-align:center; font-size:13px; color:#6c757d; margin-bottom:1rem; }
            .section-title { background:#d1e7dd; color:#0a3622; font-weight:bold;
                padding:6px 10px; border-radius:4px; margin-top:1rem; margin-bottom:.4rem; }
            table { width:100%; border-collapse:collapse; }
            table td, table th { border:1px solid #dee2e6; padding:8px; vertical-align:top; }
            table th { background:#f8f9fa; width:22%; font-weight:600; }
            .firmas td { border:none; text-align:center; padding-top:30px; }
            .footer { text-align:center; font-size:13px; color:#6c757d;
                border-top:2px solid #dee2e6; margin-top:2rem; padding-top:10px; }
            .edicion { display:none; }
            .badge-memo { display:inline-block; font-size:.75rem; background:#d1f0eb; color:#155d50;
                border:1px solid #399e91; border-radius:6px; padding:1px 8px; margin-left:6px; font-weight:600; }
        </style>
    </head>
    <body>
    <div class="container bg-white shadow p-4 rounded">

        {{-- Barra acciones --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success" onclick="toggleEdicion()">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Editar Formato
                </button>
                <a href="{{ route('admin.formatos.a.pdf', $servicio->id_servicio) }}"
                   target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.formatos.update', ['A', $servicio->id_servicio]) }}">
            @csrf

            {{-- Encabezado institucional --}}
            <div class="row align-items-center header">
                <div class="col-3 text-center">
                    <img src="{{ asset('images/logo_semahn2.png') }}" alt="SEMAHN">
                </div>
                <div class="col-9 text-center">
                    <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
                    UNIDAD DE APOYO ADMINISTRATIVO — ÁREA DE INFORMÁTICA<br>
                    <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
                </div>
            </div>

            <h5 class="titulo">Formato A — Soporte y Desarrollo</h5>
            <p class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</p>

            {{-- Datos Generales --}}
            <div class="section-title">Datos Generales</div>
            <table class="table table-bordered">
                <tr>
                    <th>Folio</th>
                    <td>{{ $servicio->folio ?? 'ID-'.$servicio->id_servicio }}</td>
                    <th>Fecha</th>
                    <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Tipo de Formato</th><td>A</td>
                    <th>Departamento</th>
                    <td>
                        <span class="vista">
                            {{ $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado' }}
                        </span>
                        <select name="id_departamento" class="form-select edicion">
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}"
                                    {{ $dep->id_departamento == $servicio->id_departamento ? 'selected' : '' }}>
                                    {{ $dep->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>

            {{-- Clasificación --}}
            <div class="section-title">Clasificación del Servicio</div>
            <table class="table table-bordered">
                <tr>
                    <th>Subtipo</th>
                    <td>
                        <span class="vista">{{ $servicio->subtipo }}</span>
                        <select name="subtipo" class="form-select edicion">
                            @foreach(['Desarrollo','Soporte'] as $op)
                                <option {{ $servicio->subtipo == $op ? 'selected':'' }}>{{ $op }}</option>
                            @endforeach
                        </select>
                    </td>
                    <th>Tipo de Atención</th>
                    <td>
                        {{-- VISTA --}}
                        <span class="vista">
                            {{ $servicio->tipo_atencion }}
                            @if($servicio->tipo_atencion === 'Memo' && $servicio->num_memo)
                                <span class="badge-memo">Memo N° {{ $servicio->num_memo }}</span>
                            @endif
                        </span>
                        {{-- EDICIÓN --}}
                        <div class="edicion">
                            <select name="tipo_atencion" id="tipoAtencionEdit" class="form-select">
                                @foreach(['Memo','Teléfono','Jefe','Usuario'] as $op)
                                    <option {{ $servicio->tipo_atencion == $op ? 'selected':'' }}>{{ $op }}</option>
                                @endforeach
                            </select>
                            <div id="bloqueMemoEdit" class="mt-2"
                                 style="{{ $servicio->tipo_atencion === 'Memo' ? '' : 'display:none' }}">
                                <input type="text" name="num_memo" id="numMemoEdit" class="form-control"
                                       placeholder="Número o folio del memo" maxlength="100"
                                       value="{{ $servicio->num_memo }}">
                                <small class="text-muted">Número o folio del memo de referencia.</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Tipo de Servicio</th>
                    <td>
                        <span class="vista">{{ $servicio->tipo_servicio }}</span>
                        <select name="tipo_servicio" class="form-select edicion">
                            @foreach(['Equipos','Redes LAN/WAN','Antivirus','Software'] as $op)
                                <option {{ $servicio->tipo_servicio == $op ? 'selected':'' }}>{{ $op }}</option>
                            @endforeach
                        </select>
                    </td>
                    <th>Conclusión</th>
                    <td>
                        <span class="vista">{{ $servicio->conclusion_servicio }}</span>
                        <select name="conclusion_servicio" class="form-select edicion">
                            @foreach(['Terminado','En proceso'] as $op)
                                <option {{ $servicio->conclusion_servicio == $op ? 'selected':'' }}>{{ $op }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>

            {{-- Campos de texto --}}
            @foreach([
                'peticion'          => 'Petición del Servicio',
                'trabajo_realizado' => 'Trabajo Realizado',
                'detalle_realizado' => 'Detalle del Trabajo Realizado',
                'observaciones'     => 'Observaciones'
            ] as $campo => $label)
                <div class="section-title">{{ $label }}</div>
                <p class="vista">{{ $servicio->$campo ?: 'Sin información registrada.' }}</p>
                <textarea name="{{ $campo }}" class="form-control edicion" rows="3">{{ $servicio->$campo }}</textarea>
            @endforeach

            {{-- Firmas --}}
            <div class="section-title">Firmas de Conformidad</div>
            <table class="firmas">
                <tr>
                    <td>
                        <strong>Solicitante</strong><br>
                        <span class="vista">{{ $servicio->firma_usuario ?: '_________________' }}</span>
                        <input name="firma_usuario" class="form-control edicion" value="{{ $servicio->firma_usuario }}">
                    </td>
                    <td>
                        <strong>Técnico</strong><br>
                        <span class="vista">{{ $servicio->firma_tecnico ?: '_________________' }}</span>
                        <input name="firma_tecnico" class="form-control edicion" value="{{ $servicio->firma_tecnico }}">
                    </td>
                    <td>
                        <strong>Jefe de Área</strong><br>
                        <span class="vista">{{ $servicio->firma_jefe_area ?: '_________________' }}</span>
                        <input name="firma_jefe_area" class="form-control edicion" value="{{ $servicio->firma_jefe_area }}">
                    </td>
                </tr>
            </table>

            {{-- Guardar --}}
            <div id="guardarBtn" class="text-end mt-4" style="display:none;">
                <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>

        <div class="footer">
            Sistema de Formatos Digitales SEMAHN 2026 — Generado el {{ date('d/m/Y H:i') }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleEdicion() {
            document.querySelectorAll('.vista').forEach(v  => v.style.display = 'none');
            document.querySelectorAll('.edicion').forEach(e => e.style.display = 'block');
            document.getElementById('guardarBtn').style.display = 'block';
            if (document.getElementById('tipoAtencionEdit').value === 'Memo') {
                document.getElementById('bloqueMemoEdit').style.display = 'block';
                document.getElementById('numMemoEdit').setAttribute('required','required');
            }
        }
        document.getElementById('tipoAtencionEdit').addEventListener('change', function () {
            const bloque = document.getElementById('bloqueMemoEdit');
            const input  = document.getElementById('numMemoEdit');
            if (this.value === 'Memo') {
                bloque.style.display = 'block';
                input.setAttribute('required','required');
            } else {
                bloque.style.display = 'none';
                input.removeAttribute('required');
                input.value = '';
            }
        });
    </script>
    </body>
    </html>
