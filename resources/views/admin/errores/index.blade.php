@extends('layouts.admin')

@section('title', 'Errores del sistema')
@section('header_title', 'Errores del Sistema')
@section('header_subtitle', 'Fallos registrados automáticamente')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-bug fa-lg text-danger"></i>
                <div>
                    <h5 class="mb-0">Fallos del sistema</h5>
                    <small class="text-body-secondary">El mismo fallo repetido suma ocurrencias en una sola fila</small>
                </div>
            </div>

            <div class="btn-group btn-group-sm">
                @foreach(['pendientes' => 'Pendientes', 'atendidos' => 'Atendidos', 'todos' => 'Todos'] as $valor => $texto)
                    <a href="{{ route('admin.errores.index', ['estado' => $valor]) }}"
                       class="btn {{ $estado === $valor ? 'btn-primary' : 'btn-outline-primary' }}">{{ $texto }}</a>
                @endforeach
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Última vez</th>
                        <th>Error</th>
                        <th>Petición</th>
                        <th class="text-center">Veces</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($errores as $error)
                        <tr>
                            <td class="text-body-secondary text-nowrap">{{ $error->ultima_vez }}</td>
                            <td>
                                <div class="fw-semibold">{{ class_basename($error->clase) }}</div>
                                <small class="text-body-secondary">{{ \Illuminate\Support\Str::limit($error->mensaje, 120) }}</small>
                            </td>
                            <td><small class="text-body-secondary">{{ $error->metodo }} {{ \Illuminate\Support\Str::limit($error->url, 60) }}</small></td>
                            <td class="text-center">
                                <span class="badge bg-danger-subtle text-danger px-3 py-2">{{ $error->ocurrencias }}</span>
                            </td>
                            <td>
                                @if($error->atendido_en)
                                    <span class="badge bg-success-subtle text-success px-3 py-2">Atendido</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.errores.show', $error->id_error) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-body-secondary py-5">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                No hay errores en esta vista.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($errores->hasPages())
            <div class="card-footer">{{ $errores->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</div>
@endsection
