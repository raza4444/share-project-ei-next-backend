<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('user_permissions', function (Blueprint $table) {
            $table->unsignedInteger('permission_type')->nullable();
            $table->foreign('permission_type')->references('id')->on('permission_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_permissions', function (Blueprint $table) {

            $table->dropForeign('user_permissions_permission_type_foreign');
            $table->dropColumn('permission_type');

        });
        Schema::dropIfExists('permission_types');
    }
}
