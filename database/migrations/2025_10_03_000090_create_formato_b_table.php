<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formato_b', function (Blueprint $table) {
            $table->id('id_formatoB');
            $table->unsignedBigInteger('id_servicio');

            $table->enum('subtipo', ['Computadora','Impresora']);
            $table->text('descripcion_servicio')->nullable();
            $table->string('equipo',50)->nullable();
            $table->string('marca',50)->nullable();
            $table->string('modelo',50)->nullable();
            $table->string('numero_inventario',50)->nullable();
            $table->string('numero_serie',50)->nullable();

            $table->string('procesador',50)->nullable();
            $table->string('ram',50)->nullable();
            $table->string('disco_duro',50)->nullable();
            $table->string('sistema_operativo',50)->nullable();

            $table->enum('tipo_servicio', ['Preventivo','Correctivo','Instalación','Corrección','Diagnóstico'])->nullable();
            $table->text('diagnostico')->nullable();
            $table->enum('origen_falla', ['Desgaste natural','Mala operación','Otro'])->nullable();
            $table->text('trabajo_realizado')->nullable();
            $table->text('conclusion_servicio')->nullable();

            $table->text('detalle_realizado')->nullable();
            $table->string('firma_usuario',120)->nullable();
            $table->string('firma_tecnico',120)->nullable();
            $table->string('firma_jefe_area',120)->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formato_b');
    }
};
