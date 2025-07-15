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
        Schema::create('property_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('season_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('custom_season_name')->nullable();
            $table->date('date_from');
            $table->date('date_to');
            $table->decimal('season_basic_night_net', 6, 2);
            $table->decimal('season_basic_night_gross', 6, 2);
            $table->decimal('season_weekend_night_net', 6, 2)->nullable();
            $table->decimal('season_weekend_night_gross', 6, 2)->nullable();
            $table->boolean('discount')->default(false);
            $table->integer('min_stay_nights');
            $table->integer('max_stay_nights');
            // check-in
            $table->boolean('checkin_mon')->default(false);
            $table->boolean('checkin_tue')->default(false);
            $table->boolean('checkin_wed')->default(false);
            $table->boolean('checkin_thu')->default(false);
            $table->boolean('checkin_fri')->default(false);
            $table->boolean('checkin_sat')->default(false);
            $table->boolean('checkin_sun')->default(false);
            $table->boolean('checkin_any')->default(false);

            // check-out
            $table->boolean('checkout_mon')->default(false);
            $table->boolean('checkout_tue')->default(false);
            $table->boolean('checkout_wed')->default(false);
            $table->boolean('checkout_thu')->default(false);
            $table->boolean('checkout_fri')->default(false);
            $table->boolean('checkout_sat')->default(false);
            $table->boolean('checkout_sun')->default(false);
            $table->boolean('checkout_any')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_seasons');
    }
};
