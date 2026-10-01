<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DepartamentoNombreLongitudTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('departamentos', function ($table) {
            $table->id('id_departamento');
            $table->string('nombre', 300);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
        });
        Schema::create('roles', function ($table) {
            $table->integer('id_rol');
            $table->string('nombre');
        });
        DB::table('roles')->insert(['id_rol' => 1, 'nombre' => 'Administrador']);
    }

    public function test_acepta_300_y_rechaza_301_caracteres_en_departamentos_y_quick_store(): void
    {
        $admin = new Cuenta();
        $admin->id_cuenta = 1;
        $admin->id_rol = 1;
        $this->actingAs($admin);

        $this->post(route('admin.departamentos.store'), ['nombre' => str_repeat('A', 300)])
            ->assertRedirect(route('admin.departamentos.index'));
        $this->assertSame(300, mb_strlen(DB::table('departamentos')->first()->nombre));

        $this->postJson(route('admin.departamentos.store'), ['nombre' => str_repeat('D', 301)])
            ->assertUnprocessable()->assertJsonValidationErrors('nombre');

        $id = DB::table('departamentos')->first()->id_departamento;
        $this->put(route('admin.departamentos.update', $id), ['nombre' => str_repeat('C', 300)])
            ->assertRedirect(route('admin.departamentos.index'));
        $this->assertSame(300, mb_strlen(DB::table('departamentos')->where('id_departamento', $id)->value('nombre')));
        $this->putJson(route('admin.departamentos.update', $id), ['nombre' => str_repeat('E', 301)])
            ->assertUnprocessable()->assertJsonValidationErrors('nombre');

        $user = new Cuenta();
        $user->id_cuenta = 2;
        $user->id_rol = 2;
        $this->actingAs($user);

        $this->postJson(route('admin.departamentos.quickStore'), ['nombre' => str_repeat('B', 300)])
            ->assertCreated();
        $this->postJson(route('admin.departamentos.quickStore'), ['nombre' => str_repeat('F', 301)])
            ->assertUnprocessable()->assertJsonValidationErrors('nombre');
    }

}
