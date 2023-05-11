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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('serie')->nullable(false);
            $table->string('correlative')->nullable(false);
            $table->date('date_emission')->nullable(false);
            $table->date('date_expiration')->nullable(true);
            $table->string('company')->nullable(false);
            $table->string('company_ruc',11)->nullable(false);
            $table->string('company_phone')->nullable(true);
            $table->string('company_department')->nullable(true);
            $table->string('company_province')->nullable(true);
            $table->string('company_district')->nullable(true);
            $table->string('company_urbanization')->nullable(true);
            $table->string('company_address')->nullable(true);
            $table->string('customer')->nullable(false);
            $table->string('customer_ruc',11)->nullable(false);
            $table->string('customer_phone')->nullable(true);
            // $table->string('customer_phone')->nullable(false);
            $table->string('customer_department')->nullable(true);
            $table->string('customer_province')->nullable(true);
            $table->string('customer_district')->nullable(true);
            $table->string('customer_urbanization')->nullable(true);
            $table->string('customer_address')->nullable(true);
            // $table->string('type_operation')->nullable(false);
            $table->string('payment_method')->nullable(false);
            $table->float('monto_operaciones_agravadas')->default(0.00);
            $table->float('igv')->default(0.00);
            $table->float('total_tributes')->default(0.00);
            $table->float('valor_venta')->default(0.00);
            $table->float('icbper')->default(0.00);
            $table->float('subtotal')->default(0.00);
            $table->float('monto_importe_venta')->default(0.00);
            $table->string('currency')->nullable(false);
            $table->float('advanced')->default(0.00)->nullable();
            $table->float('discount')->default(0.00)->nullable();
            $table->float('isc')->default(0.00)->nullable();
            $table->float('other_charges')->default(0.00)->nullable();
            $table->float('other_tributes')->default(0.00)->nullable();
            $table->string('document_pdf')->nullable(false);
            $table->string('document_xml')->nullable(false);
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
        Schema::dropIfExists('invoices');
    }
};
