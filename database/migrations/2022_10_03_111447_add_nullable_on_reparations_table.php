<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableOnReparationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->date('inspection_date')->nullable();
            $table->date('repair_start')->nullable();
            $table->date('repair_finish')->nullable();
            $table->date('post_repair_inspection_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->date('inspection_date')->nullable(false);
            $table->date('repair_start')->nullable(false);
            $table->date('repair_finish')->nullable(false);
            $table->date('post_repair_inspection_date')->nullable(false);
        });
    }
}
