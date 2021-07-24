<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_time');
            $table->integer('duration')->nullable();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('course_id');
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('course_id')->references('id')->on('courses');
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
        Schema::dropIfExists('invs');
    }
}
