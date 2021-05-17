<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExcludedAppointmentTypeIdToDutyTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('duty_triggers', function (Blueprint $table) {
            $table->integer('excludedAppointmentTypeId')->unsigned()->nullable();
            $table->foreign('excludedAppointmentTypeId')->references('id')->on('appointment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('duty_triggers', function (Blueprint $table) {
            $table->dropForeign('excludedAppointmentTypeId');
            $table->dropColumn('excludedAppointmentTypeId');
        });
    }
}
