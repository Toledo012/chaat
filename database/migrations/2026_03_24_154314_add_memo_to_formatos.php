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
        Schema::table('formato_b', function (Blueprint $table) {
                $table->string("tipo_atencion", 100)->nullable();

                $table->string("num_memo", 100)->nullable()->after("tipo_atencion");
            });

        Schema::table('formato_c', function (Blueprint $table) {
           $table->string("tipo_atencion", 100)->nullable();
           $table->string("num_memo", 100)->nullable()->after("tipo_atencion");

        });

        {
            Schema::table('formato_d', function (Blueprint $table) {
                $table->string("tipo_atencion", 100)->nullable();
                $table->string("num_memo", 100)->nullable()->after("tipo_atencion");
            });
        }
    }


    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('formato_b', function (Blueprint $table) {
            //
        });
        Schema::table('formato_c', function (Blueprint $table) {
            //
        });
        Schema::table('formato_d', function (Blueprint $table) {
            //
        });



    }

};
