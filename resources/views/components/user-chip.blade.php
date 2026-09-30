@props([
    'cuenta' => null,
    'icon' => 'fa-user',
    'fallback' => 'Sistema',
    'departamento' => null, // Si se pasa, sobreescribe el depto de la cuenta (ej. área del ticket)
])

@if($cuenta)
    @php
        $nombre = $cuenta->nombre_mostrado;
        $depto  = $departamento ?? $cuenta->departamento_nombre;
    @endphp
    <div class="d-flex align-items-start gap-2">
        <div class="bg-body-tertiary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width: 28px; height: 28px; font-size: 0.7rem; font-weight: bold; color: var(--primary-color); border: 1px solid var(--border-color);">
            {{ strtoupper(mb_substr($nombre, 0, 1)) }}
        </div>
        <div class="lh-sm">
            <span class="small fw-semibold text-body d-block">
                <i class="fas {{ $icon }} me-1 text-body-secondary small"></i>{{ $nombre }}
            </span>
            @if($depto)
                <span class="text-body-secondary" style="font-size: 0.68rem;">
                    <i class="fas fa-building me-1 opacity-75"></i>{{ $depto }}
                </span>
            @endif
        </div>
    </div>
@else
    <span class="text-body-secondary small italic">{{ $fallback }}</span>
@endif
