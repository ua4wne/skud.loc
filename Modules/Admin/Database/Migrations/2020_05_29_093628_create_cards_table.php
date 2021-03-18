<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',20)->unique();
            $table->boolean('granted')->default(0);
            $table->tinyInteger('flags')->nullable();
            $table->integer('time_zone_id')->unsigned();
            $table->foreign('time_zone_id')->references('id')->on('time_zones');
            $table->boolean('share')->default();
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
        Schema::dropIfExists('cards');
    }
}
