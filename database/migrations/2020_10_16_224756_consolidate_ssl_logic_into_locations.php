<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ConsolidateSslLogicIntoLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_locations', function (Blueprint $table) {
            $table->unsignedInteger('status_cert_gen')->default(0)->after('sub_category');
            $table->unsignedInteger('status_cert_import')->default(0)->after('status_cert_gen');
            $table->dateTime('last_cert_gen')->nullable()->default(null)->after('status_cert_import');
            $table->dateTime('last_cert_import')->nullable()->default(null)->after('last_cert_gen');
            $table->dateTime('last_cert_gen_touched')->nullable()->default(null)->after('last_cert_import');
            $table->dateTime('last_cert_import_touched')->nullable()->default(null)->after('last_cert_import');
            $table->dateTime('last_ssl_error')->nullable()->default(null)->after('last_cert_import');
            $table->text('last_ssl_error_message')->nullable()->after('last_ssl_error');
            $table->text('ssl_options')->nullable()->after('last_ssl_error_message');
            $table->integer('ssl_count_processed_gen')->default(0)->after('ssl_options');
            $table->integer('ssl_count_processed_import')->default(0)->after('ssl_count_processed_gen');
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
            $table->dropColumn([
                'status_cert_gen',
                'status_cert_import',
                'last_cert_gen',
                'last_cert_import',
                'last_cert_gen_touched',
                'last_cert_import_touched',
                'last_ssl_error',
                'last_ssl_error_message',
                'ssl_options',
                'ssl_count_processed_gen',
                'ssl_count_processed_import'
            ]);
        });
    }
}
