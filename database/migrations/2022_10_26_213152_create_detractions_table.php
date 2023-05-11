<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detractions', function (Blueprint $table) {
            $table->id();
            $table->string('cod_bien_producto');
            $table->string('cod_medio_pago');
            $table->string('cuenta_banco');
            $table->float('porcentaje');
            $table->float('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detractions');
    }
};
