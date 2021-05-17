<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRoleUserPermissionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_role_user_permission', function (Blueprint $table) {
      $table->unsignedInteger('user_role_id')->index();
      $table->foreign('user_role_id')->references('id')->on('user_roles')->onDelete('cascade');
      $table->unsignedInteger('user_permission_id');
      $table->foreign('user_permission_id')->references('id')->on('user_permissions')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('user_role_user_permission');
  }
}
