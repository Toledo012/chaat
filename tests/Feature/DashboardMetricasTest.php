<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardMetricasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::create(2026, 9, 30, 12));

        Schema::create('usuarios_formatos', function ($table) {
            $table->integer('id_usuario');
            $table->string('nombre');
        });
        Schema::create('cuentas', function ($table) {
            $table->integer('id_cuenta');
            $table->integer('id_rol');
            $table->string('estado');
        });
        Schema::create('servicios', function ($table) {
            $table->integer('id_servicio');
            $table->integer('id_usuario');
            $table->string('tipo_formato');
            $table->date('fecha');
        });
        Schema::create('tickets', function ($table) {
            $table->integer('id_ticket');
            $table->string('estado');
            $table->dateTime('created_at');
        });
        Schema::create('catalogo_materiales', function ($table) {
            $table->integer('id_material');
            $table->string('nombre');
            $table->string('unidad_sugerida')->nullable();
        });

        DB::table('usuarios_formatos')->insert(['id_usuario' => 1, 'nombre' => 'Técnica']);
        DB::table('cuentas')->insert(['id_cuenta' => 1, 'id_rol' => 1, 'estado' => 'activo']);
        DB::table('servicios')->insert([
            ['id_servicio' => 1, 'id_usuario' => 1, 'tipo_formato' => 'A', 'fecha' => '2026-09-02'],
            ['id_servicio' => 2, 'id_usuario' => 1, 'tipo_formato' => 'B', 'fecha' => '2026-09-15'],
            ['id_servicio' => 3, 'id_usuario' => 1, 'tipo_formato' => 'A', 'fecha' => '2026-08-05'],
        ]);
        DB::table('tickets')->insert([
            ['id_ticket' => 1, 'estado' => 'nuevo', 'created_at' => '2026-09-01 10:00:00'],
            ['id_ticket' => 2, 'estado' => 'asignado', 'created_at' => '2026-09-15 10:00:00'],
            ['id_ticket' => 3, 'estado' => 'completado', 'created_at' => '2026-08-01 10:00:00'],
            ['id_ticket' => 4, 'estado' => 'completado', 'created_at' => '2025-09-30 10:00:00'],
        ]);

        $admin = new Cuenta();
        $admin->id_cuenta = 1;
        $admin->id_rol = 1;
        $admin->estado = 'activo';
        $this->actingAs($admin);
    }

    public function test_filtra_formatos_y_compara_tickets_por_mes(): void
    {
        $view = app(AdminController::class)->dashboard(Request::create('/admin/dashboard', 'GET', [
            'anio' => '2026', 'mes' => '9',
        ]));
        $data = $view->getData();

        $this->assertSame(2, $data['ticketsMes']);
        $this->assertSame(1, $data['ticketsMesAnterior']);
        $this->assertSame(1, $data['ticketsMismoMesAnterior']);
        $this->assertSame(2, $data['totalFormatos']);
        $this->assertSame(2, $data['stats']['tickets_abiertos']);
        $this->assertSame(2, $data['ticketsActuales'][8]);
        $this->assertSame(1, $data['ticketsAnteriores'][8]);

        $anual = app(AdminController::class)->dashboard(Request::create('/admin/dashboard', 'GET', [
            'anio' => '2026', 'mes' => '0',
        ]))->getData();
        $this->assertSame(3, $anual['totalFormatos']);
        $this->assertSame(3, $anual['ticketsAnio']);
        $this->assertSame(1, $anual['ticketsAnioAnterior']);
    }
}
