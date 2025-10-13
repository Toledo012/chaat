<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Permisos', function (Blueprint $table) {
            $table->id('id_permiso');
            $table->string('nombre', 30)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Permisos');
    }
};
