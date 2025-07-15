<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_bathrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('name');
            $table->boolean('private')->default(false);
            $table->enum('bathroom_type', ['En-suite bathroom', 'Full bathroom', 'WC'])->default('Full bathroom');
            $table->enum('toilet', ['No toilet', 'Toilet'])->default('Toilet');
            $table->enum('shower', ['No shower', 'Separate shower', 'Shower over bath'])->default('No shower');
            $table->enum('bath', ['Jacuzzi', 'No bath', 'Standard bath', 'Whirlpool'])->default('No bath');
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_bathrooms');
    }
};

