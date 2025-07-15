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
        Schema::table('property_availability', function (Blueprint $table) {
            $table->date('date_from')->nullable()->change();
            $table->date('date_to')->nullable()->change();

            if (!Schema::hasColumn('property_availability', 'date_for_sale')) {
                $table->date('date_for_sale')->nullable()->after('date_to');
            }

            if (!Schema::hasColumn('property_availability', 'type')) {
                $table->enum('type', ['sale', 'rent'])->default('rent')->after('property_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_availability', function (Blueprint $table) {
            if (Schema::hasColumn('property_availability', 'date_for_sale')) {
                $table->dropColumn('date_for_sale');
            }
            if (Schema::hasColumn('property_availability', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
