<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales_utilizados', function (Blueprint $table) {
            $table->id('id_relacion');
            $table->unsignedBigInteger('id_servicio');
            $table->unsignedBigInteger('id_material');
            $table->decimal('cantidad', 10, 2)->nullable();
            $table->decimal('costo_aproximado', 10, 2)->nullable();
            $table->text('descripcion')->nullable();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');
            $table->foreign('id_material')->references('id_material')->on('catalogo_materiales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales_utilizados');
    }
};
