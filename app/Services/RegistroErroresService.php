<?php

namespace App\Services;

use App\Mail\ErrorSistemaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Registra los fallos del sistema en la tabla `errores` y avisa a los administradores.
 *
 * Corre dentro del manejador de excepciones de Laravel: NUNCA debe lanzar.
 * Si falla (p. ej. la BD está caída) solo deja rastro en el log de archivo,
 * que Laravel sigue escribiendo de todos modos.
 *
 * Va aparte de `movimientos`: esa tabla es auditoría de quién cambió qué;
 * un fallo no es la operación de nadie.
 */
class RegistroErroresService
{
    /** Un mismo fallo no vuelve a mandar correo antes de esto. */
    private const MINUTOS_ENTRE_AVISOS = 30;

    public function registrar(Throwable $e, ?Request $request = null): void
    {
        try {
            $huella = hash('sha256', $e::class . '|' . $e->getFile() . '|' . $e->getLine());
            $ahora = now();

            // El mismo fallo repetido suma ocurrencias: un error en bucle no llena la tabla
            $repetido = DB::table('errores')->where('huella', $huella)->update([
                'ocurrencias' => DB::raw('ocurrencias + 1'),
                'ultima_vez'  => $ahora,
            ]);

            if (!$repetido) {
                DB::table('errores')->insert([
                    'huella'      => $huella,
                    'clase'       => Str::limit($e::class, 185, ''),
                    'mensaje'     => $e->getMessage(),
                    'archivo'     => Str::limit($e->getFile(), 250, ''),
                    'linea'       => $e->getLine(),
                    'metodo'      => $request?->method(),
                    'url'         => $request ? Str::limit($request->url(), 500, '') : null, // sin query: ahí pueden ir tokens
                    'id_cuenta'   => Auth::id(),
                    'ip'          => $request?->ip(),
                    'navegador'   => Str::limit((string) $request?->userAgent(), 400, ''),
                    'traza'       => Str::limit($e->getTraceAsString(), 8000),
                    'primera_vez' => $ahora,
                    'ultima_vez'  => $ahora,
                ]);
            }

            $this->avisarSiProcede($huella, $ahora);
        } catch (Throwable $fallo) {
            Log::error('No se pudo registrar un error del sistema', [
                'original'     => $e->getMessage(),
                'al_registrar' => $fallo->getMessage(),
            ]);
        }
    }

    /**
     * Correo a los admins activos, con freno: la primera vez y luego cada MINUTOS_ENTRE_AVISOS.
     * El UPDATE condicionado "toma el turno" de forma atómica: si dos peticiones fallan a la vez,
     * solo una manda el correo. No usa Cache porque la BD no tiene tabla `cache`.
     */
    private function avisarSiProcede(string $huella, $ahora): void
    {
        $tomoTurno = DB::table('errores')
            ->where('huella', $huella)
            ->where(fn ($q) => $q->whereNull('avisado_en')
                ->orWhere('avisado_en', '<', $ahora->copy()->subMinutes(self::MINUTOS_ENTRE_AVISOS)))
            ->update(['avisado_en' => $ahora]);

        if (!$tomoTurno) {
            return;
        }

        $correos = DB::table('cuentas')
            ->join('usuarios_formatos', 'usuarios_formatos.id_usuario', '=', 'cuentas.id_usuario')
            ->where('cuentas.id_rol', 1)
            ->where('cuentas.estado', 'activo')
            ->pluck('usuarios_formatos.email')
            ->filter(fn ($c) => filter_var($c, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if (!$correos) {
            return;
        }

        // Síncrono como el resto de correos del sistema (no hay tabla `jobs` para encolar).
        // ponytail: si el SMTP tarda, el usuario espera esos segundos en la pantalla de error;
        // upgrade path: crear la tabla jobs, correr un worker y cambiar send() por queue().
        Mail::to($correos)->send(new ErrorSistemaMail(DB::table('errores')->where('huella', $huella)->first()));
    }
}
