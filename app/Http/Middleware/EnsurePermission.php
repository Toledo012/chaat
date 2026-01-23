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

        // Soportar una cadena con comas (permiso:a,b,c)
        if (count($params) === 1 && is_string($params[0]) && str_contains($params[0], ',')) {
            $params = array_map('trim', explode(',', $params[0]));
        }

        // Sin permisos definidos: negar
        if (empty($params)) {
            return redirect()->route('user.dashboard')
                ->with('error', 'No tienes permisos suficientes');
        }

        $has = $modeAny ? false : true; // any: arranca false, all: arranca true

        foreach ($params as $perm) {
            $ok = method_exists($user, 'tienePermiso') && $user->tienePermiso($perm);

            if ($modeAny) {
                if ($ok) { $has = true; break; }
            } else {
                if (!$ok) { $has = false; break; }
            }
        }



        //redireccion por rol//prueba//
if (!$has) {

    if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
        return redirect()->route('admin.dashboard')->with('error', 'No tienes permisos suficientes');
    }

    if (method_exists($user, 'isDepartamento') && $user->isDepartamento()) {
        return redirect()->route('departamento.dashboard')->with('error', 'No tienes permisos suficientes');
    }

    return redirect()->route('user.dashboard')->with('error', 'No tienes permisos suficientes');
}


        return $next($request);
    }
}
