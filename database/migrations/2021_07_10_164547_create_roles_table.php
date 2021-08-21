<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        DB::table('roles')->insert([
           [
               'name' => 'admin',
               'slug' => 'admin',
               'created_at' => now(),
               'updated_at' => now()
           ],
            [
                'name' => 'user',
                'slug' => 'user',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'data entry',
                'slug' => 'de',
                'created_at' => now(),
                'updated_at' => now()

            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
