<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fname',50);
            $table->string('mname',50)->nullable();
            $table->string('lname',50);
            $table->string('image',50)->nullable();
            $table->string('card',20)->nullable();
            $table->integer('renter_id')->unsigned();
            $table->foreign('renter_id')->references('id')->on('renters');
            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars');
            $table->string('car_num',10)->nullable();
            $table->integer('doc_type_id')->unsigned();
            $table->foreign('doc_type_id')->references('id')->on('doc_types');
            $table->string('doc_series',7);
            $table->string('doc_num',10);
            $table->string('phone',15)->nullable();
            $table->boolean('employee')->default(0);
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
        Schema::dropIfExists('visitors');
    }
}
