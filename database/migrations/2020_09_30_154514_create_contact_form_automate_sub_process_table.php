<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactFormAutomateSubProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_form_automator_sub_process', function (Blueprint $table) {
            $table->increments('id');
            $table->string('contact_form_automator_process_id')->references('id')->on('contact_form_automator_process')->onDelete('set null');
            $table->integer('pid');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_form_automator_sub_process');
    }
}
