<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TicketService;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Permite recibir roles separados por coma en un solo parámetro
        if (count($roles) === 1 && is_string($roles[0]) && str_contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }

        $rolNombre = strtolower(optional($user->rol)->nombre ?? '');

        // Si no hay rol asociado, fuera
        if ($rolNombre === '') {
            abort(403);
        }

        // Comparamos contra lo que pases: Administrador, Usuario, Departamento
        $roles = array_map(fn($r) => strtolower(trim($r)), $roles);

        if (!in_array($rolNombre, $roles, true)) {
            // Redirección a dashboard según rol real
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'No tienes acceso a esa sección.');
            }
            if (method_exists($user, 'isDepartamento') && $user->isDepartamento()) {
                return redirect()->route('departamento.dashboard')->with('error', 'No tienes acceso a esa sección.');
            }
            return redirect()->route('user.dashboard')->with('error', 'No tienes acceso a esa sección.');
        }

        return $next($request);
    }
}
