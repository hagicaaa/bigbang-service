<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
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
            $table->string('invoice_number');
            $table->unsignedBigInteger('reparation_id');
            $table->integer('total')->nullable();
            $table->string('invoice_pdf_dir')->nullable();
            $table->boolean('payment_status')->nullable();
            $table->boolean('pickup_status')->nullable();
            $table->timestamps();

            $table->foreign('reparation_id')->references('id')->on('reparations');
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
}
