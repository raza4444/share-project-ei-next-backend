<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsExtHostedToCampaignLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_locations', function (Blueprint $table) {
            $table->boolean('is_ext_hosted')->after('ext_host_ftpdirectoryhtml');
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
            $table->dropColumn('is_ext_hosted');
        });
    }
}
