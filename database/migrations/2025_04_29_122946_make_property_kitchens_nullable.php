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
        Schema::table('property_kitchens', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->enum('type', ['Kitchenette', 'Open plan kitchen', 'Outdoor kitchen', 'Separate kitchen'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('property_kitchens', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->enum('type', ['Kitchenette', 'Open plan kitchen', 'Outdoor kitchen', 'Separate kitchen'])->nullable(false)->change();
        });
    }
};
