<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScenarioCellsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenario_cells', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('scenario_id');
            $table->smallInteger('system');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('scenario_id')->references('id')->on('scenarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scenarios');
    }
}
