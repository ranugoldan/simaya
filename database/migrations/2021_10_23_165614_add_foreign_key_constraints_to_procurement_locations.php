<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyConstraintsToProcurementLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procurement_locations', function (Blueprint $table) {
            $table->foreign('procurement_id')->references('id')->on('procurements')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('procurement_locations', function (Blueprint $table) {
            $table->dropForeign(['procurement_id']);
            $table->dropForeign(['location_id']);
        });
    }
}
