<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyBookingRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_booking_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->boolean('is_free_cancellation'); 
            $table->enum('free_cancellation_period', [
                '18:00 on the day of arrival',
                '14:00 on the day of arrival',
                '1 day before arrival',
                '2 days before arrival',
                '3 days before arrival',
                '5 days before arrival',
                '7 days before arrival',
                '14 days before arrival',
                '21 days before arrival',
                '28 days before arrival',
                '30 days before arrival',
                '42 days before arrival',
                '60 days before arrival',
                'No free cancellation',
            ])->nullable();
            $table->enum('cancellation_fee', [
                'the cost of the first night',
                '50% of the total price',
                '100% of the total price',
            ]); 
            $table->enum('no_show_fee', [
                'same as cancellation fee',
                '100% of the total price',
            ]); 
            $table->enum('rate_adjustment_type', [
                'increase by',
                'decrease by',
            ])->nullable(); 
            $table->decimal('rate_adjustment_value', 8, 2)->nullable(); 
            $table->timestamps(); // created_at & updated_at

            $table->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_booking_rules');
    }
}
