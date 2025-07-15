<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('service_category_id');
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('availability')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('service_category_id')
                ->references('id')
                ->on('service_categories')
                ->onDelete('cascade');

            // Index
            $table->index('service_category_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}
