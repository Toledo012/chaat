<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('id_departamento')
                ->nullable()
                ->after('nombre')
                ->constrained('departamentos', 'id_departamento')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_departamento']); // 👈 CLAVE
            $table->dropColumn('id_departamento');   // 👈 CLAVE
        });
    }
};
