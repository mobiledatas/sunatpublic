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
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_invoice')->references('id')->on('invoices');
            $table->integer('quantity')->nullable(false);
            $table->string('measurement_unit')->nullable(false);
            $table->float('unit_value')->nullable(false);
            $table->longText('description')->nullable(false);
            $table->float('icbper')->default(0.00);
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
        Schema::dropIfExists('invoice_lines');
    }
};
