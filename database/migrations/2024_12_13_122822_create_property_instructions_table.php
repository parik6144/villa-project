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
        Schema::create('property_instructions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();

            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('check_in_contact_person')->nullable();
            $table->enum('key_collection_point', [
                'At the property',
                'From another location',
                'Key code entrance',
                'Keys are in a lock box',
                'There is a reception'
            ])->nullable();
            $table->string('telephone_number')->nullable();

            $table->text('check_in_instructions')->nullable();
            $table->json('attached_instructions')->nullable();

            $table->json('closest_airports')->nullable();

            $table->text('directions')->nullable();

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
        Schema::dropIfExists('property_instructions');
    }
};
