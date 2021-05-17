<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationMailIdToLocationsNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations_notes', function (Blueprint $table) {
            $table->unsignedInteger('location_mail_id')->nullable()->after('pinned');

            $table->foreign('location_mail_id')->references('id')->on('location_mails')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations_notes', function (Blueprint $table) {
            $table->dropForeign(['location_mail_id']);
            $table->dropColumn('location_mail_id');
        });
    }
}
