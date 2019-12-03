<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndex extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('scenario_cells', function (Blueprint $table) {
      $table->integer('svg_index');
    });
    Schema::table('cell_chains', function (Blueprint $table) {
      $table->integer('svg_index');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('scenario_cells', function (Blueprint $table) {
      $table->dropColumn('svg_index');
    });
    Schema::table('cell_chains', function (Blueprint $table) {
      $table->dropColumn('svg_index');
    });
  }
}
