<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogFileNameInContactFormAutomatorSubProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_form_automator_sub_process', function (Blueprint $table) {
            $table->string('log_file')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_form_automator_sub_process', function (Blueprint $table) {
            $table->dropColumn('log_file');
        });
    }
}
