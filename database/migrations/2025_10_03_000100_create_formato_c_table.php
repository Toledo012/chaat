<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formato_c', function (Blueprint $table) {
            $table->id('id_formatoC');
            $table->unsignedBigInteger('id_servicio');

            $table->text('descripcion_servicio')->nullable();
            $table->enum('tipo_red', ['Red','Telefonía'])->nullable();
            $table->enum('tipo_servicio', ['Preventivo','Correctivo','Configuración'])->nullable();
            $table->text('diagnostico')->nullable();

            $table->enum('origen_falla', ['Desgaste natural','Mala operación','Otro'])->nullable();
            $table->text('trabajo_realizado')->nullable();
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
        Schema::dropIfExists('formato_c');
    }
};
