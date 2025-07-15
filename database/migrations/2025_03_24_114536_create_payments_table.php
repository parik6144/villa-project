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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id')->unique(); // ID из Planyo
            $table->unsignedBigInteger('planyo_reservation_id')->index();; // связь с reservations
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('EUR');
            $table->string('payment_status')->nullable();
            $table->dateTime('payment_time')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('comment')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('extra_info')->nullable();
            $table->string('uid')->nullable();
            $table->timestamps();

            $table->foreign('planyo_reservation_id')->references('planyo_reservation_id')->on('reservations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
