<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCellChainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cell_chains', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('prev_cell_id');
            $table->unsignedInteger('next_cell_id');

            $table->foreign('prev_cell_id')->references('id')->on('scenario_cells');
            $table->foreign('next_cell_id')->references('id')->on('scenario_cells');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cell_chains');
    }
}
