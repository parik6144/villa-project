<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('property_other_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->boolean('common_area')->default(false);
            $table->boolean('dining_room')->default(false);
            $table->boolean('drying_room')->default(false);
            $table->boolean('eating_area')->default(false);
            $table->boolean('fitness_room')->default(false);
            $table->boolean('games_room')->default(false);
            $table->boolean('hall')->default(false);
            $table->boolean('laundry')->default(false);
            $table->boolean('library')->default(false);
            $table->boolean('living_room')->default(false);
            $table->boolean('lounge')->default(false);
            $table->boolean('office')->default(false);
            $table->boolean('pantry')->default(false);
            $table->boolean('rumpus_room')->default(false);
            $table->boolean('sauna')->default(false);
            $table->boolean('studio')->default(false);
            $table->boolean('study')->default(false);
            $table->boolean('tv_room')->default(false);
            $table->boolean('work_studio')->default(false);
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_other_rooms');
    }
};
