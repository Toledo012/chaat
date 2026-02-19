<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('id_departamento')->nullable()->after('id_servicio');
            $table->foreign('id_departamento')
                ->references('id_departamento')
                ->on('departamentos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['id_departamento']);
            $table->dropColumn('id_departamento');
        });
    }
};
