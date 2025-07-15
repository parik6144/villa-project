<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_settings', function (Blueprint $table) {
            $table->integer('async_period_minutes')->default(5);
        });
    }

    public function down(): void
    {
        Schema::table('property_settings', function (Blueprint $table) {
            $table->dropColumn('async_period_minutes');
        });
    }
};
