<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLegalCellNumberCheckedToCrawlerDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crawler_data', function (Blueprint $table) {
            $table->boolean('legal_cell_number_checked')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crawler_data', function (Blueprint $table) {
            $table->dropIfExists(['legal_cell_number_checked']);
        });
    }
}
