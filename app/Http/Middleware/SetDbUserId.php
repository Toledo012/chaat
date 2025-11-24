<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetDbUserId
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (auth()->check() && isset(auth()->user()->id_cuenta)) {
                DB::statement('SET @id_cuenta = ?', [auth()->user()->id_cuenta]);
            } else {
                DB::statement('SET @id_cuenta = NULL');
            }
        } catch (\Throwable $e) {
            // Ignorar fallos al configurar variable; no bloquear la petición
        }

        return $next($request);
    }
}

