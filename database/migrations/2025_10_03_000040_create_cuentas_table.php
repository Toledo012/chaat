<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id('id_cuenta');
            $table->string('username', 30)->unique();
            $table->string('password');
            $table->enum('estado', ['activo','inactivo'])->default('activo');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_rol');

            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_rol')->references('id_rol')->on('roles')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
