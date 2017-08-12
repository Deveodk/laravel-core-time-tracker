<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreTimeTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_time_tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('user_agent')->nullable();
            $table->float('time');
            $table->string('page');
            $table->string('origin');
            $table->ipAddress("ip")->nullable();
            $table->unsignedInteger('tracked_id')->nullable();
            $table->string('tracked_type')->nullable();
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
        Schema::drop('core_timetracks');
    }
}
