<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtHostDomainExtHostFtphostExtHostFtpusernameExtHostFtppasswordAndExtHostFtpdirectoryhtmlToCampaignLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_locations', function (Blueprint $table) {
            $table->string('ext_host_domain')->nullable()->after('ftpdirectoryhtml');
            $table->string('ext_host_ftphost')->nullable()->after('ext_host_domain');
            $table->string('ext_host_ftpusername')->nullable()->after('ext_host_ftphost');
            $table->string('ext_host_ftppassword')->nullable()->after('ext_host_ftpusername');
            $table->string('ext_host_ftpdirectoryhtml')->nullable()->after('ext_host_ftppassword');
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
            $table->dropColumn(['ext_host_domain', 'ext_host_ftphost','ext_host_ftpusername', 'ext_host_ftppassword', 'ext_host_ftpdirectoryhtml']);
        });
    }
}
