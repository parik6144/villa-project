<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_bathrooms', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->enum('bathroom_type', ['En-suite bathroom', 'Full bathroom', 'WC'])->nullable()->default(null)->change();
            $table->enum('toilet', ['No toilet', 'Toilet'])->nullable()->default(null)->change();
            $table->enum('shower', ['No shower', 'Separate shower', 'Shower over bath'])->nullable()->default(null)->change();
            $table->enum('bath', ['Jacuzzi', 'No bath', 'Standard bath', 'Whirlpool'])->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('property_bathrooms', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->enum('bathroom_type', ['En-suite bathroom', 'Full bathroom', 'WC'])->default('Full bathroom')->nullable(false)->change();
            $table->enum('toilet', ['No toilet', 'Toilet'])->default('Toilet')->nullable(false)->change();
            $table->enum('shower', ['No shower', 'Separate shower', 'Shower over bath'])->default('No shower')->nullable(false)->change();
            $table->enum('bath', ['Jacuzzi', 'No bath', 'Standard bath', 'Whirlpool'])->default('No bath')->nullable(false)->change();
        });
    }
};
