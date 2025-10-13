<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Catalogo_Materiales', function (Blueprint $table) {
            $table->id('id_material');
            $table->string('nombre', 50);
            $table->string('unidad_sugerida', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Catalogo_Materiales');
    }
};
