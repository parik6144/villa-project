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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('property_class', [
                'residential',
                'commercial',
                'land',
                'other'
            ])->nullable();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->nullOnDelete();
            $table->string('property_type_custom')->nullable();
            $table->foreignId('basic_rate_commission_id')->nullable()->constrained('basic_rate_commissions')->nullOnDelete();
            $table->enum('approval_status', ['pending', 'approved', 'declined'])->default('pending');
            $table->boolean('active')->default(true);
            $table->string('title')->nullable();
            $table->boolean('deal_type_rent')->default(false);
            $table->boolean('deal_type_monthly_rent')->default(false);
            $table->boolean('deal_type_sale')->default(false);
            $table->decimal('floorspace', 8, 2)->nullable();
            $table->enum('floorspace_units', ['m2', 'ft2'])->nullable();
            $table->decimal('grounds', 8, 2)->nullable();
            $table->enum('grounds_units', ['m2', 'ft2'])->nullable();
            $table->integer('floors_in_building')->nullable();
            $table->integer('floors_of_property')->nullable();
            $table->enum('entrance', [
                'Secured',    // Common with security
                'Unsecured',  // Common without security
                'Private'     // Private
            ])->nullable();
            $table->foreignId('rental_licence_type_id')->nullable()->constrained('licence_types')->nullOnDelete();
            $table->string('rental_licence_number')->nullable();
	    
	    // Real estate details	    
	    $table->decimal('kitchen_area_size', 5, 2)->nullable();
	    $table->enum('kitchen_area_units', ['m2', 'ft2'])->nullable();
	    $table->decimal('living_area_size', 5, 2)->nullable();
	    $table->enum('living_area_units', ['m2', 'ft2'])->nullable();
	    $table->string('heating_features')->nullable();
	    $table->json('aditional_features')->nullable();
	    $table->json('suitable_for')->nullable();
	    $table->string('suitable_for_custom')->nullable();
	    $table->decimal('common_expences', 5, 2)->nullable();
	    $table->integer('year_of_construction')->nullable();	    
	    $table->integer('year_of_renovation')->nullable();
	    $table->decimal('price_for_sale_eur', 10, 2)->nullable();
	    $table->decimal('price_for_sale_per_sq_m', 6, 2)->nullable();
	    $table->decimal('return_on_investment', 5, 2)->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('country')->nullable();
	    $table->string('street')->nullable();
            $table->text('address')->nullable();
            $table->text('apartment_floor_building')->nullable();
            $table->string('city')->nullable();
            $table->string('state_or_region')->nullable();
            $table->string('postal_code', 6)->nullable();
            $table->enum('orientation', [
                'East',
                'East West',
                'East meridian',
                'North',
                'North east',
                'North west',
                'West',
                'West meridian',
                'Meridian',
                'South',
                'South east',
                'South west',
            ])->nullable();
	    
	    $table->string('commercial_title')->nullable();
            $table->string('headline')->nullable();
            $table->text('short_summary')->nullable();
            $table->text('description')->nullable();
            $table->text('brief_description')->nullable();

            $table->enum('suitable_for_kids', [
                'welcome',
                'great',
                'not_suitable'
            ])->nullable();
            $table->boolean('events_allowed')->default(false);

            $table->boolean('pets')->default(false);
            $table->integer('max_pets')->nullable()->unsigned();
            $table->boolean('pets_fee')->default(false);

            $table->boolean('wheelchair_access')->default(false);
            $table->enum('smoking_allowed', [
                'no_smoking',
                'allowed',
                'outside'
            ])->nullable();
            $table->enum('camera', [
                'inside',
                'no',
                'outside'
            ])->nullable();
            $table->boolean('noise_monitor')->default(false);
            $table->text('house_rules')->nullable();

            // "Nightly rates" group
            $table->decimal('basic_night_net', 8, 2)->nullable();
            $table->decimal('basic_night_gross', 8, 2)->nullable();
            $table->decimal('weekend_night_net', 8, 2)->nullable();
            $table->decimal('weekend_night_gross', 8, 2)->nullable();
            $table->decimal('monthly_rate', 8, 2)->nullable();
            $table->decimal('monthly_rate_sqm', 8, 2)->nullable();

            // "Basic rules" group
            $table->integer('max_guests')->nullable();
            $table->integer('min_stay_nights')->nullable();
            $table->integer('max_stay_nights')->nullable();

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

            $table->boolean('is_cleaning')->default(false);

            $table->timestamps();

            //booking rules
            $table->enum('advance_booking_notice', [
                'no_notice',
                '1_day',
                '2_days',
                '3_days',
                '5_days',
                '7_days'
            ])->nullable();

            $table->enum('cancellation_policy', [
                'free_7_days',
                'free_14_days',
                'free_30_days',
                'super_flexible',
                'flexible',
                'moderate',
                'strict',
                'non_refundable',
            ])->nullable();

            $table->enum('additional_policy', [
                'free_7_days',
                'free_14_days',
                'free_30_days',
                'super_flexible',
                'flexible',
                'moderate',
                'strict',
                'non_refundable',
            ])->nullable();
            $table->decimal('rates_increase', 5, 2)->nullable(); 
            $table->decimal('rates_decrease', 5, 2)->nullable();

            $table->enum('additional_policy_2', [
                'free_7_days',
                'free_14_days',
                'free_30_days',
                'super_flexible',
                'flexible',
                'moderate',
                'strict',
                'non_refundable',
            ])->nullable();
            $table->decimal('rates_increase_2', 5, 2)->nullable();
            $table->decimal('rates_decrease_2', 5, 2)->nullable();
            $table->string('allow_external_ical')->nullable();
            $table->string('export_ical_url')->nullable();
            $table->integer('planyo_resource_id')->nullable();

            $table->index('user_id');
            $table->index('property_type_id');
            $table->index('country');
	    
	    $table->string('url_for_site_presentation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
