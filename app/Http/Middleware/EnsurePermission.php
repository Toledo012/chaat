<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, ...$params)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin siempre pasa
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }

        // Normalizar parámetros
        $params = array_values(array_filter($params, fn($p) => $p !== null && $p !== ''));

        $modeAny = false;
        if (!empty($params) && strtolower($params[0]) === 'any') {
            $modeAny = true;
            array_shift($params);
        }

        // Soportar una cadena con comas
        if (count($params) === 1 && str_contains($params[0], ',')) {
            $params = array_map('trim', explode(',', $params[0]));
        }

        // Sin permisos definidos: negar
        if (empty($params)) {
            return redirect()->route('user.dashboard')->with('error', 'No tienes permisos suficientes');
        }

        // Fallback genérico: usar método tienePermiso si existe
        $has = false;
        $map = [
            'gestion_usuarios' => 1,
            'gestion_formatos' => 2,
            'crear_usuarios' => 3,
            'editar_usuarios' => 4,
            'eliminar_usuarios' => 5,
            'cambiar_roles' => 6,
            'activar_cuentas' => 7,
        ];

        foreach ($params as $perm) {
            $ok = false;
            if (method_exists($user, 'tienePermiso')) {
                $ok = $user->tienePermiso($perm);
            }
            // Fallback por sesión permisos ID
            if (!$ok && isset($map[$perm])) {
                $sessionPerms = (array) session('permisos_usuario', []);
                $ok = in_array((int) $map[$perm], array_map('intval', $sessionPerms), true);
            }
            // Fallback por método permisosArray
            if (!$ok && method_exists($user, 'permisosArray')) {
                $ok = in_array((int) ($map[$perm] ?? -1), (array) $user->permisosArray(), true);
            }

            if ($modeAny && $ok) { $has = true; break; }
            if (!$modeAny && !$ok) { $has = false; break; } // all-mode falla rápido
            if (!$modeAny && $ok) { $has = true; }
        }

        if (!$has) {
            return redirect()->route('user.dashboard')->with('error', 'No tienes permisos suficientes');
        }

        return $next($request);
    }
}
