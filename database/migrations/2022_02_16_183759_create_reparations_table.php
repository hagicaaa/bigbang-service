<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReparationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reparations', function (Blueprint $table) {
            $table->id();
            $table->string('inv_id');
            $table->unsignedBigInteger('computer_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('inspection_date');
            $table->date('repair_start');
            $table->date('post_repair_inspection_date');
            $table->date('repair_finish');
            $table->date('paid_at')->nullable();
            $table->unsignedBigInteger('received_by');
            $table->timestamps();

            $table->foreign('computer_id')->references('id')->on('computers');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('received_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reparations');
    }
}
