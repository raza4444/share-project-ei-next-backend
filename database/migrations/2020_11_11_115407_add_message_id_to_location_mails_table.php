<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessageIdToLocationMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_mails', function (Blueprint $table) {
            $table->string('message_id')->after('location_id')->unique()->nullable();
            $table->string('from')->after('to')->nullable();
            $table->string('to')->nullable()->change();
            $table->unsignedInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_mails', function (Blueprint $table) {
            $table->dropColumn('message_id');
            $table->dropColumn('from');
        });
    }
}
