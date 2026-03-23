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
        Schema::table('formato_a', function (Blueprint $table)
        {
            $table->string("num_memo", 100)->nullable()->after("tipo_atencion");       });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
