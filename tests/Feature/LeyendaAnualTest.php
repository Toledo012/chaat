<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Leyenda del encabezado: la del año del formato, o la del año anterior más reciente.
// Corre solo su migración (que precarga 2025); las demás usan triggers de MySQL.
class LeyendaAnualTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_09_30_000100_create_leyendas_table.php']);
    }

    private function leyenda(?string $fecha): string
    {
        return trim(Blade::render('<x-leyenda-anual :fecha="$fecha" />', ['fecha' => $fecha]));
    }

    public function test_usa_la_de_su_anio_o_la_anterior(): void
    {
        $this->assertSame('<em>"2025, Año de Rosario Castellanos Figueroa"</em>', $this->leyenda('2025-06-15'));
        $this->assertStringContainsString('2025, Año de Rosario', $this->leyenda('2026-02-01'), '2026 sin capturar: usa 2025');
        $this->assertSame('', $this->leyenda('2024-12-31'), 'antes de la primera: nada');

        DB::table('leyendas')->insert(['anio' => 2026, 'texto' => '2026, Año de Jaime Sabines Gutiérrez']);

        $this->assertStringContainsString('Jaime Sabines', $this->leyenda('2026-02-01'));
        $this->assertStringContainsString('Rosario', $this->leyenda('2025-06-15'), 'un formato 2025 conserva su leyenda');
    }

    public function test_invitado_no_entra_a_editar(): void
    {
        $this->get('/admin/leyendas')->assertRedirect(route('login'));
        $this->post('/admin/leyendas', ['anio' => 2026, 'texto' => 'x'])->assertRedirect(route('login'));
    }
}
