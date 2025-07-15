<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('property_bedrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('name');
            $table->enum('type', ['Bedroom', 'Living room', 'Other room']);
            $table->tinyInteger('bunk_bed')->default(0);
            $table->tinyInteger('double_bed')->default(0);
            $table->tinyInteger('king_sized_bed')->default(0);
            $table->tinyInteger('queen_sized_bed')->default(0);
            $table->tinyInteger('single_bed_adult')->default(0);
            $table->tinyInteger('single_bed_child')->default(0);
            $table->tinyInteger('sofa_bed_double')->default(0);
            $table->tinyInteger('sofa_bed_single')->default(0);
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_bedrooms');
    }
};
