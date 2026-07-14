<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuentas', function (Blueprint $table) {
            // Preferencias de correo por cuenta (modelo opt-out).
            // NULL = todas habilitadas por defecto. Estructura esperada:
            // { "nuevos": true, "asignados": true, "concluidos": true }
            $table->json('preferencias_correo')->nullable()->after('id_rol');
        });
    }

    public function down(): void
    {
        Schema::table('cuentas', function (Blueprint $table) {
            $table->dropColumn('preferencias_correo');
        });
    }
};
