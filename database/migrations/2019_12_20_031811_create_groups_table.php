<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('groups', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('bot_id');
      $table->string('key', 36);

      $table->timestamps();
    });

    Schema::table('progress', function (Blueprint $table) {
      $table->unsignedInteger('group_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('progress', function (Blueprint $table) {
      $table->dropColumn('group_id');
    });
    Schema::dropIfExists('groups');
  }
}
