<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('value')->default(false);
            $table->timestamps();
        });

        DB::table('settings')->insert(
            [
                [
                    'name' => 'manual_selection',
                    'value' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'show_details',
                    'value' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
