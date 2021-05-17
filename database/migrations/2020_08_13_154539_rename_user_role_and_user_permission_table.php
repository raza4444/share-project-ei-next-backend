<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameUserRoleAndUserPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('roles'))
            Schema::rename('user_roles', 'roles');
        
        if (!Schema::hasTable('permissions'))
            Schema::rename('user_permissions', 'permissions');
        

        if (!Schema::hasTable('role_permission'))
            Schema::rename('user_role_user_permission', 'role_permission');

        if (Schema::hasTable('user_user_permission'))
        Schema::rename('user_user_permission', 'user_permission');

        Schema::table('user_permission', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('permission_id')->references('id')->on('permissions');    
         });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign('user_permissions_permission_type_foreign');
            $table->dropColumn('permission_type');
         });

         Schema::dropIfExists('permission_types');    
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
