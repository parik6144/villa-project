<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('property_extras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('extra_service');
            $table->enum('fee_basis', [
                'free',
                'per-unit-day',
                'per-day',
                'per-person-week',
                'per-unit',
                'per-person-day',
                'per-week',
                'per-person',
                'per-unit-week'
            ]);
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('earliest_order', [
                'at-the-time-of-booking',
                '2-days-before-check-in',
                '1-day-before-check-in',
                '2-days-before-check-out',
                '1-day-before-check-out'
            ]);
            $table->enum('latest_order', [
                '2-days-before-check-in',
                '1-day-before-check-in',
                '2-days-before-check-out',
                '1-day-before-check-out',
                'no-restriction'
            ]);
            $table->text('additional_info')->nullable();
            $table->timestamps();
            $table->boolean('is_custom')->default(false);
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();

        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_extras');
    }
};
