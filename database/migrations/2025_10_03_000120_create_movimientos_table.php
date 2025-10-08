<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id('id_movimiento');
            $table->string('tabla',100);
            $table->enum('accion', ['INSERT','UPDATE','DELETE']);
            $table->unsignedBigInteger('id_registro');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->unsignedBigInteger('id_cuenta')->nullable();
            $table->timestamp('fecha')->useCurrent();

            $table->foreign('id_cuenta')->references('id_cuenta')->on('cuentas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
