<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveLocationNoteIdAndAddLocationMailIdInLocationMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_mails', function (Blueprint $table) {
            $table->dropColumn(['location_note_id']);
            $table->string('location_mail_id')->after('message_id')->nullable();
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
            $table->dropColumn(['location_mail_id']);
            $table->string('location_note_id')->after('message_id')->nullable();
        });
    }
}
