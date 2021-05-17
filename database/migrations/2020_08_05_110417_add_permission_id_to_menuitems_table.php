<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionIdToMenuitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menuitems', function (Blueprint $table) {
            $table->integer('user_permission_id')->after('schluessel')->unsigned()->nullable();
            $table->foreign('user_permission_id')->references('id')->on('user_permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menuitems', function (Blueprint $table) {
            $table->dropForeign('menuitems_user_permission_id_foreign');
            $table->dropColumn('user_permission_id');
        });
    }
}
