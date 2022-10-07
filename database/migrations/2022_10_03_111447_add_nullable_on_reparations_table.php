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
            $table->date('inspection_date')->nullable()->change();
            $table->date('repair_start')->nullable()->change();
            $table->date('repair_finish')->nullable()->change();
            $table->date('post_repair_inspection_date')->nullable()->change();
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
            $table->date('inspection_date')->nullable(false)->change();
            $table->date('repair_start')->nullable(false)->change();
            $table->date('repair_finish')->nullable(false)->change();
            $table->date('post_repair_inspection_date')->nullable(false)->change();
        });
    }
}
