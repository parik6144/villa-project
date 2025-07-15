<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('property_bedrooms', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->enum('type', ['Bedroom', 'Living room', 'Other room'])->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('property_bedrooms', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->enum('type', ['Bedroom', 'Living room', 'Other room'])->nullable(false)->change();
        });
    }
};
