<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactFormAutomatorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_form_automator_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message', 4096);
            $table->integer('process_id')->unsigned();
            $table->integer('sub_process_id')->unsigned();
            $table->timestamps();

            $table->foreign('sub_process_id')->references('id')->on('contact_form_automator_sub_process')->onDelete('cascade');
            $table->foreign('process_id')->references('id')->on('contact_form_automator_process')->onDelete('cascade');
        });

        Schema::table('contact_form_automator_sub_process', function (Blueprint $table) {
            $table->dropColumn('log_file');
            $table->string('log_number')->unique()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_form_automator_logs', function (Blueprint $table) {
            $table->dropForeign(['sub_process_id']);
            $table->dropForeign(['process_id']);
        });

        Schema::table('contact_form_automator_sub_process', function (Blueprint $table) {
            $table->string('log_file')->nullable()->after('status');
            $table->dropColumn('log_number');
        });
        Schema::dropIfExists('contact_form_automator_logs');
    }
}
