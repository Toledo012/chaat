<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->tienePermiso($permission)) {
            return redirect()->route('user.dashboard')
                ->with('error', 'No tienes permisos para realizar esta acción.');
        }

        return $next($request);
    }
}