<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_mails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('to');
            $table->string('subject');
            $table->string('content');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('location_id');
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('campaign_locations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_mails');
    }
}
