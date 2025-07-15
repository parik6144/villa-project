<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyAttributesTable extends Migration
{
    public function up()
    {
        Schema::create('property_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade'); // Связь с таблицей properties
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade'); // Связь с таблицей attributes
            $table->string('value')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_attributes');
    }
}