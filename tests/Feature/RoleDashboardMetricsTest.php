<?php

namespace Tests\Feature;

use App\Http\Controllers\DeptoViewController;
use App\Http\Controllers\UserController;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleDashboardMetricsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::create(2026, 9, 30, 12));

        Schema::create('tickets', function ($table) {
            $table->integer('id_ticket');
            $table->integer('creado_por');
            $table->integer('asignado_a')->nullable();
            $table->string('estado');
            $table->dateTime('created_at');
        });
        Schema::create('servicios', function ($table) {
            $table->integer('id_servicio');
            $table->integer('id_usuario');
            $table->string('tipo_formato');
            $table->date('fecha');
        });
        Schema::create('rol_permiso', function ($table) {
            $table->integer('id_rol');
            $table->integer('id_permiso');
        });
        Schema::create('permisos', function ($table) {
            $table->integer('id_permiso');
            $table->string('nombre');
        });
        Schema::create('usuarios_formatos', function ($table) {
            $table->integer('id_usuario');
            $table->integer('id_departamento')->nullable();
            $table->string('nombre');
            $table->string('puesto')->nullable();
            $table->string('email')->nullable();
        });
        Schema::create('departamentos', function ($table) {
            $table->integer('id_departamento');
            $table->string('nombre');
        });

        DB::table('tickets')->insert([
            ['id_ticket' => 1, 'creado_por' => 3, 'asignado_a' => 2, 'estado' => 'nuevo', 'created_at' => '2026-09-10 12:00:00'],
            ['id_ticket' => 2, 'creado_por' => 3, 'asignado_a' => 2, 'estado' => 'completado', 'created_at' => '2025-09-10 12:00:00'],
            ['id_ticket' => 3, 'creado_por' => 4, 'asignado_a' => 4, 'estado' => 'nuevo', 'created_at' => '2026-09-11 12:00:00'],
            ['id_ticket' => 4, 'creado_por' => 4, 'asignado_a' => null, 'estado' => 'nuevo', 'created_at' => '2026-08-11 12:00:00'],
        ]);
        DB::table('servicios')->insert([
            ['id_servicio' => 1, 'id_usuario' => 11, 'tipo_formato' => 'A', 'fecha' => '2026-09-12'],
            ['id_servicio' => 2, 'id_usuario' => 12, 'tipo_formato' => 'B', 'fecha' => '2026-09-12'],
            ['id_servicio' => 3, 'id_usuario' => 11, 'tipo_formato' => 'C', 'fecha' => '2026-08-12'],
        ]);
        DB::table('permisos')->insert(['id_permiso' => 2, 'nombre' => 'gestion_formatos']);
        DB::table('rol_permiso')->insert(['id_rol' => 2, 'id_permiso' => 2]);
        DB::table('departamentos')->insert(['id_departamento' => 1, 'nombre' => 'Soporte']);
        DB::table('usuarios_formatos')->insert([
            ['id_usuario' => 11, 'id_departamento' => 1, 'nombre' => 'Técnica Uno', 'puesto' => 'Técnica', 'email' => 'tecnica@example.test'],
            ['id_usuario' => 13, 'id_departamento' => 1, 'nombre' => 'Solicitante Tres', 'puesto' => 'Enlace', 'email' => 'enlace@example.test'],
            ['id_usuario' => 15, 'id_departamento' => 1, 'nombre' => 'Técnico Nuevo', 'puesto' => 'Técnico', 'email' => 'nuevo@example.test'],
        ]);
    }

    public function test_usuario_solo_ve_metricas_asignadas_y_formatos_propios(): void
    {
        $account = new Cuenta();
        $account->id_cuenta = 2;
        $account->id_rol = 2;
        $account->id_usuario = 11;
        $this->actingAs($account);

        $data = app(UserController::class)->dashboard(Request::create('/user/dashboard', 'GET', [
            'anio' => '2026', 'mes' => '9',
        ]))->getData();

        $this->assertSame(2, $data['ticketStats']->total);
        $this->assertSame(1, $data['ticketStats']->activos);
        $this->assertSame(1, $data['disponibles']);
        $this->assertSame(1, $data['ticketActivity']['current'][8]);
        $this->assertSame(1, $data['ticketActivity']['previous'][8]);
        $this->assertSame(1, $data['formatosTotal']);
        $this->assertSame(1, $data['formatosPorTipo']['A']);
        $this->assertStringContainsString('Mis tickets por mes', view('user.dashboard', $data)->render());
    }

    public function test_departamento_solo_ve_sus_solicitudes(): void
    {
        $account = new Cuenta();
        $account->id_cuenta = 3;
        $account->id_rol = 3;
        $account->id_usuario = 13;
        $this->actingAs($account);

        $data = app(DeptoViewController::class)->dashboard(Request::create('/departamento/dashboard', 'GET', [
            'anio' => '2026', 'mes' => '9',
        ]))->getData();

        $this->assertSame(2, $data['allStatusCounts']->sum());
        $this->assertSame(1, $data['statusCounts']['nuevo']);
        $this->assertSame(1, $data['ticketActivity']['current'][8]);
        $this->assertSame(1, $data['ticketActivity']['previous'][8]);
        $this->assertCount(2, $data['misSolicitudes']);
        $this->assertStringContainsString('Solicitudes por mes', view('departamento.dashboard', $data)->render());
    }

    public function test_usuario_sin_tickets_muestra_ceros_y_estado_vacio(): void
    {
        $account = new Cuenta();
        $account->id_cuenta = 5;
        $account->id_rol = 2;
        $account->id_usuario = 15;
        $this->actingAs($account);

        $data = app(UserController::class)->dashboard(Request::create('/user/dashboard'))->getData();

        $this->assertSame(0, $data['ticketStats']->total);
        $this->assertSame(0, $data['ticketStats']->activos);
        $this->assertSame(0, $data['ticketStats']->completados);
        $this->assertStringContainsString('Aún no tienes tickets asignados', view('user.dashboard', $data)->render());
    }
}
