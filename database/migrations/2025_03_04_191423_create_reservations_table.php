<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planyo_reservation_id');
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('client_id');
            $table->string('cart_id')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('status');
            $table->integer('quantity')->default(1);
            $table->boolean('wants_share')->default(false);
            $table->dateTime('creation_time');
            $table->string('unit_assignment')->nullable();
            $table->string('custom_color')->nullable();
            $table->unsignedBigInteger('site_id');
            $table->string('name');
            $table->string('currency', 10);
            $table->boolean('night_reservation')->default(false);
            $table->text('user_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->string('country', 10)->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('ppp_rs')->nullable();
            $table->text('user_text')->nullable();
            $table->json('properties')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->json('log_events')->nullable();
            $table->json('notifications_sent')->nullable();
            $table->string('creation_website')->nullable();
            $table->json('regular_products')->nullable();
            $table->json('custom_products')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
