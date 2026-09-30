<?php

namespace Tests\Feature;

use App\Http\Controllers\FormatoController;
use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

// Acceso a formatos por registro: nadie ve formatos ajenos cambiando el id en la URL.
// Solo se crean las 2 tablas que consulta autorizarServicio(); las migraciones reales
// usan triggers de MySQL y no corren en SQLite.
class FormatoAccesoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('servicios', function ($t) {
            $t->integer('id_servicio');
            $t->integer('id_usuario');
        });
        Schema::create('tickets', function ($t) {
            $t->integer('id_servicio');
            $t->integer('creado_por');
            $t->integer('asignado_a')->nullable();
        });

        DB::table('servicios')->insert([
            ['id_servicio' => 1, 'id_usuario' => 10], // hecho por el técnico 10
            ['id_servicio' => 2, 'id_usuario' => 20], // hecho por el técnico 20, ticket asignado al 10
        ]);
        DB::table('tickets')->insert([
            ['id_servicio' => 2, 'creado_por' => 30, 'asignado_a' => 10], // ticket del depto 30
        ]);
    }

    private function cuenta(int $id, int $rol): Cuenta
    {
        return (new Cuenta)->forceFill(['id_cuenta' => $id, 'id_usuario' => $id, 'id_rol' => $rol, 'estado' => 'activo']);
    }

    private function puedeVer(Cuenta $cuenta, int $idServicio): bool
    {
        $this->actingAs($cuenta);
        $autorizar = (new \ReflectionMethod(FormatoController::class, 'autorizarServicio'))->getClosure(app(FormatoController::class));

        try {
            $autorizar($idServicio);
            return true;
        } catch (HttpException $e) {
            return false;
        }
    }

    public function test_reglas_por_rol(): void
    {
        $admin = $this->cuenta(1, 1);
        $tecnico10 = $this->cuenta(10, 2);
        $tecnico40 = $this->cuenta(40, 2);
        $depto30 = $this->cuenta(30, 3);
        $depto50 = $this->cuenta(50, 3);

        $this->assertTrue($this->puedeVer($admin, 1));
        $this->assertTrue($this->puedeVer($tecnico10, 1), 'técnico: el suyo');
        $this->assertTrue($this->puedeVer($tecnico10, 2), 'técnico: de ticket asignado a él');
        $this->assertFalse($this->puedeVer($tecnico40, 1), 'técnico: ajeno');
        $this->assertTrue($this->puedeVer($depto30, 2), 'depto: de su ticket');
        $this->assertFalse($this->puedeVer($depto30, 1), 'depto: sin ticket suyo');
        $this->assertFalse($this->puedeVer($depto50, 2), 'depto: ticket de otro depto');
    }

    public function test_invitado_no_ve_pdf_ni_preview(): void
    {
        $this->get('/admin/formatos/a/1/pdf')->assertRedirect(route('login'));
        $this->get('/admin/formatos/a/1/preview')->assertRedirect(route('login'));
    }
}
