<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Leyenda anual del encabezado de los formatos ("2025, Año de ...").
// Solo crea una tabla nueva: segura con `php artisan migrate` sobre la BD restaurada del dump.
return new class extends Migration {
    public function up(): void
    {
        Schema::create('leyendas', function (Blueprint $table) {
            $table->id('id_leyenda');
            $table->unsignedSmallInteger('anio')->unique();
            $table->string('texto', 255);
            $table->timestamps();
        });

        // La que estaba fija en los blades, para que los formatos no cambien al desplegar
        DB::table('leyendas')->insert([
            'anio'       => 2025,
            'texto'      => '2025, Año de Rosario Castellanos Figueroa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('leyendas');
    }
};
