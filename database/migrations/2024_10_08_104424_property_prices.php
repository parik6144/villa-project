<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_id');
            $table->decimal('value', 10, 2);
            $table->unsignedBigInteger('property_id');
            $table->timestamps();

            $table->index('price_id');
            $table->index('property_id');

            $table->foreign('price_id')->references('id')->on('price_types')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_prices');
    }
};

