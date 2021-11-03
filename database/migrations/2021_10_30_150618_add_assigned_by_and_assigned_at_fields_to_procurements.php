<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignedByAndAssignedAtFieldsToProcurements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->integer('assigned_by')->unsigned()->nullable();
            $table->timestamp('assigned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn('assigned_by', 'assigned_at');
        });
    }
}
