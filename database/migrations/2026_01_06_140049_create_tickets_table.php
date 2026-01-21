<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('id_ticket');

            // de que departamento viene el ticket
            $table->unsignedBigInteger('id_departamento');

            // Datos del solicitante (persona que reporta)
            $table->string('nombre_solicitante', 100);
            $table->string('telefono', 30)->nullable();
            $table->string('correo_solicitante', 120)->nullable();

            // Contenido
            $table->string('asunto', 150);
            $table->text('descripcion');

            // Tipo de atención
            $table->enum('tipo_atencion', [
                'equipo',
                'red_wifi',
                'software_programas',
                'otro'
            ])->default('otro');

            // Quién lo creó (departamento o usuario)
            $table->enum('creado_por_tipo', ['departamento', 'usuario'])->default('departamento');
            $table->unsignedBigInteger('id_usuario_creador')->nullable(); // si lo creó admin/técnico

            // Tomado / asignado a técnico
            $table->unsignedBigInteger('id_tecnico_asignado')->nullable();
            $table->timestamp('tomado_en')->nullable();

            // Solo admin reasigna/asigna
            $table->unsignedBigInteger('asignado_por')->nullable();
            $table->timestamp('asignado_en')->nullable();

            // Estado
            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'en_espera',
                'terminado',
                'cancelado'
            ])->default('pendiente');

            //  Relación con formatos/servicios (si no no se concluye el ticket)
            $table->string('formato_requerido', 10)->nullable(); // A/B/C/D
            $table->unsignedBigInteger('id_servicio')->nullable();
            $table->timestamp('formato_generado_en')->nullable();

            $table->timestamp('cerrado_en')->nullable();

            $table->timestamps();

            /* ======================
               FOREIGN KEYS
            ====================== */

            $table->foreign('id_departamento')
                ->references('id_departamento')
                ->on('departamentos')
                ->cascadeOnDelete();

            $table->foreign('id_usuario_creador')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();

            $table->foreign('id_tecnico_asignado')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();

            $table->foreign('asignado_por')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();

            $table->foreign('id_servicio')
                ->references('id_servicio')
                ->on('servicios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
