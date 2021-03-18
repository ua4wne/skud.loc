<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type',10);
            $table->string('snum',10)->unique();
            $table->string('fware',10)->nullable();
            $table->string('conn_fw',10)->nullable();
            $table->string('image',50)->nullable();
            $table->string('text',255)->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('mode')->default(0);
            $table->integer('time_zone_id')->unsigned();
            $table->foreign('time_zone_id')->references('id')->on('time_zones');
            $table->string('address',15);
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
        Schema::dropIfExists('devices');
    }
}
