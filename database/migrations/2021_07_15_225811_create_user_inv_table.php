<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_inv', function (Blueprint $table) {
            $table->unsignedBigInteger('inv_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['inv_id', 'user_id']);
            $table->foreign('inv_id')->references('id')->on('invs');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_inv');
    }
}
