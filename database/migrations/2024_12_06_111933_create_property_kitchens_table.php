<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_kitchens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['Kitchenette', 'Open plan kitchen', 'Outdoor kitchen', 'Separate kitchen']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_kitchens');
    }
};
