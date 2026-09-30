<?php

namespace Tests\Feature;

use App\Mail\ErrorSistemaMail;
use App\Services\RegistroErroresService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

// Módulo de errores: registra, agrupa repetidos, frena correos y no filtra detalles al usuario.
// Corre solo la migración nueva; las demás usan triggers de MySQL y no corren en SQLite.
class RegistroErroresTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('usuarios_formatos', function ($t) {
            $t->integer('id_usuario');
            $t->string('email')->nullable();
        });
        Schema::create('cuentas', function ($t) {
            $t->integer('id_cuenta')->primary();
            $t->integer('id_usuario');
            $t->integer('id_rol');
            $t->string('estado');
        });
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_09_30_000000_create_errores_table.php']);

        DB::table('usuarios_formatos')->insert([
            ['id_usuario' => 1, 'email' => 'admin@semahn.test'],
            ['id_usuario' => 2, 'email' => 'inactivo@semahn.test'],
            ['id_usuario' => 3, 'email' => 'tecnico@semahn.test'],
        ]);
        DB::table('cuentas')->insert([
            ['id_cuenta' => 1, 'id_usuario' => 1, 'id_rol' => 1, 'estado' => 'activo'],
            ['id_cuenta' => 2, 'id_usuario' => 2, 'id_rol' => 1, 'estado' => 'inactivo'],
            ['id_cuenta' => 3, 'id_usuario' => 3, 'id_rol' => 2, 'estado' => 'activo'],
        ]);

        Mail::fake();
    }

    private function fallo(): \RuntimeException
    {
        return new \RuntimeException('detalle-interno-secreto'); // misma línea = misma huella
    }

    public function test_agrupa_repetidos_y_frena_correos(): void
    {
        $servicio = app(RegistroErroresService::class);

        $servicio->registrar($this->fallo());
        $servicio->registrar($this->fallo());

        $this->assertSame(1, DB::table('errores')->count());
        $this->assertSame(2, (int) DB::table('errores')->value('ocurrencias'));
        Mail::assertSent(ErrorSistemaMail::class, 1);
        Mail::assertSent(ErrorSistemaMail::class, fn ($m) => $m->hasTo('admin@semahn.test')
            && !$m->hasTo('inactivo@semahn.test') && !$m->hasTo('tecnico@semahn.test'));

        $this->travel(31)->minutes();
        $servicio->registrar($this->fallo());
        Mail::assertSent(ErrorSistemaMail::class, 2);
    }

    public function test_pantalla_500_no_muestra_detalles_y_queda_registrado(): void
    {
        config(['app.debug' => false]);
        Route::get('/_prueba-fallo', fn () => throw $this->fallo());

        $this->get('/_prueba-fallo')
            ->assertStatus(500)
            ->assertSee('Algo salió mal de nuestro lado')
            ->assertDontSee('detalle-interno-secreto')
            ->assertDontSee('RuntimeException');

        $this->assertSame('detalle-interno-secreto', DB::table('errores')->value('mensaje'));
    }

    public function test_invitado_no_entra_al_panel(): void
    {
        $this->get('/admin/errores')->assertRedirect(route('login'));
    }
}
