<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procurement_models', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('procurement_id')->unsigned();
            $table->integer('model_id')->unsigned();
            $table->integer('qty')->unsigned();
            $table->decimal('purchase_cost', 20, 2);
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
        Schema::dropIfExists('procurement_models');
    }
}
