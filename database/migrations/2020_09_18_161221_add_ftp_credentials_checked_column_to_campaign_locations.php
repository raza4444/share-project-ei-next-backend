<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFtpCredentialsCheckedColumnToCampaignLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_locations', function (Blueprint $table) {
            $table->boolean('ftp_credentials_checked')->after('ssl_origin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_locations', function (Blueprint $table) {
            $table->dropColumn('ftp_credentials_checked');
        });
    }
}
