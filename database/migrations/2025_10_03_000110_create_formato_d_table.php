<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formato_d', function (Blueprint $table) {
            $table->id('id_formatoD');
            $table->unsignedBigInteger('id_servicio');

            $table->date('fecha');
            $table->string('equipo',50);
            $table->string('marca',50)->nullable();
            $table->string('modelo',50)->nullable();
            $table->string('serie',50)->nullable();

            $table->string('otorgante',120)->nullable();
            $table->string('receptor',120)->nullable();
            $table->string('firma_jefe_area',120)->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formato_d');
    }
};
