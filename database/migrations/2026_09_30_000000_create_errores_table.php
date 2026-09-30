<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Fallos del sistema (los registra App\Services\RegistroErroresService).
// Solo crea una tabla nueva: no toca ninguna existente, así que es segura con
// `php artisan migrate` sobre la BD de producción restaurada del dump.
return new class extends Migration {
    public function up(): void
    {
        Schema::create('errores', function (Blueprint $table) {
            $table->id('id_error');

            // sha256 de clase + archivo + línea: el mismo fallo suma ocurrencias en vez de otra fila
            $table->string('huella', 64)->unique();

            $table->string('clase', 190);
            $table->text('mensaje');
            $table->string('archivo', 255)->nullable();
            $table->unsignedInteger('linea')->nullable();

            $table->string('metodo', 10)->nullable();
            $table->string('url', 500)->nullable();   // sin query string
            $table->unsignedBigInteger('id_cuenta')->nullable();   // null: invitado o consola
            $table->string('ip', 45)->nullable();
            $table->string('navegador', 400)->nullable();
            $table->text('traza')->nullable();

            $table->unsignedInteger('ocurrencias')->default(1);
            $table->timestamp('primera_vez')->useCurrent();
            $table->timestamp('ultima_vez')->useCurrent();
            $table->timestamp('avisado_en')->nullable();   // último correo a los admins

            $table->timestamp('atendido_en')->nullable();
            $table->unsignedBigInteger('atendido_por')->nullable();

            $table->index(['atendido_en', 'ultima_vez']);

            $table->foreign('id_cuenta')->references('id_cuenta')->on('cuentas')->nullOnDelete();
            $table->foreign('atendido_por')->references('id_cuenta')->on('cuentas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('errores');
    }
};
