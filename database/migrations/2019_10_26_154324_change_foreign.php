<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeign extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('bots', function (Blueprint $table) {
      $table->dropForeign(['project_id']);
      $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
    });
    Schema::table('scenarios', function (Blueprint $table) {
      $table->dropForeign(['bot_id']);
      $table->foreign('bot_id')->references('id')->on('bots')->onDelete('cascade');
    });
    Schema::table('scenario_cells', function (Blueprint $table) {
      $table->dropForeign(['scenario_id']);
      $table->foreign('scenario_id')->references('id')->on('scenarios')->onDelete('cascade');
    });
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign(['bot_id']);
      $table->foreign('bot_id')->references('id')->on('bots')->onDelete('cascade');
    });
    Schema::table('variables', function (Blueprint $table) {
      $table->dropForeign(['bot_id']);
      $table->foreign('bot_id')->references('id')->on('bots')->onDelete('cascade');
    });
    Schema::table('cell_conditions', function (Blueprint $table) {
      $table->dropForeign(['scenario_cell_id']);
      $table->foreign('scenario_cell_id')->references('id')->on('scenario_cells')->onDelete('cascade');
      $table->dropForeign(['variable_id']);
      $table->foreign('variable_id')->references('id')->on('variables')->onDelete('cascade');
    });
    Schema::table('cell_speeches', function (Blueprint $table) {
      $table->dropForeign(['scenario_cell_id']);
      $table->foreign('scenario_cell_id')->references('id')->on('scenario_cells')->onDelete('cascade');
    });
    Schema::table('cell_memories', function (Blueprint $table) {
      $table->dropForeign(['scenario_cell_id']);
      $table->foreign('scenario_cell_id')->references('id')->on('scenario_cells')->onDelete('cascade');
      $table->dropForeign(['variable_id']);
      $table->foreign('variable_id')->references('id')->on('variables')->onDelete('cascade');
    });
    Schema::table('cell_chains', function (Blueprint $table) {
      $table->dropForeign(['prev_cell_id']);
      $table->foreign('prev_cell_id')->references('id')->on('scenario_cells')->onDelete('cascade');
      $table->dropForeign(['next_cell_id']);
      $table->foreign('next_cell_id')->references('id')->on('scenario_cells')->onDelete('cascade');
    });
    Schema::table('dictionaries', function (Blueprint $table) {
      $table->dropForeign(['bot_id']);
      $table->foreign('bot_id')->references('id')->on('bots')->onDelete('cascade');
    });
    Schema::table('words', function (Blueprint $table) {
      $table->dropForeign(['dictionary_id']);
      $table->foreign('dictionary_id')->references('id')->on('dictionaries')->onDelete('cascade');
    });
    Schema::table('progress', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->dropForeign(['scenario_id']);
      $table->foreign('scenario_id')->references('id')->on('scenarios')->onDelete('cascade');
      $table->dropForeign(['scenario_cell_id']);
      $table->foreign('scenario_cell_id')->references('id')->on('scenario_cells')->onDelete('cascade');
    });
    Schema::table('storage', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->dropForeign(['variable_id']);
      $table->foreign('variable_id')->references('id')->on('variables')->onDelete('cascade');
    });

  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
  }
}
