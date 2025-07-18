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
        Schema::table('property_sync', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id')->after('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_sync', function (Blueprint $table) {
            $table->dropColumn('site_id');
        });
    }
};
