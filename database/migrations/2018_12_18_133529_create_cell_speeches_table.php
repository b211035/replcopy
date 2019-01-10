<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCellSpeechesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cell_speeches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('scenario_cell_id');
            $table->text('text');
            $table->smallInteger('condition')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('scenario_cell_id')->references('id')->on('scenario_cells');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cell_speeches');
    }
}
