{{--
    Leyenda anual del encabezado de los formatos. Usa la del año de la fecha del formato
    (un formato de 2025 reimpreso en 2026 conserva la de 2025); si ese año no tiene,
    la del año anterior más reciente. Se edita en Gestión de Formatos → Leyenda del encabezado.
--}}
@props(['fecha' => null])
@php
    $anio = $fecha ? \Carbon\Carbon::parse($fecha)->year : now()->year;
    $leyenda = \Illuminate\Support\Facades\DB::table('leyendas')
        ->where('anio', '<=', $anio)
        ->orderByDesc('anio')
        ->value('texto');
@endphp
@if($leyenda)<em>"{{ $leyenda }}"</em>@endif
