<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formato_a', function (Blueprint $table) {
            $table->id('id_formatoA');
            $table->unsignedBigInteger('id_servicio');

            $table->enum('subtipo', ['Desarrollo','Soporte']);
            $table->enum('tipo_atencion', ['Memo','Teléfono','Jefe','Usuario'])->nullable();
            $table->text('peticion')->nullable();
            $table->enum('tipo_servicio', ['Equipos','Redes LAN/WAN','Antivirus','Software'])->nullable();
            $table->enum('trabajo_realizado', ['En sitio','Área de producción','Traslado de equipo'])->nullable();
            $table->enum('conclusion_servicio', ['Terminado','En proceso'])->nullable();

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
        Schema::dropIfExists('formato_a');
    }
};
