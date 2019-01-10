<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCellConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cell_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('scenario_cell_id');
            $table->unsignedInteger('variable_id');
            $table->smallInteger('condition');
            $table->string('condition_value', 100)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('scenario_cell_id')->references('id')->on('scenario_cells');
            $table->foreign('variable_id')->references('id')->on('variables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cell_conditions');
    }
}
