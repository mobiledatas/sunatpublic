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
        Schema::create('invoice_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_invoice')->references('id')->on('invoices');
            $table->float('amount')->nullable(false);
            $table->string('currency')->nullable(false);
            $table->date('payment_date')->nullable(false);
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
        Schema::dropIfExists('invoice_quotes');
    }
};
