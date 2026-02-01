<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id_ticket');

            $table->string('folio', 30)->unique()->nullable(); // Folio único, puede ser nulo al inicio
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();

            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->enum('estado', ['nuevo', 'asignado', 'en_proceso', 'en_espera', 'completado', 'cancelado'])->default('nuevo');

            // Quién lo creó
            $table->unsignedBigInteger('creado_por'); // cuentas.id_cuenta

            // Asignación (a quién se le asignó)
            $table->unsignedBigInteger('asignado_a')->nullable();  // cuentas.id_cuenta

            // Quién asignó (admin o usuario cuando se auto-toma)
            $table->unsignedBigInteger('asignado_por')->nullable(); // cuentas.id_cuenta

            $table->timestamps();

            // FKs
            $table->foreign('creado_por')->references('id_cuenta')->on('cuentas')->cascadeOnDelete();
            $table->foreign('asignado_a')->references('id_cuenta')->on('cuentas')->nullOnDelete();
            $table->foreign('asignado_por')->references('id_cuenta')->on('cuentas')->nullOnDelete();

            // Índices útiles
            $table->index(['estado']);
            $table->index(['prioridad']);
            $table->index(['creado_por']);
            $table->index(['asignado_a']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
