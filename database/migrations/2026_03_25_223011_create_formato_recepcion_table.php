<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('formato_recepcion', function (Blueprint $table) {
            $table->id('id_formato_r');
            $table->unsignedBigInteger('id_servicio');

            $table->text('descripcion')->nullable();


            $table->string('firma_usuario',120)->nullable();
            $table->string('firma_tecnico',120)->nullable();

            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formato_recepcion');
    }
};
