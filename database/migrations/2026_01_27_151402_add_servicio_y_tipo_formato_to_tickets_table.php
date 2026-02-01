<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            // Tipo de formato requerido por el ticket (A–D)
            $table->enum('tipo_formato', ['a', 'b', 'c', 'd'])
                ->after('prioridad');

            // Relación con servicios (se crea cuando el técnico inicia atención)
            $table->unsignedBigInteger('id_servicio')
                ->nullable()
                ->after('asignado_por');

            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->nullOnDelete();

            $table->index('id_servicio');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            $table->dropForeign(['id_servicio']);
            $table->dropIndex(['id_servicio']);

            $table->dropColumn('id_servicio');
            $table->dropColumn('tipo_formato');
        });
    }
};
